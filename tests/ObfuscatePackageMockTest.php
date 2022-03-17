<?php

namespace PhpUniter\PackageLaravel\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;
use PhpUniter\PackageLaravel\Infrastructure\Integrations\PhpUniterIntegration;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateClient;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateRequest;

class ObfuscatePackageMockTest extends TestCase
{
    use CreatesApplicationPackageLaravel;
    const FILE = 'BasicTemplateObf';
    public static int $counter = 123456789;

    /**
     * @dataProvider getInputAndExpected
     */
    public function testCommand($input, $expected)
    {
        $this->app->bind(PhpUniterIntegration::class, function (Application $app) use ($expected) {
            $body = json_encode([
                'test'  => $expected,
                'code'  => 200,
                'stats' => ['1', '2'],
                'log'   => 'warnings list',
            ]);

            $mock = new MockHandler([
                new Response(200, ['X-Foo' => 'Bar'], $body),
            ]);

            $handlerStack = HandlerStack::create($mock);
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

        self::assertEquals(0, $res);
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
