# Extension points reference

Use these recipes inside the installed Extension Cookbook package. The complete
PHP classes below reproduce shipped source, including namespaces and imports;
they are not extra classes to register alongside it. For a new package, replace
its namespace, owner, keys, translations and view identifiers consistently.
The [manifest](../capell.json) and [Composer metadata](../composer.json) supply
the provider buckets and dependencies used here. The [overview](overview.md)
contains the contribution map and registrar inventory.

## Frontend provider

The [manifest](../capell.json) puts this provider in the frontend bucket. The installed-package guard remains inside boot(). It loads routes, views and translations, tags a scoped component contributor, and registers the resource group, widget and render hook when their registrars are bound. Registration alone does not publish assets: the [runtime provider](../src/Providers/ExtensionCookbookServiceProvider.php) declares hasAssets(), and the package ships [CSS](../resources/dist/extension-cookbook.css) and [JavaScript](../resources/dist/extension-cookbook.js) for the declared public paths.

Source: [`src/Providers/FrontendServiceProvider.php`](../src/Providers/FrontendServiceProvider.php).

```php
<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Providers;

use Capell\Core\Enums\PresentationLoadingStrategy;
use Capell\Core\Facades\CapellCore;
use Capell\ExtensionCookbook\Support\Frontend\ExtensionCookbookFrontendComponentContributor;
use Capell\ExtensionCookbook\Support\Frontend\ExtensionCookbookRenderHook;
use Capell\ExtensionCookbook\Widget\ExtensionCookbookWidget;
use Capell\ExtensionCookbook\Widget\ExtensionCookbookWidgetInputData;
use Capell\ExtensionCookbook\Widget\ExtensionCookbookWidgetRenderData;
use Capell\Frontend\Contracts\FrontendComponentContributor;
use Capell\Frontend\Data\Assets\FrontendResourceData;
use Capell\Frontend\Data\Assets\FrontendResourceGroupData;
use Capell\Frontend\Data\Assets\PublicResourceSourceData;
use Capell\Frontend\Enums\RenderHookLocation;
use Capell\Frontend\Support\Assets\FrontendResourceRegistry;
use Capell\Frontend\Support\Render\FrontendHookRegistrar;
use Capell\LayoutBuilder\Data\WidgetExtensions\WidgetExtensionDefinitionData;
use Capell\LayoutBuilder\Support\WidgetExtensions\WidgetExtensionRegistrar;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class FrontendServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! CapellCore::isPackageInstalled(ExtensionCookbookServiceProvider::$packageName)) {
            return;
        }

        $base = dirname(__DIR__, 2);
        $this->loadRoutesFrom($base . '/routes/web.php');
        $this->loadViewsFrom($base . '/resources/views', 'capell-extension-cookbook');
        $this->loadTranslationsFrom($base . '/resources/lang', 'capell-extension-cookbook');
        Blade::anonymousComponentNamespace('Capell\\ExtensionCookbook\\View\\Components', 'extension-cookbook');

        $this->app->scoped(ExtensionCookbookFrontendComponentContributor::class);
        $this->app->tag(ExtensionCookbookFrontendComponentContributor::class, FrontendComponentContributor::TAG);

        if ($this->app->bound(FrontendResourceRegistry::class)) {
            $this->app->make(FrontendResourceRegistry::class)->register(new FrontendResourceGroupData(
                key: 'capell-app.extension-cookbook',
                label: 'Capell Extension Cookbook',
                package: ExtensionCookbookServiceProvider::$packageName,
                resources: [
                    FrontendResourceData::style(
                        handle: 'capell-app/extension-cookbook:styles',
                        package: ExtensionCookbookServiceProvider::$packageName,
                        source: new PublicResourceSourceData('/vendor/capell-extension-cookbook/extension-cookbook.css'),
                        loadingStrategy: PresentationLoadingStrategy::Visible,
                    ),
                    FrontendResourceData::classicScript(
                        handle: 'capell-app/extension-cookbook:script',
                        package: ExtensionCookbookServiceProvider::$packageName,
                        source: new PublicResourceSourceData('/vendor/capell-extension-cookbook/extension-cookbook.js'),
                        loadingStrategy: PresentationLoadingStrategy::Visible,
                    ),
                ],
            ));
        }

        if ($this->app->bound(WidgetExtensionRegistrar::class)) {
            $this->app->make(WidgetExtensionRegistrar::class)->register(new WidgetExtensionDefinitionData(
                key: 'capell-app.extension-cookbook',
                packageName: ExtensionCookbookServiceProvider::$packageName,
                stateVersion: 1,
                filamentWidget: ExtensionCookbookWidget::class,
                inputData: ExtensionCookbookWidgetInputData::class,
                renderData: ExtensionCookbookWidgetRenderData::class,
                fallbackView: 'capell-extension-cookbook::widget.extension-cookbook',
                components: ['blade' => 'extension-cookbook::widget.extension-cookbook'],
                resourceGroups: ['capell-app.extension-cookbook'],
                defaultResourceLoadingStrategy: PresentationLoadingStrategy::Visible,
            ));
        }

        if ($this->app->bound(FrontendHookRegistrar::class)) {
            $this->app->make(FrontendHookRegistrar::class)->contribute(
                location: RenderHookLocation::BodyEnd,
                extension: new ExtensionCookbookRenderHook,
                owner: ExtensionCookbookServiceProvider::$packageName,
                key: 'extension-cookbook.route-note',
                target: 'extension-cookbook',
                cacheSafe: true,
            );
        }
    }
}
```

