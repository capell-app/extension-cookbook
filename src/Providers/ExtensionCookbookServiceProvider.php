<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Providers;

use Capell\Core\Data\BlueprintSubjectDescriptorData;
use Capell\Core\Data\OutboundEventDefinitionData;
use Capell\Core\Models\Page;
use Capell\Core\Support\ContentGraph\ContentGraphRegistry;
use Capell\Core\Support\Packages\AbstractPackageServiceProvider;
use Capell\Core\Support\Settings\SettingsGroupMetadata;
use Capell\ExtensionCookbook\Console\Commands\ExtensionCookbookDoctorCommand;
use Capell\ExtensionCookbook\ContentGraph\ReferenceEntryContentGraphExtractor;
use Capell\ExtensionCookbook\Data\ReferenceEntryPublishedPayloadData;
use Capell\ExtensionCookbook\Jobs\AuditExtensionCookbookHealthJob;
use Capell\ExtensionCookbook\Listeners\ReferenceEntrySubscriber;
use Capell\ExtensionCookbook\Metrics\ReferenceEntryMetricsCollector;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Capell\ExtensionCookbook\Settings\ExtensionCookbookSettings;
use Capell\ExtensionCookbook\Support\Core\ReferenceEntryPageInterceptor;
use Illuminate\Console\Scheduling\Schedule;
use Override;
use Spatie\LaravelPackageTools\Package;

final class ExtensionCookbookServiceProvider extends AbstractPackageServiceProvider
{
    public static string $name = 'capell-extension-cookbook';

    public static string $packageName = 'capell-app/extension-cookbook';

    private bool $installedRuntimeBooted = false;

    #[Override]
    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::$name)
            ->hasAssets()
            ->hasConfigFile('extension-cookbook')
            ->hasTranslations()
            ->hasViews(self::$name)
            ->hasMigrations([
                '2026_08_29_000001_create_extension_cookbook_entries',
                '2026_08_29_000002_scope_extension_cookbook_entry_slugs',
            ]);
    }

    #[Override]
    protected function bootInstalledPackage(): self
    {
        if ($this->installedRuntimeBooted) {
            return $this;
        }

        $surface = $this->surface();
        $surface->models([ReferenceEntry::class])
            ->blueprintSubject(new BlueprintSubjectDescriptorData('extension-cookbook.entry', 'Capell Extension Cookbook entry', ReferenceEntry::class, self::$packageName))
            ->modelInterceptor(Page::class, ReferenceEntryPageInterceptor::class, 'extension-cookbook.entry')
            ->subscriber(ReferenceEntrySubscriber::class)
            ->settingsClass(ExtensionCookbookSettings::group(), ExtensionCookbookSettings::class)
            ->settingsMetadata(new SettingsGroupMetadata(ExtensionCookbookSettings::group(), 'Capell Extension Cookbook', packageName: self::$packageName))
            ->outboundEvent(new OutboundEventDefinitionData('extension-cookbook.entry-published', 1, ReferenceEntryPublishedPayloadData::class, 'A Capell Extension Cookbook entry was published.', self::$packageName))
            ->metricCollector(ReferenceEntryMetricsCollector::class);

        if ($this->app->runningInConsole()) {
            $this->commands([ExtensionCookbookDoctorCommand::class]);
            $this->callAfterResolving(Schedule::class, static function (Schedule $schedule): void {
                $schedule->job(new AuditExtensionCookbookHealthJob)
                    ->daily()
                    ->withoutOverlapping();
            });
        }

        if ($this->app->bound(ContentGraphRegistry::class)) {
            $this->app->make(ContentGraphRegistry::class)->register(ReferenceEntryContentGraphExtractor::class);
        }

        $this->installedRuntimeBooted = true;

        return $this;
    }
}
