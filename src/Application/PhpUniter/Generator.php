<?php

namespace PhpUniter\PackageLaravel\Application\PhpUniter;

use GuzzleHttp\Client;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\Obfuscator;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;

class Generator
{
    private Client $client;
    private Obfuscator $obfuscatorService;

    public function __construct(Client $client, Obfuscator $obfuscatorService)
    {
        $this->client = $client;
        $this->obfuscatorService = $obfuscatorService;
    }

    public function generate(LocalFile $localFile): PhpUnitTest
    {
        $obfuscatedFileBody = $this->obfuscatorService->obfuscate($localFile->getFileBody());
        $unitTest = $this->client->post($obfuscatedFileBody);

        return new PhpUnitTest(
            $localFile,
            $this->obfuscatorService->obfuscate($unitTest['unitTest']),
            $this->obfuscatorService->obfuscate($unitTest['repositories']),
        );
    }
}
