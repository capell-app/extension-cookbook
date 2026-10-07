<?php

declare(strict_types=1);

use Capell\ExtensionCookbook\Jobs\AuditExtensionCookbookHealthJob;
use Capell\ExtensionCookbook\Providers\ExtensionCookbookServiceProvider;
use Capell\Tests\Support\PackageInstallationSurfaceSnapshot;
use Capell\Tests\Support\PackageInstallationTestCase;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Application;
use Illuminate\View\Compilers\BladeCompiler;

it('registers installed runtime once during in-process installation', function (): void {
    $surface = static fn (Application $app): int => count(array_filter(
        $app->make(Schedule::class)->events(),
        static fn (Event $event): bool => $event->description === AuditExtensionCookbookHealthJob::class,
    ));

    $fresh = 0;
    PackageInstallationTestCase::assertFreshInstalledBoot('extension-cookbook', static function (Application $app) use ($surface, &$fresh): void {
        $fresh = $surface($app);
        expect($fresh)->toBe(1);
    });

    PackageInstallationTestCase::assertInProcessInstallation('extension-cookbook', static function (Application $app, Closure $refresh) use ($surface, $fresh): void {
        expect($surface($app))->toBe(0);
        $refresh();
        expect($surface($app))->toBe($fresh);

        $schedule = $app->make(Schedule::class);
        $scheduledEvents = $schedule->events();
        $listeners = $app->make(Dispatcher::class)->getRawListeners();
        $refresh();
        expect($surface($app))->toBe($fresh)
            ->and($app->make(Dispatcher::class)->getRawListeners())->toBe($listeners)
            ->and($schedule->events())->toBe($scheduledEvents);
    });
});

it('matches every fresh installed surface after metadata boot and repeated installation refresh', function (): void {
    $fresh = [];
    PackageInstallationTestCase::assertFreshInstalledBoot('extension-cookbook', static function (Application $app) use (&$fresh): void {
        $fresh = PackageInstallationSurfaceSnapshot::capture($app);
        expect($app->make(BladeCompiler::class)->getClassComponentAliases())->toHaveKey('extension-cookbook.widget', 'capell-extension-cookbook::widget.extension-cookbook');
    });

    PackageInstallationTestCase::assertInProcessInstallation('extension-cookbook', static function (Application $app, Closure $refresh) use ($fresh): void {
        $app->register(ExtensionCookbookServiceProvider::class);
        expect($app->make(BladeCompiler::class)->getClassComponentAliases())->not->toHaveKey('extension-cookbook.widget');
        $refresh();
        $actual = PackageInstallationSurfaceSnapshot::capture($app);
        foreach ($fresh as $surface => $expected) {
            expect($actual[$surface])->toBe($expected, $surface);
        }
        $refresh();
        $actual = PackageInstallationSurfaceSnapshot::capture($app);
        foreach ($fresh as $surface => $expected) {
            expect($actual[$surface])->toBe($expected, $surface);
        }
    });
});
