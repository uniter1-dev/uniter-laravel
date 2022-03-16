<?php

namespace PhpUniter\PackageLaravel\Application;

use GuzzleHttp\Exception\GuzzleException;
use PhpUniter\PackageLaravel\Application\File\Entity\ClassFile;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\File\Exception\DirectoryPathWrong;
use PhpUniter\PackageLaravel\Application\File\Exception\FileNotAccessed;
use PhpUniter\PackageLaravel\Application\File\Exception\RequestFail;
use PhpUniter\PackageLaravel\Application\Obfuscator\Entity\ObfuscatedClass;
use PhpUniter\PackageLaravel\Application\Obfuscator\Obfuscator;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;
use PhpUniter\PackageLaravel\Infrastructure\Integrations\PhpUniterIntegration;

class PhpUnitService
{
    private Placer $testPlacer;
    private PhpUniterIntegration $phpUniterIntegration;
    /**
     * @var callable
     */
    private $uniqKeyGenerator;

    public function __construct(PhpUniterIntegration $phpUniterIntegration, Placer $testPlacer, callable $uniqKeyGenerator)
    {
        $this->phpUniterIntegration = $phpUniterIntegration;
        $this->testPlacer = $testPlacer;
        $this->uniqKeyGenerator = $uniqKeyGenerator;
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
        $this->uniqKeyGenerator);
        return $data[0];
    }

    /**
     * @throws DirectoryPathWrong
     * @throws FileNotAccessed
     * @throws GuzzleException
     * @throws RequestFail
     */
    public function toProcess(LocalFile $file, array $options, callable $integration, callable $uniqKeyGenerator): array
    {
        $obfuscateble = ClassFile::make($file);
        $obfuscator = new ObfuscatedClass(
            $obfuscateble,
            $uniqKeyGenerator,
            new Obfuscator(),
        );
        $obfuscatedSourceText = $obfuscator->makeObfuscated();
        $phpUnitTest = $integration($obfuscatedSourceText, $options);
        $testObfuscatedGenerated = $phpUnitTest->getUnitTest();
        $deObfuscated = $obfuscator->deObfuscate($testObfuscatedGenerated);
        $this->testPlacer->placeUnitTest($file->getFilePath(), $deObfuscated);

        return [$phpUnitTest, $testObfuscatedGenerated, $obfuscatedSourceText];
    }
}