## Typed widget: editor schema

The provider above registers four boundaries under `capell-app.extension-cookbook`: this editor schema, input Data, render Data and fallback Blade view. Its resourceGroups value selects the group registered in that provider. The Filament schema owns authoring fields and validation; it must not leak into public output.

Source: [`src/Widget/ExtensionCookbookWidget.php`](../src/Widget/ExtensionCookbookWidget.php).

```php
<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Widget;

use Capell\Admin\Contracts\Widgets\FilamentWidget;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

final class ExtensionCookbookWidget implements FilamentWidget
{
    public static function getWidgetName(): string
    {
        return 'capell-app.extension-cookbook';
    }

    public static function make(): Block
    {
        return Block::make(self::getWidgetName())
            ->label(__('capell-extension-cookbook::admin.widget.label'))
            ->schema([
                TextInput::make('title')->label(__('capell-extension-cookbook::admin.widget.title'))->required()->maxLength(120),
                Textarea::make('summary')->label(__('capell-extension-cookbook::admin.widget.summary'))->required()->maxLength(500),
            ]);
    }
}
```

## Typed widget: input Data

Keep input defaults in the typed boundary rather than resolving models in Blade.

Source: [`src/Widget/ExtensionCookbookWidgetInputData.php`](../src/Widget/ExtensionCookbookWidgetInputData.php).

```php
<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Widget;

use Spatie\LaravelData\Data;

final class ExtensionCookbookWidgetInputData extends Data
{
    public function __construct(public string $title = 'Extension examples', public string $summary = 'A practical reference for extension contracts.') {}
}
```

## Typed widget: render Data

The fromInput() projection exposes only title and summary. This example needs no database reads. Prepare any record-dependent public values before rendering.

Source: [`src/Widget/ExtensionCookbookWidgetRenderData.php`](../src/Widget/ExtensionCookbookWidgetRenderData.php).

```php
<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Widget;

use Spatie\LaravelData\Data;

final class ExtensionCookbookWidgetRenderData extends Data
{
    public function __construct(public readonly string $title, public readonly string $summary) {}

    public static function fromInput(ExtensionCookbookWidgetInputData $input): self
    {
        return new self($input->title, $input->summary);
    }
}
```

## Typed widget: fallback view

The fallback view receives prepared values and escapes both strings. It does not receive the editor schema, permissions or model relationships.

Source: [`resources/views/widget/extension-cookbook.blade.php`](../resources/views/widget/extension-cookbook.blade.php).

```blade
<article
    class="rounded-lg border border-outline bg-surface-raised p-5 shadow-sm"
>
    <h2 class="font-display text-lg font-semibold text-on-surface">
        {{ $title }}
    </h2>
    <p class="mt-2 text-sm leading-6 text-on-surface-variant">{{ $summary }}</p>
</article>
```

## Frontend component contributor

Implement FrontendComponentContributor and return typed contributions. The frontend provider binds this class as scoped and tags it with FrontendComponentContributor::TAG; defining the class alone does not register it. This Blade-target contribution and the widget definition's components mapping are separate declarations: preserve their actual identifiers.

Source: [`src/Support/Frontend/ExtensionCookbookFrontendComponentContributor.php`](../src/Support/Frontend/ExtensionCookbookFrontendComponentContributor.php).

