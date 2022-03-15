<?php

namespace PhpUniter\PackageLaravel\Application;

use PhpUniter\PackageLaravel\Application\File\Entity\ClassFile;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\File\Exception\DirectoryPathWrong;
use PhpUniter\PackageLaravel\Application\File\Exception\FileNotAccessed;
use PhpUniter\PackageLaravel\Application\File\Exception\RequestFail;
use PhpUniter\PackageLaravel\Application\Obfuscator\ObfuscatorFabric;
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

    /**
     * @throws DirectoryPathWrong
     * @throws FileNotAccessed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws RequestFail
     * @throws Obfuscator\Exception\ObfuscationFailed
     */
    public function process(LocalFile $file, array $options): PhpUnitTest
    {
        $obfuscateble = ClassFile::make($file);
        $obfuscator = ObfuscatorFabric::getObfuscated($obfuscateble);
        $obfuscatedSourceText = $obfuscator->getObfuscated();
        $phpUnitTest = $this->phpUniterIntegration->generatePhpUnitTest($obfuscatedSourceText, $options);
        $testText = $phpUnitTest->getUnitTest();
        $phpUnitTest->setUnitTest($obfuscator->deObfuscate($testText));
        $this->testPlacer->place($phpUnitTest);

        return $phpUnitTest;
    }
}
