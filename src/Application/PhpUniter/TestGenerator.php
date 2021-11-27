<?php

namespace PhpUniter\PackageLaravel\Application\PhpUniter;

class TestGenerator
{
    private Guzzle $request;

    /**
     * TestGenerator constructor.
     * @param Guzzle $request
     */
    public function __construct(Guzzle $request)
    {
        $this->request = $request;
    }


    public function generate(string $filePath)
    {

    }
}