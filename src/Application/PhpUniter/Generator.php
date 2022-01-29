<?php

namespace PhpUniter\PackageLaravel\Application\PhpUniter;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;

class Generator
{
    private GuzzleClient $client;

    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;
    }

    public function generate(LocalFile $localFile): PhpUnitTest
    {
        $unitTest = $this->client->post($localFile->getFileBody());

        return new PhpUnitTest(
            $localFile,
            $unitTest['unitTest'],
            $unitTest['repositories'],
        );
    }
}