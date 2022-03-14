<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator\Entity;

use Closure;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\Obfuscator\Exception\ObfuscationFailed;
use PhpUniter\PackageLaravel\Application\Obfuscator\Obfuscated;

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
        return $this->getObfuscated($this->map, $this->localFile);
    }

    public function getObfuscated(ObfuscateMap $map, LocalFile $localFile): string
    {
        $callback = Closure::fromCallable(function ($matches) use ($map) {
            return $this->map->storeKeysAs($map::CLASS_NAMES, $matches, $this->getUniqueKey());
        });

        $obfuscated = preg_replace_callback_array(
            $replacements = [
                '/(?<=class\s)(\w+)/'    => $callback,
                '/(?<=function\s)(\w+)/' => function ($matches) use ($map) {
                    return $this->map->storeKeysAs($map::METHODS, $matches, $this->getUniqueKey());
                },
                '/(?<=const\s)(\w+)/' => function ($matches) use ($map) {
                    return $this->map->storeKeysAs($map::CONSTANTS, $matches, $this->getUniqueKey());
                },
                '/(?<=namespace\s)(.+)/' => function ($matches) use ($map) {
                    return $this->map->storeKeysAs($map::NAMESPACES, $matches, $this->getUniqueKey().';');
                },
            ],
            $localFile->getFileBody(),
            -1,
            $count
        );

        if (count($replacements) > $count) {
            throw new ObfuscationFailed("Obfuscation failed on {$localFile->getFilePath()}, count of replacements is not enough");
        }

        foreach ($map->getMap()[$map::METHODS] as $pair) {
            $obfuscated = self::replaceInText('->', $pair, $obfuscated, '(');
            $obfuscated = self::replaceInText('::', $pair, $obfuscated, '(');
        }

        foreach ($map->getMap()[$map::CONSTANTS] as $pair) {
            $obfuscated = self::replaceInText('::', $pair, $obfuscated);
        }

        return $obfuscated;
    }

    public function deObfuscate(string $fileBody): string
    {
        return self::deObf($this->map, $fileBody);
    }

    public static function deObf(ObfuscateMap $map, string $fileBody): string
    {
        $deObfuscated = $fileBody;

        foreach ($map->getMap()[$map::CLASS_NAMES] as $methodPair) {
            $deObfuscated = str_replace($methodPair[0], $methodPair[1], $deObfuscated);
        }

        foreach ($map->getMap()[$map::METHODS] as $methodPair) {
            $deObfuscated = str_replace($methodPair[0], $methodPair[1], $deObfuscated);
        }
        foreach ($map->getMap()[$map::CONSTANTS] as $methodPair) {
            $deObfuscated = str_replace($methodPair[0], $methodPair[1], $deObfuscated);
        }
        foreach ($map->getMap()[$map::NAMESPACES] as $methodPair) {
            $deObfuscated = str_replace($methodPair[0], $methodPair[1], $deObfuscated);
        }

        return $deObfuscated;
    }

    private static function replaceInText($prefix, $pair, $subject, $suffix = '')
    {
        $methodInText = $prefix.$pair[1].$suffix;

        return str_replace($methodInText, $prefix.$pair[0].$suffix, $subject);
    }

    private function getUniqueKey(): string
    {
        return ($this->keyGenerator)();
    }



}
