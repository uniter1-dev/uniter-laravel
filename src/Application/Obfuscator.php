<?php

namespace PhpUniter\PackageLaravel\Application;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;

class Obfuscator
{
    public function obfuscate(LocalFile $class): array
    {
        $newClassBody = $class->getFileBody(); // todo обфусифицировать
        return [new LocalFile($class->getFilePath(), $newClassBody), []]; // todo вернуть map
    }

    public function deObfuscate(PhpUnitTest $obfuscatedPhpUnitTest, array $map): PhpUnitTest
    {
        $res = $obfuscatedPhpUnitTest->getLocalFile()->getFileBody();
        return new PhpUnitTest($obfuscatedPhpUnitTest->getLocalFile(), $res, []);
    }
}
