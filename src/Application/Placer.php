<?php

namespace PhpUniter\PackageLaravel\Application;

use PhpUniter\PackageLaravel\Application\File\Exception\DirectoryPathWrong;
use PhpUniter\PackageLaravel\Application\File\Exception\FileNotAccessed;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;
use PhpUniter\PackageLaravel\Infrastructure\Repository\FileRepository;

class Placer
{
    private FileRepository $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * @throws DirectoryPathWrong
     * @throws FileNotAccessed
     */

    public function place(PhpUnitTest $phpUnitTest): bool
    {
        return $this->placeUnitTest($phpUnitTest->getUnitTest(), $phpUnitTest->getLocalFile()->getFilePath());
    }

    /**
     * @throws DirectoryPathWrong
     * @throws FileNotAccessed
     */

    private function placeUnitTest(string $unitTestText, string $filePath): bool
    {
        return $this->fileRepository->saveOne($unitTestText, $filePath);
    }

}
