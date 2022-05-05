<?php

namespace PhpUniter\PackageLaravel\Application\Generation;

use PhpUniter\PackageLaravel\Application\Generation\Exception\FilePathWrong;

class PathCorrector
{
    public static function findRelativePath(string $path, string $rootDir): string
    {
        $simplify = self::getSimplePath($path);
        if (self::isRootPath($simplify)) {
            if (0 !== strpos($simplify, $rootDir)) {
                throw new FilePathWrong('Absolute path to file is not in project directory'); // impossible situation
            }

            return substr($simplify, strlen($rootDir));
        }

        return $simplify;
    }

    private static function isRootPath(string $path): bool
    {
        return '/' === $path[0];
    }

    public static function normaliseBackSlashes(string $path): string
    {
        $path = str_replace('/', '\\', $path);

        return str_replace('\\\\', '\\', $path);
    }

    public static function getSimplePath(string $path): string
    {
        // Cleaning path regarding OS
        $path = mb_ereg_replace('\\\\|/', DIRECTORY_SEPARATOR, $path, 'msr');
        // Check if path start with a separator (UNIX)
        $startWithSeparator = DIRECTORY_SEPARATOR === $path[0];
        // Check if start with drive letter
        preg_match('/^[a-z]:/', $path, $matches);
        $startWithLetterDir = isset($matches[0]) ? $matches[0] : false;
        // Get and filter empty sub paths
        $subPaths = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'mb_strlen');

        $absolutes = [];
        foreach ($subPaths as $subPath) {
            if ('.' === $subPath) {
                continue;
            }
            // if $startWithSeparator is false
            // and $startWithLetterDir
            // and (absolutes is empty or all previous values are ..)
            // save absolute cause that's a relative and we can't deal with that and just forget that we want go up
            if ('..' === $subPath
                && !$startWithSeparator
                && !$startWithLetterDir
                && empty(array_filter($absolutes, function ($value) { return !('..' === $value); }))
            ) {
                $absolutes[] = $subPath;
                continue;
            }
            if ('..' === $subPath) {
                array_pop($absolutes);
                continue;
            }
            $absolutes[] = $subPath;
        }

        return
            (($startWithSeparator ? DIRECTORY_SEPARATOR : $startWithLetterDir) ?
                $startWithLetterDir.DIRECTORY_SEPARATOR : ''
            ).implode(DIRECTORY_SEPARATOR, $absolutes);
    }
}
