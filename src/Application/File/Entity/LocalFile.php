<?php

namespace PhpUniter\PackageLaravel\Application\File\Entity;

class LocalFile
{
    private string $filePath;
    private string $fileBody;

    /**
     * @param string $filePath
     * @param string $fileBody
     */
    public function __construct(string $filePath, string $fileBody)
    {
        $this->filePath = $filePath;
        $this->fileBody = $fileBody;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getFileBody(): string
    {
        return $this->fileBody;
    }
}