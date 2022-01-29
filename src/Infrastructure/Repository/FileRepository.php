<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;

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

        throw new File
    }
}