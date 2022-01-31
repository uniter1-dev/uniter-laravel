<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\File\Exception\FileNotAccessed;

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
}