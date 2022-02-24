<?php

namespace PhpUniter\PackageLaravel\Application;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;
use PhpUniter\PackageLaravel\Infrastructure\Repository\FileRepository;

class Placer
{
    private FileRepository $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function place(PhpUnitTest $phpUnitTest, string $srcPath)
    {
        return $this->placeUnitTest($phpUnitTest, $srcPath);
        //$this->placeRepositories($phpUnitTest->getRepositories());
    }

    /*
     * @TODO merge strategy: add, replace, diff
     */
    private function placeUnitTest(PhpUnitTest $unitTest, string $srcPath)
    {
        return $this->fileRepository->saveOne($unitTest, $srcPath);
    }

    private function placeRepositories(array $repositories)
    {
        foreach ($repositories as $repository) {
            //$existingRepository = findExisting($repository);
            //merge($existingRepository, $repository);
        }
    }
}
