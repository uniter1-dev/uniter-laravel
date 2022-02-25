<?php

namespace PhpUniter\PackageLaravel\Application;

use App\Application\TopAstLayer;
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

    public function process(LocalFile $file, string $srcPath): bool
    {
        try {
            $phpUnitTest = $this->phpUniterIntegration->generatePhpUnitTest($file);
            $this->testPlacer->place($phpUnitTest, $srcPath);
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    public function fakeProcess(LocalFile $file, string $srcPath, array $options): bool
    {
        try {
            $phpUnitTestText = $this->fakeGenerate($file->getFileBody(), $options);
            if (!$phpUnitTestText) {
                return false;
            }
            $phpUnitTest = new PhpUnitTest(
                $file,
                $phpUnitTestText,
                [],
            );
            $this->testPlacer->place($phpUnitTest, $srcPath);
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    public function fakeGenerate(string $fileText, array $options): ?string
    {
        $generator = new TopAstLayer();
        $generator->setOptions($options);

        return $generator->fetch($fileText);
    }
}
