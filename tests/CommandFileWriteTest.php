<?php

namespace PhpUniter\PackageLaravel\Tests;

use Illuminate\Foundation\Testing\TestCase;

class CommandFileWriteTest extends TestCase
{
    use CreatesApplicationPackageLaravel;

    public $container = [];

    public function testIsWritable()
    {
        $pathToTest = config('php-uniter.unitTestsDirectory');
        $basePath = base_path();
        self::assertIsWritable($basePath.'/'.$pathToTest);
    }
}
