<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator;

use PhpUniter\PackageLaravel\Application\Obfuscator\Entity\ObfuscatedClass;

class ObfuscatorFabric
{
    public static function getObfuscated(Obfuscatable $obfuscatable): Obfuscated
    {
        return new ObfuscatedClass(
            $obfuscatable,
            function () {
                return 'a'.bin2hex(random_bytes(5));
            },
            new Obfuscator(),
        );
    }
}
