<?php

namespace PhpUniter\PackageLaravel\Controller\Console\Cli;

use Exception;
use Illuminate\Console\Command;
use PhpUniter\PackageLaravel\Application\PhpUnitService;
use PhpUniter\PackageLaravel\Infrastructure\Repository\FileRepoInterface;

class GeneratePhpUniterTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'php-uniter:generate {filePath} {--base_test_class=} {--namespace=}';

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
    public function handle(FileRepoInterface $fileRepository, PhpUnitService $phpUnitService)
    {
        try {
            $filePath = $this->argument('filePath');

            if (!is_string($filePath)) {
                throw new Exception('Empty filePath command parameter');
            }

            $options = $this->options();
            $file = $fileRepository->findOne($filePath);

            $phpUnitTest = $phpUnitService->process($file, $options);
            $log = $phpUnitTest->getRepositories()['log'];
            $this->warn($log);
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return 1;
        }

        return 0;
    }
}
