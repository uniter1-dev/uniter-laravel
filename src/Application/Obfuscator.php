<?php

namespace PhpUniter\PackageLaravel\Application;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;

class Obfuscator
{
    public function obfuscate(string $classBody): string {
        return preg_replace(
            ["/class\s+${className}/i", '/(|public|private|protected)\s+(static\s+)?function/i'],
            ["class $proxyClassName", 'public $2function'],
            $classBody
        );
    }
}
