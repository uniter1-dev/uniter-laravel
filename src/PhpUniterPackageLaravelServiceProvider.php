<?php

namespace PhpUniter\PackageLaravel;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use PhpUniter\PackageLaravel\Application\Obfuscator\KeyGenerator\RandomMaker;
use PhpUniter\PackageLaravel\Application\PhpUnitService;
use PhpUniter\PackageLaravel\Application\Placer;
use PhpUniter\PackageLaravel\Controller\Console\Cli\GeneratePhpUniterTestCommand;
use PhpUniter\PackageLaravel\Infrastructure\Integrations\PhpUniterIntegration;
use PhpUniter\PackageLaravel\Infrastructure\Request\GenerateClient;
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

        $this->app->bind(GenerateClient::class, function (Application $app) {
            return new GenerateClient();
        });

        $this->app->bind(GenerateClient::class, function (Application $app) {
            return new GenerateClient();
        });

        $this->app->bind(GenerateRequest::class, function (Application $app) {
            return new GenerateRequest(
                'POST',
                config('php-uniter.baseUrl').'/api/v1/generator/generate',
                [
                    'auth' => [
                        'Authorization' => 'Bearer '.config('php-uniter.accessToken'),
                    ],
                    'timeout' => 2,
                ]
            );
        });

        $this->app->bind(PhpUnitService::class, function (Application $app) {
            return new PhpUnitService($app->make(PhpUniterIntegration::class), $app->make(Placer::class), $app->make(RandomMaker::class));
        });
    }
}
