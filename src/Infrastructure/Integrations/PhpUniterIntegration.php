<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Integrations;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateClient;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateRequest;

class PhpUniterIntegration
{
    private GenerateClient $client;
    private GenerateRequest $generateRequest;

    public function __construct(GenerateClient $client, GenerateRequest $generateRequest)
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

        $generatedTestJson = $unitTest->getBody()->getContents();
        $generatedTest = json_decode($generatedTestJson, true);
        $generatedTestText = $generatedTest['test'];

        return new PhpUnitTest(
            $localFile,
            $generatedTestText,
            $generatedTest,
        );
    }
}
