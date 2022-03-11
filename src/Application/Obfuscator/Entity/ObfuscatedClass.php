<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator\Entity;

use Closure;
use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\Obfuscator\Exception\ObfuscationFailed;
use PhpUniter\PackageLaravel\Application\Obfuscator\Obfuscated;

class ObfuscatedClass implements Obfuscated
{
    private const CLASS_NAMES = 'className';
    private const PROPERTIES = 'properties';
    private const METHODS = 'methods';
    private const CONSTANTS = 'constants';
    private const NAMESPACES = 'namespaces';

    private array $map = [
        self::CLASS_NAMES => [],
        self::PROPERTIES  => [],
        self::METHODS     => [],
        self::CONSTANTS   => [],
        self::NAMESPACES  => [],
    ];

    private LocalFile $localFile;
    private Closure $keyGenerator;

    public function __construct(LocalFile $localFile, Closure $uniqKeyGenerator)
    {
        $this->localFile = $localFile;
        $this->keyGenerator = $uniqKeyGenerator;
    }

    public function getObfuscatedFileBody(): string
    {
        $obfuscated = preg_replace_callback_array(
            $replacements = [
                '/(?<=class\s)(\w+)/' => function ($matches) {
                    return $this->storeKeysAs(self::CLASS_NAMES, $matches, $this->getUniqueKey());
                },
                '/(?<=function\s)(\w+)/' => function ($matches) {
                    return $this->storeKeysAs(self::METHODS, $matches, $this->getUniqueKey());
                },
                '/(?<=const\s)(\w+)/' => function ($matches) {
                    return $this->storeKeysAs(self::CONSTANTS, $matches, $this->getUniqueKey());
                },
                '/(?<=namespace\s)(.+)/' => function ($matches) {
                    return $this->storeKeysAs(self::NAMESPACES, $matches, $this->getUniqueKey().';');
                },
            ],
            $this->localFile->getFileBody(),
            -1,
            $count
        );

        if (count($replacements) > $count) {
            throw new ObfuscationFailed("Obfuscation failed on {$this->localFile->getFilePath()}, count of replacements is not enough");
        }

        foreach ($this->map[self::METHODS] as $pair) {
            $obfuscated = self::replaceInText('->', $pair, $obfuscated, '(');
            $obfuscated = self::replaceInText('::', $pair, $obfuscated, '(');
        }

        foreach ($this->map[self::CONSTANTS] as $pair) {
            $obfuscated = self::replaceInText('::', $pair, $obfuscated);
        }

        return $obfuscated;
    }

    public function deObfuscate(string $fileBody): string
    {
        $deObfuscated = $fileBody;

        foreach ($this->map[self::CLASS_NAMES] as $methodPair) {
            $deObfuscated = str_replace($methodPair[0], $methodPair[1], $deObfuscated);
        }

        foreach ($this->map[self::METHODS] as $methodPair) {
            $deObfuscated = str_replace($methodPair[0], $methodPair[1], $deObfuscated);
        }
        foreach ($this->map[self::CONSTANTS] as $methodPair) {
            $deObfuscated = str_replace($methodPair[0], $methodPair[1], $deObfuscated);
        }
        foreach ($this->map[self::NAMESPACES] as $methodPair) {
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

    private function storeKeyAs(string $type, array $matches, string $key): string
    {
        $this->map[$type] = [$key, current($matches)];

        return $key;
    }

    private function storeKeysAs(string $type, array $matches, string $key): string
    {
        $this->map[$type][] = [$key, current($matches)];

        return $key;
    }
}
