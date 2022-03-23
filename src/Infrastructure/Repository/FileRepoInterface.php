<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;

interface FileRepoInterface
{
    public function saveOne(string $unitTestText, string $filePath): bool;
}
