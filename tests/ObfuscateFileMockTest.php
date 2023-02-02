<?php

namespace Uniter1\UniterLaravel\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;
use Uniter1\UniterRequester\Application\Generation\NamespaceGenerator;
use Uniter1\UniterRequester\Application\Generation\UseGenerator;
use Uniter1\UniterRequester\Application\Obfuscator\KeyGenerator\StableMaker;
use Uniter1\UniterRequester\Application\Obfuscator\ObfuscatorFabric;
use Uniter1\UniterRequester\Application\PhpUnitService;
use Uniter1\UniterRequester\Application\Placer;
use Uniter1\UniterRequester\Infrastructure\Integrations\PhpUniterIntegration;
use Uniter1\UniterRequester\Infrastructure\Repository\FakeUnitTestRepository;
use Uniter1\UniterRequester\Infrastructure\Repository\UnitTestRepositoryInterface;
use Uniter1\UniterRequester\Infrastructure\Request\GenerateClient;
use Uniter1\UniterRequester\Infrastructure\Request\GenerateRequest;

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
                new UseGenerator(config('uniter1.helperClass'))
            );
        });

        $this->app->bind(PhpUniterIntegration::class, function (Application $app) use ($obfTest) {
            $body = json_encode([
                'test'       => $obfTest,
                'code'       => 200,
                'stats'      => ['1', '2'],
                'log'        => 'warnings list',
                'class'      => 'Foo',
                'namespace'  => 'Foo\Bar\Application\Barbar\Entity',
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

        $res = $this->artisan('uniter1:generate', [
            'filePath'          => __DIR__.'/Fixtures/SourceClass.php.input',
        ])->execute();

        $deObfuscatedTest = $fakeRepository->getFile('FooTest.php');
        self::actualize(__DIR__.'/Fixtures/Deobfuscated.test.expected', $deObfuscatedTest);

        self::assertEquals(0, $res);
        self::assertEquals($result, $deObfuscatedTest);
    }

    /**
     * @dataProvider getInputAndExpected
     */
    public function testCommandParam($input, $obfExpected, $obfTest, $result, $responseMethods, $replacedResult)
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
                new UseGenerator(config('uniter1.helperClass'))
            );
        });

        $am = explode("\n\n", $responseMethods);
        $arrayMethods = ['test_obf3739311' => $am[0], 'test_obf3739321' => $am[1]];
        $this->app->bind(PhpUniterIntegration::class, function (Application $app) use ($obfTest, $arrayMethods) {
            $body = json_encode([
                'test'            => $obfTest,
                'code'            => 200,
                'stats'           => ['1', '2'],
                'log'             => 'warnings list',
                'class'           => 'Foo',
                'namespace'       => 'Foo\Bar\Application\Barbar\Entity',
                'test_methods'    => $arrayMethods,
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

        $fakeRepository->save($result, 'FooTest.php');

        $res = $this->artisan('uniter1:generate', [
            'filePath'               => __DIR__.'/Fixtures/SourceClass.php.input',
            '--overwrite-one-method' => 'fOne',
        ])->execute();

        $deObfuscatedTest = $fakeRepository->getFile('FooTest.php');
        self::actualize(__DIR__.'/Fixtures/Replaced.test.expected', $deObfuscatedTest);

        self::assertEquals(0, $res);
        self::assertEquals($replacedResult, $deObfuscatedTest);
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
                file_get_contents(__DIR__.'/Fixtures/Obfuscated.methods.responce'),
                file_get_contents(__DIR__.'/Fixtures/Replaced.test.expected'),
            ],
        ];
    }

    public static function actualize(string $path, string $actual, $doIt = false): void
    {
        $dirCurrent = getcwd();
        $fileExists = file_exists('/opt/project/.actualize');
        if ($doIt || $fileExists) {
            $done = self::updateExpected($path, $actual);
        }
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
