<?php

namespace PhpUniter\PackageLaravel\Application;

use GuzzleHttp\Exception\GuzzleException;
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
     * @throws GuzzleException
     * @throws RequestFail
     * @throws Obfuscator\Exception\ObfuscationFailed
     */
    public function process(LocalFile $file, array $options): phpUnitTest
    {
        return $this->toProcess($file, $options, function (LocalFile $file, array $options) {
            return $this->phpUniterIntegration->generatePhpUnitTest($file, $options);
        });
    }

    /**
     * @throws DirectoryPathWrong
     * @throws FileNotAccessed
     * @throws GuzzleException
     * @throws RequestFail
     * @throws Obfuscator\Exception\ObfuscationFailed
     */

    public function toProcess(LocalFile $file, array $options, callable $integration): PhpUnitTest
    {
        $obfuscateble = ClassFile::make($file);
        $obfuscator = ObfuscatorFabric::getObfuscated($obfuscateble);
        $obfuscatedSourceText = $obfuscator->getObfuscated();
        $phpUnitTest = $integration($obfuscatedSourceText, $options);
        $testText = $phpUnitTest->getUnitTest();
        $obfuscatedTestText = $obfuscator->deObfuscate($testText);
        $this->testPlacer->placeUnitTest($file->getFilePath(), $obfuscatedTestText);

        return $phpUnitTest;
    }
}
