<?php

namespace PhpUniter\PackageLaravel;

use Illuminate\Support\ServiceProvider;
use \GuzzleHttp\Client;
use PhpUniter\PackageLaravel\Application\PhpUniter\Generator;
use PhpUniter\PackageLaravel\Controller\Console\Cli\GeneratePhpUniterTestCommand;

class PhpUniterPackageLaravelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('php-uniter.php'),
            ], 'config');

            // Registering package commands.
             $this->commands([
                 GeneratePhpUniterTestCommand::class
             ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'php-uniter');

        // Register the main class to use with the facade
        $this->app->singleton('php-uniter', function () {
            return new PhpUniterPackageLaravel;
        });

        $this->app->bind(Generator::class, function ($app) {
            return new Generator(new Client());
        });
    }
}
