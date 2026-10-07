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
use Override;

final class FrontendServiceProvider extends ServiceProvider
{
    private bool $installedRuntimeBooted = false;

    #[Override]
    public function register(): void
    {
        $this->booted($this->bootInstalledRuntime(...));
    }

    private function bootInstalledRuntime(): void
    {
        if ($this->installedRuntimeBooted || ! CapellCore::isPackageInstalled(ExtensionCookbookServiceProvider::$packageName)) {
            return;
        }

        $base = dirname(__DIR__, 2);
        $this->loadRoutesFrom($base . '/routes/web.php');
        $this->loadViewsFrom($base . '/resources/views', 'capell-extension-cookbook');
        $this->loadTranslationsFrom($base . '/resources/lang', 'capell-extension-cookbook');
        Blade::anonymousComponentNamespace('Capell\\ExtensionCookbook\\View\\Components', 'extension-cookbook');

        // Frontend discovery has already finished during an in-process installation.
        Blade::component('capell-extension-cookbook::widget.extension-cookbook', 'extension-cookbook.widget');

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
        $this->installedRuntimeBooted = true;
    }
}
