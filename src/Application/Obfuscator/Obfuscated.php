<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator;

use Closure;

interface Obfuscated
{
    public function __construct(Obfuscatable $localFile, Closure $uniqKeyGenerator, Obfuscator $obfuscator);

    public function getObfuscatedFileBody(): string;

    public function deObfuscate(string $fileBody): string;
}
