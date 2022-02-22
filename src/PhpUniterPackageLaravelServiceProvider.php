<?php

namespace PhpUniter\PackageLaravel;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use PhpUniter\PackageLaravel\Controller\Console\Cli\GeneratePhpUniterTestCommand;
use PhpUniter\PackageLaravel\Infrastructure\Integrations\PhpUniterIntegration;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateRequest;

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
                 GeneratePhpUniterTestCommand::class,
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
            return new PhpUniterPackageLaravel();
        });

        $this->app->bind(PhpUniterIntegration::class, function (Application $app) {
            return new PhpUniterIntegration(
                new Client(),
                $app->make(GenerateRequest::class)
            );
        });

        $this->app->bind(GenerateRequest::class, function (Application $app) {
            return new GenerateRequest(
            'POST',
                config('php-uniter.baseUrl').'/api/v1/generator/generate',
                [
                    'auth' => [
                        'Authorization' => 'Bearer '.config('php-uniter.accessToken'),
                    ],
                    // 'host'    => config('php-uniter.baseUrl'),
                    'timeout' => 2,
                ]
            );
        });
    }
}
