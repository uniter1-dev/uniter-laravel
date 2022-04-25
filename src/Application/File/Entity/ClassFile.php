<?php

namespace PhpUniter\PackageLaravel\Application\File\Entity;

use PhpUniter\PackageLaravel\Application\Obfuscator\Obfuscatable;

class ClassFile extends LocalFile implements Obfuscatable
{
    public static function make(LocalFile $file): ?self
    {
        if (empty($file->getFileBody())) {
            return null;
        }

        return new self($file->getFilePath(), $file->getFileBody());
    }
}
