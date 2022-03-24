<?php

namespace PhpUniter\PackageLaravel\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;
use PhpUniter\PackageLaravel\Application\Obfuscator\KeyGenerator\StableMaker;
use PhpUniter\PackageLaravel\Application\PhpUnitService;
use PhpUniter\PackageLaravel\Application\Placer;
use PhpUniter\PackageLaravel\Infrastructure\Integrations\PhpUniterIntegration;
use PhpUniter\PackageLaravel\Infrastructure\Repository\FakeRepository;
use PhpUniter\PackageLaravel\Infrastructure\Repository\FileRepoInterface;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateClient;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateRequest;

class CommandPackageMockTest extends TestCase
{
    use CreatesApplicationPackageLaravel;

    public function testCommand()
    {
        $this->app->bind(FileRepoInterface::class, FakeRepository::class);
        $fakeRepository = new FakeRepository();
        $this->app->bind(PhpUnitService::class, function (Application $app) use ($fakeRepository) {
            return new PhpUnitService($app->make(PhpUniterIntegration::class),
                new Placer($fakeRepository),
                new StableMaker());
        });

        $this->app->bind(PhpUniterIntegration::class, function (Application $app) {
            $body = json_encode([
                'test'  => '<?php abstract class RealTest extends NewMockery {}; ?>',
                'code'  => 200,
                'stats' => ['1', '2'],
                'log'   => 'warnings list',
                'class' => 'Real',
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
            'filePath'          => 'resources/tests/simple.php',
            '--base_test_class' => 'NewMockery',
        ])->execute();

        self::assertEquals(0, $res);
    }

    public function testFail()
    {
        $this->app->bind(PhpUniterIntegration::class, function (Application $app) {
            $mock = new MockHandler([
                new Response(500, [], ''),
            ]);

            $handlerStack = HandlerStack::create($mock);
            $client = new GenerateClient(['handler' => $handlerStack]);

            return new PhpUniterIntegration(
                $client,
                $app->make(GenerateRequest::class)
            );
        });

        $res = $this->artisan('php-uniter:generate', [
            'filePath'          => 'resources/tests/simple.php',
            '--base_test_class' => 'NewMockery',
        ])->execute();

        self::assertEquals(1, $res);
    }
}
