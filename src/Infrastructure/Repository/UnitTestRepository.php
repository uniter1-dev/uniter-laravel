<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

use PhpUniter\PackageLaravel\Application\File\Exception\DirectoryPathWrong;
use PhpUniter\PackageLaravel\Application\File\Exception\FileNotAccessed;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;

class UnitTestRepository implements UnitTestRepositoryInterface
{
    private string $baseUnitTestsDirectory;
    private string $filePath;

    public function __construct(string $baseUnitTestsDirectory)
    {
        $this->baseUnitTestsDirectory = $baseUnitTestsDirectory;
    }

    /**
     * @throws DirectoryPathWrong
     * @throws FileNotAccessed
     */
    public function saveOne(PhpUnitTest $unitTest, string $relativePath, string $className): int
    {
        $pathToTest = $this->makePath(dirname($relativePath), $className);

        $testDir = dirname($pathToTest);
        $touch = $this->touchDir($testDir);

        if (!$touch) {
            throw new DirectoryPathWrong("Directory $testDir cannot be created");
        }

        $unitTest->setPathToTest($pathToTest);
        if ($size = file_put_contents($pathToTest, $unitTest->getFinalUnitTest())) {
            return $size;
        }

        throw new FileNotAccessed("File $pathToTest was not saved");
    }

    public function getFile(PhpUnitTest $phpUnitTest, string $className): string
    {
        return file_get_contents($this->filePath);
    }

    public function makePath(string $relativePath, string $className): string
    {
        $this->filePath = $this->baseUnitTestsDirectory.self::getRelativeTestPath($relativePath, $className);

        return $this->filePath;
    }

    private static function getRelativeTestPath(string $relativePath, string $className): string
    {
        return $relativePath.'/'.$className.'Test.php';
    }

    protected function touchDir(string $dirPath): bool
    {
        if (is_dir($dirPath)) {
            return true;
        }

        return mkdir($dirPath, 0777, true);
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
