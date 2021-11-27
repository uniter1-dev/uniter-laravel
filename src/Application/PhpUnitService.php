<?php

namespace PhpUniter\PackageLaravel\Application;

use PhpUniter\PackageLaravel\Application\PhpUniter\TestGenerator;
use PhpUniter\PackageLaravel\Application\Test\TestPlacer;
use \SplFileObject;
use \Exception;

class PhpUnitService
{
    public function __construct(TestGenerator $testGenerator, TestPlacer $testPlacer)
    {
        $this->testGenerator = $testGenerator;
        $this->testPlacer = $testPlacer;
    }

    public function process(SplFileObject $file): bool
    {
        try {
            $test = $this->testGenerator->generate($file);

            $this->placer->place($test);
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }
}