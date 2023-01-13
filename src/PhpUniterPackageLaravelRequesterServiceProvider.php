<?php

namespace Uniter1\UniterLaravel;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Uniter1\UniterLaravel\Controller\Console\Cli\GenerateUniterTestCommand;
use Uniter1\UniterLaravel\Controller\Console\Cli\RegisterUniterUserCommand;
use Uniter1\UniterRequester\Application\Generation\NamespaceGenerator;
use Uniter1\UniterRequester\Application\Generation\PathCorrector;
use Uniter1\UniterRequester\Application\Generation\UseGenerator;
use Uniter1\UniterRequester\Application\Obfuscator\KeyGenerator\ObfuscateNameMaker;
use Uniter1\UniterRequester\Application\Obfuscator\KeyGenerator\RandomMaker;
use Uniter1\UniterRequester\Application\PhpUnitService;
use Uniter1\UniterRequester\Application\PhpUnitUserRegisterService;
use Uniter1\UniterRequester\Application\Placer;
use Uniter1\UniterRequester\Infrastructure\Integrations\PhpUniterIntegration;
use Uniter1\UniterRequester\Infrastructure\Integrations\PhpUniterRegistration;
use Uniter1\UniterRequester\Infrastructure\Repository\UnitTestRepository;
use Uniter1\UniterRequester\Infrastructure\Request\GenerateClient;
use Uniter1\UniterRequester\Infrastructure\Request\GenerateRequest;
use Uniter1\UniterRequester\Infrastructure\Request\RegisterRequest;

class PhpUniterPackageLaravelRequesterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('uniter1.php'),
            ], 'config');

            // Registering package commands.
            $this->commands([
                GenerateUniterTestCommand::class,
                RegisterUniterUserCommand::class,
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
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'uniter1');

        $this->app->bind(GenerateClient::class, function () {
            return new GenerateClient();
        });

        $this->app->bind(RegisterRequest::class, function (Application $app) {
            return  new RegisterRequest(
            'POST',
            config('uniter1.baseUrl').config('uniter1.registrationPath'),
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
            config('uniter1.baseUrl').config('uniter1.generationPath'),
            [
                'accept'        => ['application/json'],
                'timeout'       => 2,
            ],
            config('uniter1.accessToken')
            );
        });

        $this->app->bind(PhpUniterIntegration::class, function (Application $app) {
            return new PhpUniterIntegration(
                $this->app->get(GenerateClient::class),
                $this->app->get(GenerateRequest::class)
            );
        });

        $this->app->bind(UnitTestRepository::class, function () {
            return new UnitTestRepository(config('uniter1.projectDirectory'));
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
            return new UseGenerator(config('uniter1.helperClass'));
        });

        $this->app->bind(NamespaceGenerator::class, function () {
            return new NamespaceGenerator(
                config('uniter1.baseNamespace'),
                config('uniter1.unitTestsDirectory'),
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
                config('uniter1.obfuscate'),
            );
        });

        $this->app->bind(LaravelRequester::class, function (Application $app) {
            return new LaravelRequester(
                $this->app->get(PhpUnitUserRegisterService::class),
                $this->app->get(PhpUnitService::class),
                config('uniter1.projectDirectory')
            );
        });
    }
}
