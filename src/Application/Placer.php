<?php

namespace PhpUniter\PackageLaravel\Application;

use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;
use PhpUniter\PackageLaravel\Infrastructure\Repository\FileRepository;

/**
 *
 */
class Placer
{
    private FileRepository $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function place(PhpUnitTest $phpUnitTest)
    {
        $this->placeUnitTest($phpUnitTest->getUnitTest());
        $this->placeRepositories($phpUnitTest->getRepositories());
    }

    /*
     * @TODO merge strategy: add, replace, diff
     */
    private function placeUnitTest(string $unitTest) {
        $existingUnitTest = $this->fileRepository->findOne($unitTest);
        //merge($existingUnitTest, $unitTest);
    }

    private function placeRepositories(array $repositories){

        foreach($repositories as $repository)
        {
            //$existingRepository = findExisting($repository);
            //merge($existingRepository, $repository);
        }
    }

}
