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

    public function place(PhpUnitTest $phpUnitTest)
    {
        return $this->placeUnitTest($phpUnitTest->getUnitTest(), $phpUnitTest->getLocalFile()->getFilePath());
    }

    /*
     * @TODO merge strategy: add, replace, diff
     */
    private function placeUnitTest(string $unitTestText, string $filePath)
    {
        return $this->fileRepository->saveOne($unitTestText, $filePath);
    }

}
