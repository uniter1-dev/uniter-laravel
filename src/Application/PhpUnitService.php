<?php

namespace PhpUniter\PackageLaravel\Application;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\PhpUniter\Generator;
use \Exception;

/**
 * Class PhpUnitService
 */
class PhpUnitService
{
    private Placer $testPlacer;
    private Generator $testGenerator;
    private Obfuscator $obfuscatorService;

    public function __construct(Generator $testGenerator, Obfuscator $obfuscatorService, Placer $testPlacer)
    {
        $this->testGenerator = $testGenerator;
        $this->obfuscatorService = $obfuscatorService;
        $this->testPlacer = $testPlacer;
    }

    public function process(LocalFile $file): bool
    {
        try {
            [$obfuscated, $map] = $this->obfuscatorService->obfuscate($file->getFileBody());
            $obfuscatedPhpUnitTest = $this->testGenerator->generate($obfuscated->getBody());
            $phpUnitTest = $this->obfuscatorService->deObfuscate($obfuscatedPhpUnitTest, $map);

            $this->testPlacer->place($phpUnitTest);
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }
}