```php
<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Support\Frontend;

use Capell\Frontend\Contracts\FrontendComponentContributor;
use Capell\Frontend\Data\FrontendComponentContributionData;
use Capell\Frontend\Enums\FrontendComponentTarget;

final class ExtensionCookbookFrontendComponentContributor implements FrontendComponentContributor
{
    /** @return list<FrontendComponentContributionData> */
    public function components(): array
    {
        return [new FrontendComponentContributionData(
            name: 'extension-cookbook.widget',
            component: 'capell-extension-cookbook::widget.extension-cookbook',
            target: FrontendComponentTarget::Blade,
        )];
    }
}
```

## Render hook

The provider's contribute() call registers this extension at BodyEnd with owner `capell-app/extension-cookbook`, key `extension-cookbook.route-note`, target `extension-cookbook` and cacheSafe: true. That cache declaration fits this escaped, non-personalised note; it is not suitable for user-specific content. Never query from a hook inside guarded public rendering.

The [frontend tests](../tests/Feature/Frontend/ExtensionCookbookFrontendTest.php) exercise the hydrated view, hook, component declaration and real package route. These are source references, not evidence that this documentation edit reran the tests.

Source: [`src/Support/Frontend/ExtensionCookbookRenderHook.php`](../src/Support/Frontend/ExtensionCookbookRenderHook.php).

```php
<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Support\Frontend;

use Capell\Frontend\Contracts\RenderHookExtensionInterface;
use Capell\Frontend\Data\RenderHookContext;

final class ExtensionCookbookRenderHook implements RenderHookExtensionInterface
{
    public function render(RenderHookContext $context): string
    {
        return '<p>' . e(__('capell-extension-cookbook::frontend.route_note')) . '</p>';
    }
}
```

## Health check

The [manifest](../capell.json) declares this ChecksExtensionHealth implementation. It checks whether package-owned storage exists and returns typed diagnostics without creating or repairing tables. The [health test](../tests/Feature/Health/ExtensionCookbookHealthCheckTest.php) covers this diagnostic.

Source: [`src/Health/ExtensionCookbookHealthCheck.php`](../src/Health/ExtensionCookbookHealthCheck.php).

```php
<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Health;

use Capell\Core\Contracts\Extensions\ChecksExtensionHealth;
use Capell\Core\Data\Diagnostics\DoctorCheckResultData;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

final class ExtensionCookbookHealthCheck implements ChecksExtensionHealth
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }

    /** @return Collection<int, DoctorCheckResultData> */
    public static function runDiagnostics(): Collection
    {
        $table = (new ReferenceEntry)->getTable();
        $available = Schema::hasTable($table);

        return collect([new DoctorCheckResultData(
            label: 'Capell Extension Cookbook storage',
            passed: $available,
            message: $available ? 'Extension Cookbook storage is available.' : 'Extension Cookbook storage table is missing.',
            remediation: $available ? null : 'Run the Capell Extension Cookbook migrations.',
        )]);
    }

    public static function passed(): bool
    {
        return self::runDiagnostics()->every(static fn (DoctorCheckResultData $result): bool => $result->passed);
    }
}
```

## Console command

The command evaluates diagnostics once, prints each result and returns failure if any result failed. The installed [runtime provider](../src/Providers/ExtensionCookbookServiceProvider.php) calls `$this->commands([ExtensionCookbookDoctorCommand::class])` only in console contexts. The exact command name is `capell:extension-cookbook-doctor`.

Source: [`src/Console/Commands/ExtensionCookbookDoctorCommand.php`](../src/Console/Commands/ExtensionCookbookDoctorCommand.php).

```php
<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Console\Commands;

use Capell\Core\Data\Diagnostics\DoctorCheckResultData;
use Capell\ExtensionCookbook\Health\ExtensionCookbookHealthCheck;
use Illuminate\Console\Command;

final class ExtensionCookbookDoctorCommand extends Command
{
    protected $signature = 'capell:extension-cookbook-doctor';

    protected $description = 'Check Capell Extension Cookbook availability.';

    public function handle(): int
    {
        $checks = ExtensionCookbookHealthCheck::runDiagnostics();

        foreach ($checks as $check) {
            $check->passed ? $this->info($check->message) : $this->error($check->message);
        }

        return $checks->every(static fn (DoctorCheckResultData $check): bool => $check->passed)
            ? self::SUCCESS
            : self::FAILURE;
    }
}
```

