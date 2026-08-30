<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Providers;

use Capell\Admin\Facades\CapellAdmin;
use Capell\ExtensionCookbook\Bridges\ExtensionCookbookAdminBridge;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Capell\ExtensionCookbook\Policies\ReferenceEntryPolicy;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Override;

final class AdminServiceProvider extends ServiceProvider
{
    public static string $packageName = 'capell-app/extension-cookbook';

    #[Override]
    public function register(): void
    {
        $this->app->booted(function (): void {
            if (! class_exists(CapellAdmin::class) || ! class_exists(Page::class)) {
                return;
            }

            CapellAdmin::registerAdminBridge(
                self::$packageName,
                ExtensionCookbookAdminBridge::class,
            );
            CapellAdmin::bootAdminBridges(self::$packageName);

            if (class_exists(ReferenceEntry::class) && class_exists(ReferenceEntryPolicy::class)) {
                Gate::policy(ReferenceEntry::class, ReferenceEntryPolicy::class);
            }
        });
    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'capell-extension-cookbook');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'capell-extension-cookbook');
    }
}
