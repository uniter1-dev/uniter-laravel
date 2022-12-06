<?php

namespace PhpUniter\PhpUniterLaravel;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use PhpUniter\PhpUniterLaravel\Controller\Console\Cli\GeneratePhpUniterTestCommand;
use PhpUniter\PhpUniterLaravel\Controller\Console\Cli\RegisterPhpUniterUserCommand;
use PhpUniter\PhpUniterRequester\Application\Generation\NamespaceGenerator;
use PhpUniter\PhpUniterRequester\Application\Generation\PathCorrector;
use PhpUniter\PhpUniterRequester\Application\Generation\UseGenerator;
use PhpUniter\PhpUniterRequester\Application\Obfuscator\KeyGenerator\ObfuscateNameMaker;
use PhpUniter\PhpUniterRequester\Application\Obfuscator\KeyGenerator\RandomMaker;
use PhpUniter\PhpUniterRequester\Application\PhpUnitService;
use PhpUniter\PhpUniterRequester\Application\PhpUnitUserRegisterService;
use PhpUniter\PhpUniterRequester\Application\Placer;
use PhpUniter\PhpUniterRequester\Infrastructure\Integrations\PhpUniterIntegration;
use PhpUniter\PhpUniterRequester\Infrastructure\Integrations\PhpUniterRegistration;
use PhpUniter\PhpUniterRequester\Infrastructure\Repository\UnitTestRepository;
use PhpUniter\PhpUniterRequester\Infrastructure\Request\GenerateClient;
use PhpUniter\PhpUniterRequester\Infrastructure\Request\GenerateRequest;
use PhpUniter\PhpUniterRequester\Infrastructure\Request\RegisterRequest;

class PhpUniterPackageLaravelRequesterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('php-uniter.php'),
            ], 'config');

            // Registering package commands.
            $this->commands([
                GeneratePhpUniterTestCommand::class,
                RegisterPhpUniterUserCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedOperand
     * @psalm-suppress InvalidScalarArgument
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'php-uniter');

        $this->app->bind(GenerateClient::class, function () {
            return new GenerateClient();
        });

        $this->app->bind(RegisterRequest::class, function (Application $app) {
            return  new RegisterRequest(
            'POST',
            config('php-uniter.baseUrl').config('php-uniter.registrationPath'),
            [
                'accept'        => ['application/json'],
                'timeout'       => 2,
            ]
            );
        });

        $this->app->bind(PhpUniterRegistration::class, function () {
            return new PhpUniterRegistration($this->app->get(GenerateClient::class),
                $this->app->get(RegisterRequest::class));
        });

        $this->app->bind(PhpUnitUserRegisterService::class, function (Application $app) {
            return new PhpUnitUserRegisterService(
                $this->app->get(PhpUniterRegistration::class),
            );
        });

        $this->app->bind(GenerateRequest::class, function () {
            return new GenerateRequest(
            'POST',
            config('php-uniter.baseUrl').config('php-uniter.generationPath'),
            [
                'accept'        => ['application/json'],
                'timeout'       => 2,
            ],
            config('php-uniter.accessToken')
            );
        });

        $this->app->bind(PhpUniterIntegration::class, function (Application $app) {
            return new PhpUniterIntegration(
                $this->app->get(GenerateClient::class),
                $this->app->get(GenerateRequest::class)
            );
        });

        $this->app->bind(UnitTestRepository::class, function () {
            return new UnitTestRepository(config('php-uniter.projectDirectory'));
        });

        $this->app->bind(Placer::class, function () {
            return new Placer(
                $this->app->get(UnitTestRepository::class)
            );
        });

        $this->app->bind(ObfuscateNameMaker::class, function () {
            return new RandomMaker();
        });

        $this->app->bind(PathCorrector::class, function () {
            return new PathCorrector();
        });

        $this->app->bind(UseGenerator::class, function () {
            return new UseGenerator(config('php-uniter.helperClass'));
        });

        $this->app->bind(NamespaceGenerator::class, function () {
            return new NamespaceGenerator(
                config('php-uniter.baseNamespace'),
                config('php-uniter.unitTestsDirectory'),
                $this->app->get(PathCorrector::class)
            );
        });

        $this->app->bind(PhpUnitService::class, function () {
            return new PhpUnitService(
                $this->app->get(PhpUniterIntegration::class),
                $this->app->get(Placer::class),
                $this->app->get(ObfuscateNameMaker::class),
                $this->app->get(NamespaceGenerator::class),
                $this->app->get(UseGenerator::class),
                config('php-uniter.obfuscate'),
            );
        });

        $this->app->bind(LaravelRequester::class, function (Application $app) {
            return new LaravelRequester(
                $this->app->get(PhpUnitUserRegisterService::class),
                $this->app->get(PhpUnitService::class),
                config('php-uniter.projectDirectory')
            );
        });
    }
}
