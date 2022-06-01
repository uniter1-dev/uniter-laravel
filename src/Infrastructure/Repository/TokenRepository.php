<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

class TokenRepository
{
    /**  @psalm-suppress MixedMethodCall */
    public static function putPermanentEnv(string $key, string $value): void
    {
        /** @var string $path */
        $path = app()->environmentFilePath();

        $escaped = preg_quote('='.(string) env($key), '/');

        file_put_contents($path, preg_replace(
            "/^{$key}{$escaped}/m",
            "{$key}={$value}",
            file_get_contents($path)
        ));
    }
}
