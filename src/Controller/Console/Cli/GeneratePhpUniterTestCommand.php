<?php

namespace PhpUniter\PackageLaravel\Controller\Console\Cli;

use Illuminate\Console\Command;

use PhpUniter\PackageLaravel\Application\PhpUnitService;
use PhpUniter\PackageLaravel\Infrastructure\Repository\FileRepository;

class GeneratePhpUniterTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'php-uniter:generate {filePath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate phpunit test';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FileRepository $fileRepository, PhpUnitService $service)
    {
        $filePath = $this->argument('filePath');

        $file = $fileRepository->findOne($filePath);
        if (!$file) {
            $this->error("File {$filePath} not found");
            return 1;
        }

        $service->process($file);
    }
}
