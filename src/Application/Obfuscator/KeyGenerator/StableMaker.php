<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator\KeyGenerator;

class StableMaker implements ObfuscateNameMaker
{
    public static int $counter = 789;

    public function make(): string
    {
        return '_obf'.bin2hex((string) self::$counter++);
    }
}
