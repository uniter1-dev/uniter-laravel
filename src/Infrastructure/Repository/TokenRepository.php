<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

class TokenRepository
{
    public static function saveToken(string $token): int
    {
        config(['PHP_UNITER_ACCESS_TOKEN' => $token]);
        self::putPermanentEnv('PHP_UNITER_ACCESS_TOKEN', $token);

        return strlen($token);
    }

    public static function putPermanentEnv($key, $value)
    {
        $path = app()->environmentFilePath();

        $escaped = preg_quote('='.env($key), '/');

        file_put_contents($path, preg_replace(
            "/^{$key}{$escaped}/m",
            "{$key}={$value}",
            file_get_contents($path)
        ));
    }
}
