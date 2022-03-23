<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

class FakeRepository extends FileRepository implements FileRepoInterface
{
    private array $files = [];

    public function saveOne(string $unitTestText, string $filePath): bool
    {
        $this->files[$filePath] = $unitTestText;

        return true;
    }

    public function getFile(string $filePath): string
    {
        return $this->files[$filePath];
    }
}
