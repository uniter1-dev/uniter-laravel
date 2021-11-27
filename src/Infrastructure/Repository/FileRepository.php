<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

use SplFileObject;

class FileRepository
{
    public function findOne(string $filePath): ?SplFileObject
    {
        try {
            if (file_exists($filePath)) {
                return new SplFileObject($filePath, 'r');
            }
        } catch (\RuntimeException $exception) {

        } catch (\LogicException $exception) {

        }

        return null;
    }
}