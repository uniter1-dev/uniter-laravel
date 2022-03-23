<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\File\Exception\DirectoryPathWrong;
use PhpUniter\PackageLaravel\Application\File\Exception\FileNotAccessed;

class FileRepository implements FileRepoInterface
{

    /**
     * @throws DirectoryPathWrong
     * @throws FileNotAccessed
     */
    public function saveOne(string $unitTestText, string $filePath): bool
    {
        $testDir = dirname($filePath);
        $touch = $this->touchDir($testDir);

        if (!$touch) {
            throw new DirectoryPathWrong("Directory $testDir cannot be created");
        }

        if (file_put_contents($filePath, $unitTestText)) {
            return true;
        }

        throw new FileNotAccessed("File $filePath was not saved");
    }

    protected function touchDir(string $dirPath): bool
    {
        if (is_dir($dirPath)) {
            return true;
        }

        return mkdir($dirPath, 0777, true);
    }
}
