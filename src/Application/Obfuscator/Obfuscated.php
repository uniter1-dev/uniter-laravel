<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator;

use Closure;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;

interface Obfuscated
{
    public function __construct(LocalFile $localFile, Closure $uniqKeyGenerator, Obfuscator $obfuscator);
    public function getObfuscatedFileBody(): string;
    public function deObfuscate(string $fileBody): string;

}
