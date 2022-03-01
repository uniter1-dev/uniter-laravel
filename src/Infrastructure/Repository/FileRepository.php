<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\File\Exception\FileNotAccessed;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;

class FileRepository
{
    public function findOne(string $filePath): ?LocalFile
    {
        if (is_readable($filePath)) {
            return new LocalFile(
                $filePath,
                file_get_contents($filePath)
            );
        }

        throw new FileNotAccessed("File $filePath was not found");
    }

    public function saveOne(string $unitTestText, string $filePath): bool
    {

        $testDir = dirname($filePath);
        if ($this->touchDir($testDir) && file_put_contents($filePath, $unitTestText)) {
            return true;
        }

        throw new FileNotAccessed("File $filePath was not found");
    }

    private function touchDir(string $dirPath)
    {
        if (is_dir($dirPath)) {
            return true;
        }

        return mkdir($dirPath, 0777, true);
    }

}
