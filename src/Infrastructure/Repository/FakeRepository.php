<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

use PhpUniter\PackageLaravel\Application\File\Exception\DirectoryPathWrong;
use PhpUniter\PackageLaravel\Application\File\Exception\FileNotAccessed;

class FakeRepository extends FileRepository implements FileRepoInterface
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

        if (is_readable($filePath)) {
            $text = file_get_contents($filePath);

            return sha1($text) == sha1($unitTestText);
        }

        throw new FileNotAccessed("File $filePath was not saved");
    }
}