## Queued job

This job retries exceptions up to three attempts with a 60-second backoff. The failure callback logs only the exception class. Its handle() discards the diagnostic collection: an unhealthy result does not itself throw, retry or persist an audit. This is a scheduling/diagnostic example, not an alerting pipeline. See the [job tests](../tests/Unit/Jobs/AuditExtensionCookbookHealthJobTest.php).

Source: [`src/Jobs/AuditExtensionCookbookHealthJob.php`](../src/Jobs/AuditExtensionCookbookHealthJob.php).

```php
<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Jobs;

use Capell\ExtensionCookbook\Health\ExtensionCookbookHealthCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

final class AuditExtensionCookbookHealthJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function handle(): void
    {
        ExtensionCookbookHealthCheck::runDiagnostics();
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Extension Cookbook health audit job exhausted its queue retry window.', [
            'exception_class' => $exception instanceof Throwable ? $exception::class : null,
        ]);
    }
}
```

## Content-graph extractor

The extractor returns an empty typed collection for an unsupported model, missing numeric identities or a related Page outside the entry's site. Otherwise it emits one weak, directed RelatesToPage edge to the Core Page identity. This belongs in graph extraction, not public Blade: its explicit Page query checks persisted site ownership without lazy-loading relatedPage. See the [source model](../src/Models/ReferenceEntry.php) and [site-ownership regression](../tests/Feature/ContentGraph/ReferenceEntryContentGraphTest.php).

Source: [`src/ContentGraph/ReferenceEntryContentGraphExtractor.php`](../src/ContentGraph/ReferenceEntryContentGraphExtractor.php).

```php
<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\ContentGraph;

use Capell\Core\Contracts\ContentGraph\ContentGraphExtractor;
use Capell\Core\Data\ContentGraph\ContentGraphEdgeCollectionData;
use Capell\Core\Data\ContentGraph\ContentGraphEdgeData;
use Capell\Core\Data\ContentGraph\ContentGraphNodeData;
use Capell\Core\Enums\ContentGraph\ContentGraphEdgeKind;
use Capell\Core\Enums\ContentGraph\ContentGraphEdgeStrength;
use Capell\Core\Models\Page;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Illuminate\Database\Eloquent\Model;

/** @property int $site_id @property int|null $related_page_id */
final class ReferenceEntryContentGraphExtractor implements ContentGraphExtractor
{
    private const string SOURCE_PACKAGE = 'capell-app/extension-cookbook';

    public static function sourceModel(): string
    {
        return ReferenceEntry::class;
    }

    public function extract(Model $model): ContentGraphEdgeCollectionData
    {
        if (! $model instanceof ReferenceEntry || ! is_numeric($model->site_id) || ! is_numeric($model->related_page_id)) {
            return ContentGraphEdgeCollectionData::make();
        }

        $source = ContentGraphNodeData::fromModel($model);
        $siteId = (int) $model->site_id;
        $pageId = (int) $model->related_page_id;

        if (! Page::query()->whereKey($pageId)->where('site_id', $siteId)->exists()) {
            return ContentGraphEdgeCollectionData::make();
        }

        $target = ContentGraphNodeData::fromModelIdentity(Page::class, $pageId);

        return ContentGraphEdgeCollectionData::make([new ContentGraphEdgeData(
            source: $source,
            target: $target,
            kind: ContentGraphEdgeKind::RelatesToPage,
            strength: ContentGraphEdgeStrength::Weak,
            sourcePackage: self::SOURCE_PACKAGE,
            siteId: $siteId,
        )]);
    }
}
```

## Runtime registration for the job and extractor

The installed [runtime provider](../src/Providers/ExtensionCookbookServiceProvider.php)
owns the remaining wiring. These are exact excerpts from bootInstalledPackage(),
not a standalone provider. The enclosing class imports
`Illuminate\Console\Scheduling\Schedule`,
`Capell\ExtensionCookbook\Console\Commands\ExtensionCookbookDoctorCommand`,
`Capell\ExtensionCookbook\Jobs\AuditExtensionCookbookHealthJob`,
`Capell\Core\Support\ContentGraph\ContentGraphRegistry` and
`Capell\ExtensionCookbook\ContentGraph\ReferenceEntryContentGraphExtractor`.

```php
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
```

The host scheduler must run; an asynchronous queue connection also needs its
worker. The schedule's overlap setting is not a separate queued-job execution
lock. A manifest marker alone does not execute the extractor registration.

