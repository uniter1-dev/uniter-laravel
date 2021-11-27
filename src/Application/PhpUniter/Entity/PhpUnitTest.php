<?php

namespace PhpUniter\PackageLaravel\Application\PhpUniter\Entity;

class PhpUnitTest
{
    private string $unitTestBody;

    /**
     * PhpUnitTest constructor.
     * @param string $unitTestBody
     */
    public function __construct(string $unitTestBody)
    {
        $this->unitTestBody = $unitTestBody;
    }

    /**
     * @return string
     */
    public function getUnitTestBody(): string
    {
        return $this->unitTestBody;
    }
}