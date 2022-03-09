<?php

namespace PhpUniter\PackageLaravel\Application\Obfuscator\Entity;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\Obfuscator\Exception\ObfuscationFailed;
use PhpUniter\PackageLaravel\Application\Obfuscator\Obfuscated;

class ObfuscatedClass implements Obfuscated
{
    private const CLASS_NAME = 'className';
    private const PROPERTIES = 'properties';
    private const METHODS = 'methods';
    private const ARGUMENTS = 'arguments';
    private const CONSTANTS = 'constants';
    private const NAMESPACES = 'namespaces';

    private array $map = [
        self::CLASS_NAME => '',
        self::PROPERTIES => [],
        self::METHODS    => [],
        self::ARGUMENTS  => [],
        self::CONSTANTS  => [],
        self::NAMESPACES => [],
    ];

    private LocalFile $localFile;
    private \Closure $keyGenerator;

    public function __construct(LocalFile $localFile, \Closure $uniqKeyGenerator)
    {
        $this->localFile = $localFile;
        $this->keyGenerator = $uniqKeyGenerator;
    }

    public function getObfuscatedFileBody(): string
    {
        $obfuscated = preg_replace_callback_array(
            $replacements = [
                '/(?<=class\s)(\w+)/' => function ($matches) {
                    return $this->storeKeyAs(self::CLASS_NAME, $matches, $this->getUniqueKey());
                },
                '/(?<=function\s)(\w+)/' => function ($matches) {
                    return $this->storeKeysAs(self::METHODS, $matches, $this->getUniqueKey());
                },
            ],
            $this->localFile->getFileBody(),
            -1,
            $count
        );

        if (count($replacements) > $count) {
            throw new ObfuscationFailed("Obfuscation failed on {$this->localFile->getFilePath()}, count of replacements is not enough");
        }

        $methods = $this->map['methods'];
        foreach ($methods as $pair) {
            $obfuscated = self::replaceInText('->', $pair, $obfuscated);
            $obfuscated = self::replaceInText('::', $pair, $obfuscated);
        }

        return $obfuscated;
    }

    public function deObfuscate(string $fileBody): string
    {
    }

    private static function replaceInText($prefix, $pair, $subject)
    {
        $methodInText = $prefix . $pair[1] . '(';

        return str_replace($methodInText, $prefix . $pair[0] . '(', $subject);
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
