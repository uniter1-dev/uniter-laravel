<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

use PhpUniter\PackageLaravel\Application\File\Exception\DirectoryPathWrong;
use PhpUniter\PackageLaravel\Application\File\Exception\FileNotAccessed;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;

class UnitTestRepository implements UnitTestRepositoryInterface
{
    private string $baseUnitTestsDirectory;

    public function __construct(string $baseUnitTestsDirectory)
    {
        $this->baseUnitTestsDirectory = $baseUnitTestsDirectory;
    }

    /**
     * @throws DirectoryPathWrong
     * @throws FileNotAccessed
     */
    public function saveOne(PhpUnitTest $phpUnitTest, string $className): int
    {
        $pathToTest = $this->makePath($phpUnitTest, $className);

        $testDir = dirname($pathToTest);
        $touch = $this->touchDir($testDir);

        if (!$touch) {
            throw new DirectoryPathWrong("Directory $testDir cannot be created");
        }

        $phpUnitTest->setPathToTest($pathToTest);
        if ($size = file_put_contents($pathToTest, $phpUnitTest->getFinalUnitTest())) {
            return $size;
        }

        throw new FileNotAccessed("File $pathToTest was not saved");
    }

    public function getFile(PhpUnitTest $phpUnitTest, string $className): string
    {
        return file_get_contents($this->makePath($phpUnitTest, $className));
    }

    public function deleteFile(PhpUnitTest $phpUnitTest, string $className): bool
    {
        return unlink($this->makePath($phpUnitTest, $className));
    }

    private function makePath(PhpUnitTest $phpUnitTest, string $className): string
    {
        return $this->baseUnitTestsDirectory.self::getRelativeTestPath($phpUnitTest, $className);
    }

    private static function getRelativeTestPath(PhpUnitTest $phpUnitTest, string $className): string
    {
        return dirname($phpUnitTest->getLocalFile()->getFilePath()).'/'.$className.'Test.php';
    }

    protected function touchDir(string $dirPath): bool
    {
        if (is_dir($dirPath)) {
            return true;
        }

        return mkdir($dirPath, 0777, true);
    }
}
