<?php

namespace Uniter1\UniterLaravel\Controller\Console\Cli;

use Illuminate\Console\Command;
use Uniter1\UniterLaravel\LaravelRequester;

class GenerateUniterTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uniter1:generate {filePath}';
    protected $name = 'uniter1:generate';

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

            if (empty($filePath)) {
                $this->error('No file path in command params');

                return 1;
            }

            $code = $laravelRequester->generate($filePath);
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return 1;
        }

        $report = $laravelRequester->getReport();
        foreach ($report->getErrors() as $message) {
            $this->error($message);
        }
        foreach ($report->getInfos() as $message) {
            $this->info($message);
        }

        return $code;
    }
}
