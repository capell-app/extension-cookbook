<?php

declare(strict_types=1);

use Capell\Core\Actions\InstallPackageAction;
use Capell\Core\Actions\RuntimeRefresh\RefreshInstalledPackageRuntimeAction;
use Capell\Core\Data\Extensions\ExtensionContributionReceiptData;
use Capell\Core\Enums\ExtensionContributionType;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Support\Extensions\ExtensionContributionReceiptRegistry;
use Capell\ExtensionCookbook\Filament\Resources\ReferenceEntries\ReferenceEntryResource;
use Capell\ExtensionCookbook\Jobs\AuditExtensionCookbookHealthJob;
use Capell\ExtensionCookbook\Providers\ExtensionCookbookServiceProvider;
use Capell\Tests\Support\InstalledRuntimeParity;
use Capell\Tests\Support\InstalledRuntimeReceiptWitness;
use Capell\Tests\Support\InstalledRuntimeState;
use Capell\Tests\Support\InstalledRuntimeTestCase;
use Capell\Tests\Support\PackageInstallationSurfaceSnapshot;
use Filament\Facades\Filament;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use PHPUnit\Framework\Assert;

it('registers reference entries before fresh installed panel construction', function (): void {
    InstalledRuntimeState::run(static function (): void {
        $app = InstalledRuntimeTestCase::boot(InstalledRuntimeParity::manifest('extension-cookbook'), true, sourceRoot: dirname(__DIR__, 2));
        $panel = Filament::getPanel('admin');
        $resources = array_values(array_filter(
            $panel->getResources(),
            static fn (string $resource): bool => $resource === ReferenceEntryResource::class,
        ));
        $routes = $app->make(Router::class)->getRoutes();
        $routes->refreshNameLookups();

        expect($resources)->toBe([ReferenceEntryResource::class])
            ->and($routes->getByName(ReferenceEntryResource::getRouteBaseName($panel) . '.index'))->not->toBeNull();
    });
});

it('registers installed runtime once during in-process installation', function (): void {
    $surface = static fn (Application $app): int => count(array_filter(
        $app->make(Schedule::class)->events(),
        static fn (Event $event): bool => $event->description === AuditExtensionCookbookHealthJob::class,
    ));

    $fresh = 0;
    InstalledRuntimeState::run(static function () use ($surface, &$fresh): void {
        $app = InstalledRuntimeTestCase::boot(InstalledRuntimeParity::manifest('extension-cookbook'), true, sourceRoot: dirname(__DIR__, 2));
        $fresh = $surface($app);
        expect($fresh)->toBe(1);
    });

    InstalledRuntimeState::run(static function () use ($surface, $fresh): void {
        $app = InstalledRuntimeTestCase::boot(InstalledRuntimeParity::manifest('extension-cookbook'), false, sourceRoot: dirname(__DIR__, 2));
        $package = CapellCore::getPackage(ExtensionCookbookServiceProvider::$packageName);
        expect($surface($app))->toBe(0);
        InstalledRuntimeTestCase::prepareLateInstallMigrations($app);
        InstallPackageAction::run($package);
        expect($surface($app))->toBe($fresh);

        $schedule = $app->make(Schedule::class);
        $scheduledEvents = $schedule->events();
        $listeners = $app->make(Dispatcher::class)->getRawListeners();
        RefreshInstalledPackageRuntimeAction::run($package);
        expect($surface($app))->toBe($fresh)
            ->and($app->make(Dispatcher::class)->getRawListeners())->toBe($listeners)
            ->and($schedule->events())->toBe($scheduledEvents);
    });
});

