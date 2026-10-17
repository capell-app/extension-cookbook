<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Providers;

use Capell\Admin\Facades\CapellAdmin;
use Capell\Core\Support\Packages\RegistersInstalledRuntime;
use Capell\ExtensionCookbook\Bridges\ExtensionCookbookAdminBridge;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Capell\ExtensionCookbook\Policies\ReferenceEntryPolicy;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Override;

final class AdminServiceProvider extends ServiceProvider
{
    use RegistersInstalledRuntime;

    public static string $packageName = 'capell-app/extension-cookbook';

    #[Override]
    public function register(): void
    {
        $this->registerInstalledRuntime(self::$packageName, 'admin');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'capell-extension-cookbook');

        // Admin prepares registered bridges while constructing the Filament panel.
        if (class_exists(CapellAdmin::class) && class_exists(Page::class)) {
            CapellAdmin::registerAdminBridge(
                self::$packageName,
                ExtensionCookbookAdminBridge::class,
            );
        }
    }

    public function boot(): void
    {
        $this->loadViewsFrom(dirname(__DIR__, 2) . '/resources/views', 'capell-extension-cookbook');

        // The bridge and policy are deliberately available before installation.
        if (! class_exists(CapellAdmin::class) || ! class_exists(Page::class)) {
            return;
        }

        CapellAdmin::bootAdminBridges(self::$packageName);

        if (class_exists(ReferenceEntry::class) && class_exists(ReferenceEntryPolicy::class)) {
            Gate::policy(ReferenceEntry::class, ReferenceEntryPolicy::class);
        }
    }

    protected function bootInstalledRuntime(): void {}
}
