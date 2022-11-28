<?php

namespace PhpUniter\PhpUniterLaravel\Tests;

use Illuminate\Foundation\Testing\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;

class GenerateRemoteTest extends TestCase
{
    use CreatesApplicationPackageLaravel;
    public $container = [];

    /**
     * @dataProvider getInputAndExpected
     */
    public function testCommand($input, $expected)
    {
        $command = $this->artisan('php-uniter:generate', [
            'filePath'          => __DIR__.'/Fixtures/SourceClass.php.input',
        ]);
        $this->pathToTest = (string) config('php-uniter.unitTestsDirectory');
        $this->projectRoot = base_path();


        try {
            $command->assertExitCode(1)
                ->run();
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }

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
                file_get_contents(__DIR__.'/Fixtures/Deobfuscated.test.expected'),
            ],
        ];
    }
}
