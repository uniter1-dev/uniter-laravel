<?php

namespace PhpUniter\PackageLaravel\Application;

use PhpUniter\PackageLaravel\Application\PhpUniter\Generator;
use PhpUniter\PackageLaravel\Application\Test\Placer;
use \SplFileObject;
use \Exception;

/**
 * Class PhpUnitService
 */
class PhpUnitService
{
    private Placer $testPlacer;

    private Generator $testGenerator;

    public function __construct(Generator $testGenerator, Placer $testPlacer)
    {
        $this->testGenerator = $testGenerator;
        $this->testPlacer = $testPlacer;
    }

    public function process(SplFileObject $file): bool
    {
        try {
            $phpUnitTest = $this->testGenerator->generate($file);

            $this->testPlacer->place($phpUnitTest);
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }
}