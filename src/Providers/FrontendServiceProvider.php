<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Providers;

use Capell\Core\Contracts\Extensions\RecordsExtensionContributionReceipt;
use Capell\Core\Enums\ExtensionContributionType;
use Capell\Core\Enums\PresentationLoadingStrategy;
use Capell\Core\Support\Packages\RegistersInstalledRuntime;
use Capell\ExtensionCookbook\Support\Frontend\ExtensionCookbookFrontendComponentContributor;
use Capell\ExtensionCookbook\Support\Frontend\ExtensionCookbookRenderHook;
use Capell\ExtensionCookbook\Widget\ExtensionCookbookWidget;
use Capell\ExtensionCookbook\Widget\ExtensionCookbookWidgetInputData;
use Capell\ExtensionCookbook\Widget\ExtensionCookbookWidgetRenderData;
use Capell\Frontend\Contracts\FrontendComponentContributor;
use Capell\Frontend\Data\Assets\FrontendResourceData;
use Capell\Frontend\Data\Assets\FrontendResourceGroupData;
use Capell\Frontend\Data\Assets\PublicResourceSourceData;
use Capell\Frontend\Enums\FrontendComponentTarget;
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
    use RegistersInstalledRuntime;

    #[Override]
    public function register(): void
    {
        $this->registerInstalledRuntime(ExtensionCookbookServiceProvider::$packageName, 'frontend');
    }

    protected function bootInstalledRuntime(): void
    {
        $base = dirname(__DIR__, 2);
        $this->loadRoutesFrom($base . '/routes/web.php');
        $this->loadViewsFrom($base . '/resources/views', 'capell-extension-cookbook');
        $this->loadTranslationsFrom($base . '/resources/lang', 'capell-extension-cookbook');
        Blade::anonymousComponentNamespace('Capell\\ExtensionCookbook\\View\\Components', 'extension-cookbook');

        $this->app->scoped(ExtensionCookbookFrontendComponentContributor::class);
        $this->app->tag(ExtensionCookbookFrontendComponentContributor::class, FrontendComponentContributor::TAG);
        // Activate only our aliases so late installation preserves other packages' overrides.
        $contributor = $this->app->make(ExtensionCookbookFrontendComponentContributor::class);
        foreach ($contributor->components() as $component) {
            if ($component->target !== FrontendComponentTarget::Blade) {
                continue;
            }

            Blade::component($component->component, $component->name);
            $this->app->make(RecordsExtensionContributionReceipt::class)->recordContributionFromSource(
                ExtensionContributionType::FrontendComponent,
                $component->name,
                $component->component,
                $contributor::class,
                'frontend',
            );
        }

        $this->callAfterResolving(FrontendResourceRegistry::class, static function (FrontendResourceRegistry $registry): void {
            $registry->register(new FrontendResourceGroupData(
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
        });
        $this->app->make(FrontendResourceRegistry::class);

        $this->callAfterResolving(WidgetExtensionRegistrar::class, static function (WidgetExtensionRegistrar $registrar): void {
            $registrar->register(new WidgetExtensionDefinitionData(
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
        });
        $this->app->make(WidgetExtensionRegistrar::class);

        $this->callAfterResolving(FrontendHookRegistrar::class, static function (FrontendHookRegistrar $registrar): void {
            $registrar->contribute(
                location: RenderHookLocation::BodyEnd,
                extension: new ExtensionCookbookRenderHook,
                owner: ExtensionCookbookServiceProvider::$packageName,
                key: 'extension-cookbook.route-note',
                target: 'extension-cookbook',
                cacheSafe: true,
            );
        });
        $this->app->make(FrontendHookRegistrar::class);
    }
}
