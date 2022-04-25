<?php

namespace PhpUniter\PackageLaravel\Application;

use PhpUniter\PackageLaravel\Application\File\Exception\DirectoryPathWrong;
use PhpUniter\PackageLaravel\Application\File\Exception\FileNotAccessed;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;
use PhpUniter\PackageLaravel\Infrastructure\Repository\UnitTestRepositoryInterface;

class Placer
{
    private UnitTestRepositoryInterface $repository;

    public function __construct(UnitTestRepositoryInterface $fileRepository)
    {
        $this->repository = $fileRepository;
    }

    /**
     * @throws DirectoryPathWrong
     * @throws FileNotAccessed
     */
    public function placeUnitTest(PhpUnitTest $phpUnitTest, string $className): int
    {
        return $this->repository->saveOne($phpUnitTest, $className);
    }
}
