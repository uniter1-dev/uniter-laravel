<?php

namespace PhpUniter\PhpUniterLaravel\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;
use PhpUniter\PhpUniterRequester\Application\Generation\NamespaceGenerator;
use PhpUniter\PhpUniterRequester\Application\Generation\UseGenerator;
use PhpUniter\PhpUniterRequester\Application\Obfuscator\KeyGenerator\StableMaker;
use PhpUniter\PhpUniterRequester\Application\Obfuscator\ObfuscatorFabric;
use PhpUniter\PhpUniterRequester\Application\PhpUnitService;
use PhpUniter\PhpUniterRequester\Application\Placer;
use PhpUniter\PhpUniterRequester\Infrastructure\Integrations\PhpUniterIntegration;
use PhpUniter\PhpUniterRequester\Infrastructure\Repository\FakeUnitTestRepository;
use PhpUniter\PhpUniterRequester\Infrastructure\Repository\UnitTestRepositoryInterface;
use PhpUniter\PhpUniterRequester\Infrastructure\Request\GenerateClient;
use PhpUniter\PhpUniterRequester\Infrastructure\Request\GenerateRequest;

class ObfuscateFileMockTest extends TestCase
{
    use CreatesApplicationPackageLaravel;
    public $container = [];

    /**
     * @dataProvider getInputAndExpected
     */
    public function testCommand($input, $obfExpected, $obfTest, $result)
    {
        $this->app->bind(ObfuscatorFabric::class, function (Application $app) {
            return new ObfuscatorFabric();
        });
        $this->app->bind(UnitTestRepositoryInterface::class, FakeUnitTestRepository::class);
        $fakeRepository = new FakeUnitTestRepository();
        $this->app->bind(PhpUnitService::class, function (Application $app) use ($fakeRepository) {
            return new PhpUnitService($app->make(PhpUniterIntegration::class),
                new Placer($fakeRepository),
                new StableMaker(),
                $app->make(NamespaceGenerator::class),
                new UseGenerator(config('php-uniter.helperClass'))
            );
        });

        $this->app->bind(PhpUniterIntegration::class, function (Application $app) use ($obfTest) {
            $body = json_encode([
                'test'  => $obfTest,
                'code'  => 200,
                'stats' => ['1', '2'],
                'log'   => 'warnings list',
                'class' => 'Foo',
            ]);

            $history = Middleware::history($this->container);
            $mock = new MockHandler([
                new Response(200, ['X-Foo' => 'Bar'], $body),
            ]);

            $handlerStack = HandlerStack::create($mock);
            $handlerStack->push($history);
            $client = new GenerateClient(['handler' => $handlerStack]);

            return new PhpUniterIntegration(
                $client,
                $app->make(GenerateRequest::class)
            );
        });

        $res = $this->artisan('php-uniter:generate', [
            'filePath'          => __DIR__.'/Fixtures/SourceClass.php.input',
        ])->execute();

        $deObfuscatedTest = $fakeRepository->getFile('FooTest.php');

        self::assertEquals(0, $res);
        self::assertEquals($result, $deObfuscatedTest);
    }

    public static function getResponseBody(array $container)
    {
        $req = current($container)['request'];
        $contents = $req->getBody()->getContents();
        $re = json_decode($contents);

        return $re->class;
    }

    public function getCases(): array
    {
        return [
            self::getInputAndExpected(),
        ];
    }

    public static function getInputAndExpected(): array
    {
        return [
            [
                file_get_contents(__DIR__.'/Fixtures/SourceClass.php.input'),
                file_get_contents(__DIR__.'/Fixtures/ObfuscatedClass.php.expected'),
                file_get_contents(__DIR__.'/Fixtures/Obfuscated.test.input'),
                file_get_contents(__DIR__.'/Fixtures/Deobfuscated.test.expected'),
            ],
        ];
    }


    public static function updateExpected(string $path, string $actual)
    {
        return file_put_contents($path, $actual);
    }

    public static function remSpaces($text)
    {
        return preg_replace('/\s+/', '', $text);
    }
}
