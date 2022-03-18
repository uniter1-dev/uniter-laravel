<?php

namespace PhpUniter\PackageLaravel\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;
use PhpUniter\PackageLaravel\Application\Obfuscator\KeyGenerator\StableMaker;
use PhpUniter\PackageLaravel\Application\PhpUnitService;
use PhpUniter\PackageLaravel\Application\Placer;
use PhpUniter\PackageLaravel\Infrastructure\Integrations\PhpUniterIntegration;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateClient;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateRequest;

class ObfuscateMiddleTest extends TestCase
{
    use CreatesApplicationPackageLaravel;
    const FILE = 'BasicTemplateObf';
    public $container = [];

    /**
     * @dataProvider getInputAndExpected
     */
    public function testCommand($input, $expected)
    {

        $this->app->bind(PhpUnitService::class, function (Application $app) {
            return new PhpUnitService($app->make(PhpUniterIntegration::class), $app->make(Placer::class), new StableMaker());
        });

        $this->app->bind(PhpUniterIntegration::class, function (Application $app) use ($expected) {
            $body = json_encode([
                'test'  => $expected,
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
            'filePath'          => 'resources/tests/obftest.php',
            '--base_test_class' => 'NewMockery',
        ])->execute();

        $requestObfuscatedText = self::getRequestBody($this->container);

        self::assertEquals(0, $res);
    }

    public static function getRequestBody(array $container)
    {
        /***  @var Request $req */
        $req = current($container)['request'];
        $re = json_decode($req->getBody()->getContents());

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
                file_get_contents(__DIR__.'/Unit/Application/Obfuscator/Entity/Fixtures/Obfuscated.php.input'),
                file_get_contents(__DIR__.'/Unit/Application/Obfuscator/Entity/Fixtures/Obfuscated.php.expected'),
            ],
        ];
    }

    public static function actualize(string $name, string $actual, $doIt = false, string $dir = '/Cases/'): void
    {
        $dirCurrent = getcwd();
        $fileExists = file_exists('/opt/project/.actualize');
        if ($doIt || $fileExists) {
            $done = self::updateExpected($name, $actual, $dir);
        }
    }

    public static function updateExpected(string $name, string $actual, string $dir = '/Cases/')
    {
        $path = __DIR__.$dir;

        return file_put_contents($path."{$name}.expected", $actual);
    }
}
