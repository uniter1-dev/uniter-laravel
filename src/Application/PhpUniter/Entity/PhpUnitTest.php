<?php

namespace PhpUniter\PackageLaravel\Application\PhpUniter\Entity;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;

class PhpUnitTest
{
    private LocalFile $localFile;
    private string $unitTest;
    private string $className;
    private array $repositories;

    public function __construct(LocalFile $localFile, string $unitTest, array $repositories, string $className)
    {
        $this->localFile = $localFile;
        $this->unitTest = $unitTest;
        $this->repositories = $repositories;
        $this->className = $className;
    }

    public function getUnitTest(): string
    {
        return $this->unitTest;
    }

    /**
     * @return string[]
     */
    public function getRepositories(): array
    {
        return $this->repositories;
    }

    public function getLocalFile(): LocalFile
    {
        return $this->localFile;
    }

    public function getClassName(): string
    {
        return $this->className;
    }
}
