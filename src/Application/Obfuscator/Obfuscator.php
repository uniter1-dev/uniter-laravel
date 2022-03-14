<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\Obfuscator\Entity\ObfuscateMap;
use PhpUniter\PackageLaravel\Application\Obfuscator\Exception\ObfuscationFailed;
use PhpUniter\PackageLaravel\Application\PhpUniter\Entity\PhpUnitTest;

class Obfuscator
{
    public function obfuscate(LocalFile $class): array
    {
        $newClassBody = $class->getFileBody(); // todo обфусифицировать

        return [new LocalFile($class->getFilePath(), $newClassBody), []]; // todo вернуть map
    }

    public function deObfuscate(PhpUnitTest $obfuscatedPhpUnitTest, array $map): PhpUnitTest
    {
        $res = $obfuscatedPhpUnitTest->getLocalFile()->getFileBody();

        return new PhpUnitTest($obfuscatedPhpUnitTest->getLocalFile(), $res, []);
    }


    public static function getObfuscated(ObfuscateMap $map, LocalFile $localFile, callable $getKeySaver): string
    {
        $obfuscated = preg_replace_callback_array(
            $replacements = [
                '/(?<=class\s)(\w+)/'    => $getKeySaver($map, $map::CLASS_NAMES),
                '/(?<=function\s)(\w+)/' => $getKeySaver($map, $map::METHODS),
                '/(?<=const\s)(\w+)/'    => $getKeySaver($map, $map::CONSTANTS),
                '/(?<=namespace\s)(.+)/' => $getKeySaver($map, $map::NAMESPACES),
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

}
