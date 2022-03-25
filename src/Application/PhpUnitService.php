<?php

namespace PhpUniter\PackageLaravel\Application;

use GuzzleHttp\Exception\GuzzleException;
use PhpUniter\PackageLaravel\Application\File\Entity\ClassFile;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\File\Exception\DirectoryPathWrong;
use PhpUniter\PackageLaravel\Application\File\Exception\FileNotAccessed;
use PhpUniter\PackageLaravel\Application\File\Exception\RequestFail;
use PhpUniter\PackageLaravel\Application\Obfuscator\Entity\ObfuscatedClass;
use PhpUniter\PackageLaravel\Application\Obfuscator\KeyGenerator\ObfuscateNameMaker;
use PhpUniter\PackageLaravel\Application\Obfuscator\Obfuscator;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;
use PhpUniter\PackageLaravel\Infrastructure\Integrations\PhpUniterIntegration;

class PhpUnitService
{
    private Placer $testPlacer;
    private PhpUniterIntegration $phpUniterIntegration;
    private ObfuscateNameMaker $keyGenerator;

    /**
     * @var callable
     */
    public function __construct(PhpUniterIntegration $phpUniterIntegration, Placer $testPlacer, ObfuscateNameMaker $keyGenerator)
    {
        $this->phpUniterIntegration = $phpUniterIntegration;
        $this->testPlacer = $testPlacer;
        $this->keyGenerator = $keyGenerator;
    }

    /**
     * @throws DirectoryPathWrong
     * @throws FileNotAccessed
     * @throws GuzzleException
     * @throws RequestFail
     */
    public function process(LocalFile $file, array $options): phpUnitTest
    {
        $data = $this->toProcess($file, $options, function (LocalFile $file, array $options) {
            return $this->phpUniterIntegration->generatePhpUnitTest($file, $options);
        },
        $this->keyGenerator);

        return $data[0];
    }

    /**
     * @throws DirectoryPathWrong
     * @throws FileNotAccessed
     * @throws GuzzleException
     * @throws RequestFail
     */
    public function toProcess(LocalFile $file, array $options, callable $integration, ObfuscateNameMaker $keyGenerator): array
    {
        $obfuscateble = ClassFile::make($file);
        $obfuscator = new ObfuscatedClass(
            $obfuscateble,
            $keyGenerator,
            new Obfuscator(),
        );
        $obfuscatedSourceText = $obfuscator->makeObfuscated();
        $phpUnitTest = $integration($obfuscatedSourceText, $options);
        $testObfuscatedGenerated = $phpUnitTest->getUnitTest();
        $deObfuscated = $obfuscator->deObfuscate($testObfuscatedGenerated);
        $pathToTest = base_path().'/'.config('php-uniter.unitTestsDirectory').'/'.dirname($file->getFilePath()).'/'.$phpUnitTest->getClassName().'.php';
        $this->testPlacer->placeUnitTest($pathToTest, $deObfuscated);

        return [$phpUnitTest, $testObfuscatedGenerated, $obfuscatedSourceText];
    }
}
