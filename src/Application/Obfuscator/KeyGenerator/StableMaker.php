<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator\KeyGenerator;

class StableMaker implements ObfuscateNameMaker
{
    public static int $counter = 123456789;

    public function make(): string
    {
        return 'a'.bin2hex((string) self::$counter++);
    }
}
