<?php

namespace PhpUniter\PackageLaravel\Application\PhpUniter\Entity;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;

class PhpUnitTest
{
    private LocalFile $localFile;
    private string $unitTest;
    private array $repositories;

    public function __construct(LocalFile $localFile, string $unitTest, array $repositories = [])
    {
        $this->localFile = $localFile;
        $this->unitTest = $unitTest;
        $this->repositories = $repositories;
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

    /**
     * @return LocalFile
     */
    public function getLocalFile(): LocalFile
    {
        return $this->localFile;
    }


}
