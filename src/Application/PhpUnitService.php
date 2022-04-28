<?php

namespace PhpUniter\PackageLaravel\Application;

use PhpUniter\PackageLaravel\Application\File\Entity\ClassFile;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\File\Exception\ObfucsatorNull;
use PhpUniter\PackageLaravel\Application\Obfuscator\KeyGenerator\ObfuscateNameMaker;
use PhpUniter\PackageLaravel\Application\Obfuscator\ObfuscatorFabric;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;
use PhpUniter\PackageLaravel\Application\PhpUniter\Exception\GeneratedTestEmpty;
use PhpUniter\PackageLaravel\Application\PhpUniter\Exception\LocalFileEmpty;
use PhpUniter\PackageLaravel\Infrastructure\Integrations\PhpUniterIntegration;

class PhpUnitService
{
    private Placer $testPlacer;
    private PhpUniterIntegration $integration;
    private ObfuscateNameMaker $keyGenerator;
    private bool $toObfuscate;

    /**
     * @var callable
     */
    public function __construct(
        PhpUniterIntegration $phpUniterIntegration,
        Placer $testPlacer,
        ObfuscateNameMaker $keyGenerator,
        bool $toObfuscate
    ) {
        $this->integration = $phpUniterIntegration;
        $this->testPlacer = $testPlacer;
        $this->keyGenerator = $keyGenerator;
        $this->toObfuscate = $toObfuscate;
    }

    /**
     * @throws File\Exception\DirectoryPathWrong
     * @throws File\Exception\FileNotAccessed
     * @throws \PhpUniter\PackageLaravel\Application\Obfuscator\Exception\ObfuscationFailed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \PhpUniter\PackageLaravel\Infrastructure\Exception\PhpUnitTestInaccessible
     * @throws GeneratedTestEmpty
     * @throws ObfucsatorNull
     * @throws LocalFileEmpty
     */
    public function process(LocalFile $classFile): PhpUnitTest
    {
        $obfuscated = ClassFile::make($classFile);

        if (is_null($obfuscated)) {
            throw new LocalFileEmpty('Local File is Empty');
        }

        if ($this->toObfuscate) {
            $obfuscator = ObfuscatorFabric::getObfuscated($obfuscated, $this->keyGenerator);

            if (is_null($obfuscator)) {
                throw new ObfucsatorNull('File is not obfuscatable');
            }

            $obfuscatedSourceFile = $obfuscator->makeObfuscated();

            $phpUnitTest = $this->integration->generatePhpUnitTest($obfuscatedSourceFile);
            $testObfuscatedGenerated = $phpUnitTest->getObfuscatedUnitTest();

            $deObfuscated = $obfuscator->deObfuscate($testObfuscatedGenerated);
            $phpUnitTest->setFinalUnitTest($deObfuscated);
        } else {
            $phpUnitTest = $this->integration->generatePhpUnitTest($classFile);
            $phpUnitTest->setFinalUnitTest($phpUnitTest->getObfuscatedUnitTest());
        }

        $className = self::findClassName($classFile);

        $testSize = $this->testPlacer->placeUnitTest($phpUnitTest, $className);

        if (empty($testSize)) {
            throw new GeneratedTestEmpty('Empty test written');
        }

        return $phpUnitTest;
    }

    public static function findClassName(LocalFile $classFile)
    {
        $text = $classFile->getFileBody();
        preg_match('/(?<=class\s)(\w+)/', $text, $matches);

        return $matches[0];
    }
}
