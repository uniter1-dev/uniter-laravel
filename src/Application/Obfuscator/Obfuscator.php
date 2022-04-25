<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator;

use PhpUniter\PackageLaravel\Application\Obfuscator\Entity\ObfuscateMap;
use PhpUniter\PackageLaravel\Application\Obfuscator\Exception\ObfuscationFailed;

class Obfuscator
{
    /**
     * @throws ObfuscationFailed
     */
    public static function obfuscate(ObfuscateMap $map, Obfuscatable $localFile, callable $getKeySaver): string
    {
        $obfuscated = preg_replace_callback_array(
            $replacements = [
                '/(?<=class\s)(\w+)/'       => $getKeySaver($map::CLASS_NAMES),
                '/(?<=function\s)(\w+)/'    => $getKeySaver($map::METHODS),
                '/(?<=const\s)(\w+)/'       => $getKeySaver($map::CONSTANTS),
                '/(?<=namespace\s)([^;]+)/' => $getKeySaver($map::NAMESPACES),
            ],
            $localFile->getFileBody(),
            -1,
            $count
        );

        foreach ($map->getMap()[$map::METHODS] as $pair) {
            $obfuscated = self::replaceInText('->', $pair, $obfuscated, '(');
            $obfuscated = self::replaceInText('::', $pair, $obfuscated, '(');
        }

        foreach ($map->getMap()[$map::CONSTANTS] as $pair) {
            $obfuscated = self::replaceInText('::', $pair, $obfuscated);
        }

        return $obfuscated;
    }

    public static function deObfuscate(ObfuscateMap $map, string $fileBody): string
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

    private static function replaceInText($prefix, $pair, $subject, $suffix = ''): string
    {
        $methodInText = $prefix.$pair[1].$suffix;

        return str_replace($methodInText, $prefix.$pair[0].$suffix, $subject);
    }
}
