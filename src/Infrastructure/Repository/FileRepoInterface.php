<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

interface FileRepoInterface
{
    public function saveOne(string $unitTestText, string $filePath): bool;
}
