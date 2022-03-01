<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Integrations;

use GuzzleHttp\Client;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateRequest;

class PhpUniterIntegration
{
    private Client $client;
    private GenerateRequest $generateRequest;

    public function __construct(Client $client, GenerateRequest $generateRequest)
    {
        $this->client = $client;
        $this->generateRequest = $generateRequest;
    }

    public function generatePhpUnitTest(LocalFile $localFile, array $options): PhpUnitTest
    {
        $unitTest = $this->client->send(
            $this->generateRequest,
            [
                'json' => [
                    'class' => $localFile->getFileBody(),
                    'options' => $options,
                ],
            ]
        );

        return new PhpUnitTest(
            $localFile,
            $unitTest['unitTest'],
            $unitTest['repositories'],
        );
    }
}
