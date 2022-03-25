<?php

namespace PhpUniter\PackageLaravel\Application;

use PhpUniter\PackageLaravel\Application\File\Exception\DirectoryPathWrong;
use PhpUniter\PackageLaravel\Application\File\Exception\FileNotAccessed;
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
    public function placeUnitTest(string $filePath, string $unitTestText): bool
    {
        return $this->fileRepository->saveOne($unitTestText, $filePath);
    }
}
