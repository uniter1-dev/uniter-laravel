<?php

namespace PhpUniter\PackageLaravel\Application\Generation;

class PathCorrector
{
    public static function toSlashes(string $path): string
    {
        return str_replace('\\', '/', $path);
    }

    public static function normaliseBackSlashes(string $path): string
    {
        $path = str_replace('/', '\\', $path);

        return str_replace('\\\\', '\\', $path);
    }

    public static function subtract(string $string, string $prefix): string
    {
        return substr($string, strlen($prefix));
    }
}
