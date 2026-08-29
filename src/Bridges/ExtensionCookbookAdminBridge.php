<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Bridges;

use Capell\Admin\Contracts\Bridges\AdminBridge;
use Capell\Admin\Data\Bridges\AdminBridgeContextData;
use Capell\Admin\Enums\DashboardEnum;
use Capell\Admin\Enums\SchemaExtenderEnum;
use Capell\Admin\Support\Bridges\AdminBridgeRegistrar;
use Capell\ExtensionCookbook\Filament\Configurators\ExtensionCookbookConfigurator;
use Capell\ExtensionCookbook\Filament\Extenders\ExtensionCookbookAdminActionExtender;
use Capell\ExtensionCookbook\Filament\Extenders\ExtensionCookbookPageSchemaExtender;
use Capell\ExtensionCookbook\Filament\Pages\ExtensionCookbookPage;
use Capell\ExtensionCookbook\Filament\Resources\ReferenceEntries\ReferenceEntryResource;
use Capell\ExtensionCookbook\Filament\Widgets\ExtensionCookbookDashboardWidget;
use Capell\ExtensionCookbook\Filament\Widgets\ExtensionCookbookOverviewStat;

final class ExtensionCookbookAdminBridge implements AdminBridge
{
    public function isEnabled(AdminBridgeContextData $context): bool
    {
        return true;
    }

    public function register(AdminBridgeRegistrar $registrar, AdminBridgeContextData $context): void
    {
        $registrar->page(ExtensionCookbookPage::class);
        $registrar->resource(
            ReferenceEntryResource::class,
            (string) __('capell-extension-cookbook::admin.navigation.group'),
        );
        $registrar->extensionDashboardFilamentWidget(ExtensionCookbookDashboardWidget::class);
        $registrar->filamentDashboardWidget(ExtensionCookbookOverviewStat::class, DashboardEnum::Main);
        $registrar->resourceHeaderActionExtender(ExtensionCookbookAdminActionExtender::class);
        $registrar->schemaExtender(ExtensionCookbookPageSchemaExtender::class, SchemaExtenderEnum::Page->value);
        $registrar->configurator(ExtensionCookbookConfigurator::class, 'blueprint', ExtensionCookbookConfigurator::getKey());
        $registrar->extensionPage('capell-app/extension-cookbook', ExtensionCookbookPage::class);
    }
}
