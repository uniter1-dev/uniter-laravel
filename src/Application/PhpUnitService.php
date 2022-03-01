<?php

namespace PhpUniter\PackageLaravel\Application;

use Exception;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Infrastructure\Integrations\PhpUniterIntegration;

class PhpUnitService
{
    private Placer $testPlacer;
    private PhpUniterIntegration $phpUniterIntegration;

    public function __construct(PhpUniterIntegration $phpUniterIntegration, Placer $testPlacer)
    {
        $this->phpUniterIntegration = $phpUniterIntegration;
        $this->testPlacer = $testPlacer;
    }

    public function process(LocalFile $file, array $options): bool
    {
        try {
            $phpUnitTest = $this->phpUniterIntegration->generatePhpUnitTest($file, $options);
            $this->testPlacer->place($phpUnitTest);
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

}
