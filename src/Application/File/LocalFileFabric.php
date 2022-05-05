<?php

declare(strict_types=1);

namespace PhpUniter\PackageLaravel\Application\File;

use PhpUniter\PackageLaravel\Application\File\Entity\ClassFile;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\File\Exception\CodeTypeWrong;

class LocalFileFabric
{
    const TYPE_CLASS = 'class';
    const TYPE_PROCEDURAL = 'procedural';

    public static function createFile(string $filePath): LocalFile
    {
        $fileBody = file_get_contents($filePath);

        switch (self::getFileType($fileBody)) {
            case self::TYPE_CLASS:
                return new ClassFile($filePath, $fileBody);
            default:
                throw new CodeTypeWrong('File '.$filePath.' can not be obfuscated: code type is not supported');
        }
    }

    private static function getFileType(string $fileBody): ?string
    {
        $isClassFile = preg_match('/(?<=class\s)(\w+)/', $fileBody);

        if ($isClassFile) {
            return self::TYPE_CLASS;
        }

        return null;
    }
}