## Manifest lifecycle actions

The [manifest actions](../capell.json) register four lifecycle classes, separate
from the extension recipes above. They implement Core's PackageLifecycleAction;
Cookbook does not declare its own interfaces under src/Contracts. These examples
call the shipped actions, rather than registering duplicate implementations.

Use these calls only inside a bootstrapped application with the package's
dependencies, configuration and migrations available. They demonstrate the
action API, not a replacement for the package install/uninstall workflow or its
checks. PackageData construction follows the
[lifecycle regression](../tests/Feature/Lifecycle/PackageLifecycleActionTest.php).
The optional reporter defaults to NullProgressReporter.

### Install

[InstallExtensionCookbookPackageAction](../src/Actions/InstallExtensionCookbookPackageAction.php)
reports completion; it does not run migrations or create example entries.

<!-- example: action install -->

```php
use Capell\Core\Data\PackageData;
use Capell\Core\Enums\PackageTypeEnum;
use Capell\ExtensionCookbook\Actions\InstallExtensionCookbookPackageAction;

$package = new PackageData('capell-app/extension-cookbook', PackageTypeEnum::Plugin);
InstallExtensionCookbookPackageAction::run($package);
```

### Setup

[SetupExtensionCookbookPackageAction](../src/Actions/SetupExtensionCookbookPackageAction.php)
delegates to [EnsureExtensionCookbookExampleAction](../src/Actions/EnsureExtensionCookbookExampleAction.php).
This callable accepts an existing site selected and authorised by its caller;
invoke it only when example-data creation is intended. No hard-coded site ID or
implicit all-sites selection is used. With the required tables present, the
action uses firstOrCreate with that site's ID and the configured example slug.
With no explicit site selection, it creates no entries.

<!-- example: action setup -->

```php
use Capell\Core\Data\PackageData;
use Capell\Core\Enums\PackageTypeEnum;
use Capell\Core\Models\Site;
use Capell\ExtensionCookbook\Actions\SetupExtensionCookbookPackageAction;

function setupCookbookForSite(Site $site): void
{
    $package = new PackageData('capell-app/extension-cookbook', PackageTypeEnum::Plugin);
    SetupExtensionCookbookPackageAction::run($package, ['site_id' => $site->getKey()]);
}
```

### After install

[AfterInstallExtensionCookbookPackageAction](../src/Actions/AfterInstallExtensionCookbookPackageAction.php)
calls the same example-data action as setup. Pass the same explicitly selected
site to target the same site/slug pair. The action does not select a site on the
caller's behalf; the callable below has the same prerequisites as setup.

<!-- example: action afterInstall -->

```php
use Capell\Core\Data\PackageData;
use Capell\Core\Enums\PackageTypeEnum;
use Capell\Core\Models\Site;
use Capell\ExtensionCookbook\Actions\AfterInstallExtensionCookbookPackageAction;

function completeCookbookInstallForSite(Site $site): void
{
    $package = new PackageData('capell-app/extension-cookbook', PackageTypeEnum::Plugin);
    AfterInstallExtensionCookbookPackageAction::run($package, ['site_id' => $site->getKey()]);
}
```

### Uninstall

[UninstallExtensionCookbookPackageAction](../src/Actions/UninstallExtensionCookbookPackageAction.php)
reports completion without deleting package data. Calling it directly does not
remove the installed package or substitute for the host's uninstall workflow.

<!-- example: action uninstall -->

```php
use Capell\Core\Data\PackageData;
use Capell\Core\Enums\PackageTypeEnum;
use Capell\ExtensionCookbook\Actions\UninstallExtensionCookbookPackageAction;

$package = new PackageData('capell-app/extension-cookbook', PackageTypeEnum::Plugin);
UninstallExtensionCookbookPackageAction::run($package);
```

## Deliberately inactive surfaces

Manifest marker classes remain for all 27 current contribution types so the
manifest is auditable. Types without a corresponding public runtime registrar
in this package are explicitly inactive in the overview: sections, page
variations and agent capabilities. Workflow attention is a conditional
contribution that emits an item only with its supported actor and context.

The package avoids broad admin replacement seams and does not introduce
receipt, ordering, public render-data or stable Admin-zone APIs. Core's public
render-data contributor is currently experimental and absent from this
package's released dependency contract; it is a follow-up, not a supported
cookbook recipe.
