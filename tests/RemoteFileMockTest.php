<?php

namespace PhpUniter\PackageLaravel\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;
use PhpUniter\PackageLaravel\Application\Generation\NamespaceGenerator;
use PhpUniter\PackageLaravel\Application\Obfuscator\KeyGenerator\StableMaker;
use PhpUniter\PackageLaravel\Application\PhpUnitService;
use PhpUniter\PackageLaravel\Application\Placer;
use PhpUniter\PackageLaravel\Infrastructure\Integrations\PhpUniterIntegration;
use PhpUniter\PackageLaravel\Infrastructure\Repository\FakeUnitTestRepository;
use PhpUniter\PackageLaravel\Infrastructure\Repository\UnitTestRepositoryInterface;
use Symfony\Component\Console\Exception\RuntimeException;

class RemoteFileMockTest extends TestCase
{
    use CreatesApplicationPackageLaravel;
    public $container = [];

    /**
     * @dataProvider getInputAndExpected
     */
    public function testCommand($input, $obfExpected, $obfTest, $result)
    {
        $this->app->bind(UnitTestRepositoryInterface::class, FakeUnitTestRepository::class);
        $fakeRepository = new FakeUnitTestRepository();
        $this->app->bind(PhpUnitService::class, function (Application $app) use ($fakeRepository) {
            return new PhpUnitService($app->make(PhpUniterIntegration::class),
                new Placer($fakeRepository),
                new StableMaker(),
                $app->make(NamespaceGenerator::class)
            );
        });

        $command = $this->artisan('php-uniter:generate', [
            'filePath' => __DIR__.'/Unit/Application/Obfuscator/Entity/Fixtures/SourceClass.php.input',
        ]);

        try {
            $res = $command
                ->run();
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
        $command->assertExitCode(1);
        self::assertEquals(1, $res);
        //$deObfuscatedTest = $fakeRepository->getFile('FooTest.php');
        //self::assertEquals(Helper::stripSpaces($result), Helper::stripSpaces($deObfuscatedTest));
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
                file_get_contents(__DIR__.'/Unit/Application/Obfuscator/Entity/Fixtures/SourceClass.php.input'),
                file_get_contents(__DIR__.'/Unit/Application/Obfuscator/Entity/Fixtures/ObfuscatedClass.php.expected'),
                file_get_contents(__DIR__.'/Unit/Application/Obfuscator/Entity/Fixtures/Obfuscated.test.input'),
                file_get_contents(__DIR__.'/Unit/Application/Obfuscator/Entity/Fixtures/Deobfuscated.test.expected'),
            ],
        ];
    }

    public static function getResponseBody(array $container)
    {
        $req = current($container)['request'];
        $contents = $req->getBody()->getContents();
        $re = json_decode($contents);

        return $re->class;
    }
}
