<?php

namespace PhpUniter\PhpUniterLaravel\Controller\Console\Cli;

use Illuminate\Console\Command;
use PhpUniter\PhpUniterLaravel\LaravelRequester;


class GeneratePhpUniterTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'php-uniter:generate {filePath}';
    protected $name = 'php-uniter:generate';

    /**
     * The console command description.
     */
    protected $description = 'Generate phpunit test';

    /**
     * Execute the console command.
     */
    public function handle(LaravelRequester $laravelRequester): ?int
    {
        try {
            $filePath = $this->argument('filePath');
            $code = $laravelRequester->generate($filePath);
        } catch (\Exception $e) {
            return 1;
        }

        return $code;
    }
}
