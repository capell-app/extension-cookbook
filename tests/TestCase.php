<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Tests;

use Capell\Core\Support\Process\ProcessFactoryInterface;
use Capell\Core\Support\Process\SymfonyProcessFactory;
use Capell\ExtensionCookbook\Providers\AdminServiceProvider;
use Capell\ExtensionCookbook\Providers\ExtensionCookbookServiceProvider;
use Capell\ExtensionCookbook\Providers\FrontendServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\LaravelSettings\LaravelSettingsServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Testbench bypasses the host manifest loader, so package tests install
     * each provider explicitly. Runtime-role filtering is covered separately
     * by the manifest contract test.
     *
     * @return list<class-string>
     */
    protected function getPackageProviders(mixed $app): array
    {
        return [
            ExtensionCookbookServiceProvider::class,
            AdminServiceProvider::class,
            FrontendServiceProvider::class,
            LaravelSettingsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp(mixed $app): void
    {
        if ($app instanceof Application) {
            $app->bind(ProcessFactoryInterface::class, SymfonyProcessFactory::class);
        }
    }
}
