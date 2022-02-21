<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator;

use PhpUniter\PackageLaravel\Application\File\Entity\ClassFile;
use PhpUniter\PackageLaravel\Application\Obfuscator\Entity\ObfuscatedClass;
use PhpUniter\PackageLaravel\Application\Obfuscator\Exception\ObfuscatorNotFound;

class ObfuscatorFabric
{
    public static function getObfuscated(Obfuscatable $obfuscatable): Obfuscated
    {
        if ($obfuscatable instanceof ClassFile) {
            return new ObfuscatedClass(
                $obfuscatable,
                function () { return 'a' . bin2hex(random_bytes(5)); }
            );
        }

        throw new ObfuscatorNotFound('Obfuscator for file type '.get_class($obfuscatable).' was not found');
    }
}
