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
        /** @var LocalFile $obfuscatedFile */
        [$obfuscatedFile, $map] = $this->obfuscatorService->obfuscate($localFile);
        $url = config('php-uniter.baseUrl');
        $api = config('php-uniter.apiUrl');
        $data = json_encode(['class' => $obfuscatedFile->getFileBody()]);
        $unitTest = $this->client->post($url.$api, ['json' => $data]);

        return new PhpUnitTest(
            $localFile,
            $this->obfuscatorService->obfuscate($unitTest['unitTest']),
            $this->obfuscatorService->obfuscate($unitTest['repositories'] ?? ''),
        );
    }
}
