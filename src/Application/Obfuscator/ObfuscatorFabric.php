<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator;

use PhpUniter\PackageLaravel\Application\File\Entity\ClassFile;
use PhpUniter\PackageLaravel\Application\Obfuscator\Entity\ObfuscatedClass;
use PhpUniter\PackageLaravel\Application\Obfuscator\KeyGenerator\ObfuscateNameMaker;

class ObfuscatorFabric
{
    public static function getObfuscated(Obfuscatable $obfuscatable, ObfuscateNameMaker $keyGenerator): ?Obfuscated
    {
        if ($obfuscatable instanceof ClassFile) {
            return new ObfuscatedClass(
                $obfuscatable,
                $keyGenerator,
                new Obfuscator(),
            );
        }

        return null;
    }
}
