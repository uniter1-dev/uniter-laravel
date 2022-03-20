<?php

namespace PhpUniter\PackageLaravel\Infrastructure\Repository;

use PhpUniter\PackageLaravel\Application\File\Entity\LocalFile;
use PhpUniter\PackageLaravel\Application\File\Exception\DirectoryPathWrong;
use PhpUniter\PackageLaravel\Application\File\Exception\FileNotAccessed;
use Illuminate\Support\Facades\Storage;

class FileRepository
{
    /**
     * @throws FileNotAccessed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function findOne(string $filePath): LocalFile
    {

        if (Storage::disk('local')->exists($filePath)) {
            return new LocalFile(
                $filePath,
                Storage::disk('local')->get($filePath)
            );
        }

        throw new FileNotAccessed("File $filePath was not found");
    }

    /**
     * @throws DirectoryPathWrong
     * @throws FileNotAccessed
     */
    public function saveOne(string $unitTestText, string $filePath): bool
    {
        $testDir = dirname($filePath);
        $touch = $this->touchDir($testDir);

        if (!$touch) {
            throw new DirectoryPathWrong("Directory $testDir cannot be created");
        }

        if (file_put_contents($filePath, $unitTestText)) {
            return true;
        }

        throw new FileNotAccessed("File $filePath was not saved");
    }

    private function touchDir(string $dirPath): bool
    {
        if (Storage::disk('local')->exists($dirPath)) {
            return true;
        }

        return Storage::disk('local')->makeDirectory($dirPath);
    }
}
