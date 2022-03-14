<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator\Entity;

use Closure;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;

use PhpUniter\PackageLaravel\Application\Obfuscator\Obfuscated;
use PhpUniter\PackageLaravel\Application\Obfuscator\Obfuscator;

class ObfuscatedClass implements Obfuscated
{
    private ObfuscateMap $map;
    private LocalFile $localFile;
    private Closure $keyGenerator;

    public function __construct(LocalFile $localFile, Closure $uniqKeyGenerator)
    {
        $this->localFile = $localFile;
        $this->keyGenerator = $uniqKeyGenerator;
        $this->map = new ObfuscateMap();
    }

    public function getObfuscatedFileBody(): string
    {
        return Obfuscator::getObfuscated($this->map, $this->localFile, [$this, 'getKeySaver']);
    }

    public function deObfuscate(string $fileBody): string
    {
        return Obfuscator::deObf($this->map, $fileBody);
    }

    public function getKeySaver(ObfuscateMap $map, string $mapKey): callable
    {
        return function ($matches) use ($map, $mapKey) {
            return $map->storeKeysAs($mapKey, $matches, $this->getUniqueKey());
        };
    }

    public function getUniqueKey(): string
    {
        return ($this->keyGenerator)();
    }
}