it('preserves unrelated Blade component overrides during late installation and refresh', function (): void {
    InstalledRuntimeState::run(static function (): void {
        $app = InstalledRuntimeTestCase::boot(
            InstalledRuntimeParity::manifest('extension-cookbook'),
            false,
            static function (Application $app): void {
                $app->make(Repository::class)->set('capell-frontend.blade_components.review-alias', 'configured-component');
            },
            sourceRoot: dirname(__DIR__, 2),
        );
        $blade = $app->make(BladeCompiler::class);
        expect($blade->getClassComponentAliases())->toHaveKey('review-alias', 'configured-component');

        Blade::component('overridden-component', 'review-alias');
        expect($blade->getClassComponentAliases())->toHaveKey('review-alias', 'overridden-component')
            ->not->toHaveKey('extension-cookbook.widget');

        $package = CapellCore::getPackage(ExtensionCookbookServiceProvider::$packageName);
        InstalledRuntimeTestCase::prepareLateInstallMigrations($app);
        InstallPackageAction::run($package);

        expect($blade->getClassComponentAliases())->toHaveKey('review-alias', 'overridden-component')
            ->toHaveKey('extension-cookbook.widget', 'capell-extension-cookbook::widget.extension-cookbook');

        RefreshInstalledPackageRuntimeAction::run($package);
        expect($blade->getClassComponentAliases())->toHaveKey('review-alias', 'overridden-component')
            ->toHaveKey('extension-cookbook.widget', 'capell-extension-cookbook::widget.extension-cookbook');
    });
});

it('matches every fresh installed surface after metadata boot and repeated installation refresh', function (): void {
    /** @return array<string, mixed> */
    $capture = static function (Application $app): array {
        $finder = $app->make(Factory::class)->getFinder();
        Assert::assertInstanceOf(FileViewFinder::class, $finder);
        $components = array_values(array_filter(
            $app->make(ExtensionContributionReceiptRegistry::class)->forPackage(ExtensionCookbookServiceProvider::$packageName),
            static fn (ExtensionContributionReceiptData $receipt): bool => $receipt->type === ExtensionContributionType::FrontendComponent,
        ));
        expect($components)->toHaveCount(1)
            ->and($components[0]->key)->toBe('extension-cookbook.widget')
            ->and($components[0]->implementation)->toBe('capell-extension-cookbook::widget.extension-cookbook');

        return PackageInstallationSurfaceSnapshot::capture($app) + [
            'view-hints' => $finder->getHints()['capell-extension-cookbook'],
            'frontend-component-receipt' => $components[0]->toArray(),
        ];
    };

    $fresh = [];
    InstalledRuntimeState::run(static function () use ($capture, &$fresh): void {
        $app = InstalledRuntimeTestCase::boot(InstalledRuntimeParity::manifest('extension-cookbook'), true, sourceRoot: dirname(__DIR__, 2));
        $fresh = $capture($app);
        expect($app->make(BladeCompiler::class)->getClassComponentAliases())->toHaveKey('extension-cookbook.widget', 'capell-extension-cookbook::widget.extension-cookbook');
    });

    InstalledRuntimeState::run(static function () use ($capture, $fresh): void {
        $app = InstalledRuntimeTestCase::boot(InstalledRuntimeParity::manifest('extension-cookbook'), false, sourceRoot: dirname(__DIR__, 2));
        $package = CapellCore::getPackage(ExtensionCookbookServiceProvider::$packageName);
        $app->register(ExtensionCookbookServiceProvider::class);
        expect($app->make(BladeCompiler::class)->getClassComponentAliases())->not->toHaveKey('extension-cookbook.widget');
        InstalledRuntimeTestCase::prepareLateInstallMigrations($app);
        $router = $app->make(Router::class);
        $stack = $router->getGroupStack();
        $router->group(['as' => 'other-hook.', 'prefix' => 'other-hook', 'middleware' => 'other-hook'], static function () use ($package): void {
            InstallPackageAction::run($package);
        });
        $router->getRoutes()->refreshNameLookups();
        expect($router->getGroupStack())->toBe($stack)
            ->and($router->getRoutes()->getByName('capell-extension-cookbook.index')?->uri())->toBe('extension-cookbook');
        $actual = $capture($app);
        foreach ($fresh as $surface => $expected) {
            expect($actual[$surface])->toBe($expected, $surface);
        }

        $witness = $app->make(InstalledRuntimeReceiptWitness::class);
        $emissions = $witness->emissions(ExtensionCookbookServiceProvider::$packageName);
        for ($refresh = 0; $refresh < 2; $refresh++) {
            RefreshInstalledPackageRuntimeAction::run($package);
            $actual = $capture($app);
            foreach ($fresh as $surface => $expected) {
                expect($actual[$surface])->toBe($expected, $surface);
            }

            expect($witness->emissions(ExtensionCookbookServiceProvider::$packageName))->toBe($emissions);
        }
    });
});
