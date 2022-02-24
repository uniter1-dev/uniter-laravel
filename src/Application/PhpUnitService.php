<?php

namespace PhpUniter\PackageLaravel\Application;

use Exception;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;
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

    public function process(LocalFile $file): bool
    {
        try {
            $phpUnitTest = $this->phpUniterIntegration->generatePhpUnitTest($file);
            $this->testPlacer->place($phpUnitTest);
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    public function fakeProcess(LocalFile $file, string $srcPath): bool
    {
        try {
            //$phpUnitTest = $this->phpUniterIntegration->generatePhpUnitTest($file);
            $phpUnitTest = new PhpUnitTest(
                $file,
                $file->getFileBody(),
                [],
            );
            $this->testPlacer->place($phpUnitTest, $srcPath);
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }
}
