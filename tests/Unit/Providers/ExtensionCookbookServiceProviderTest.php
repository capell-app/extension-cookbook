<?php

declare(strict_types=1);

use Capell\Core\Contracts\Extensions\RecordsExtensionContributionReceipt;
use Capell\Core\Data\BlueprintSubjectDescriptorData;
use Capell\Core\Support\BlueprintSubjectRegistry;
use Capell\Core\Support\CapellCoreManager;
use Capell\Core\Support\Metrics\MetricCollectorRegistry;
use Capell\Core\Support\OutboundEventRegistry;
use Capell\Core\Support\Packages\PackageSurfaceRegistrar;
use Capell\Core\Support\Settings\SettingsSchemaRegistry;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Capell\ExtensionCookbook\Settings\ExtensionCookbookSettings;

it('keeps page type and settings schema registration canonical', function (): void {
    $subjects = new BlueprintSubjectRegistry;
    $core = new CapellCoreManager;
    $settings = new SettingsSchemaRegistry;

    (new PackageSurfaceRegistrar(
        $core,
        $settings,
        resolve(MetricCollectorRegistry::class),
        resolve(OutboundEventRegistry::class),
        $subjects,
        resolve(RecordsExtensionContributionReceipt::class),
    ))
        ->blueprintSubject(new BlueprintSubjectDescriptorData(
            'extension-cookbook.entry',
            'Capell Extension Cookbook entry',
            ReferenceEntry::class,
            'capell-app/extension-cookbook',
        ))
        ->settingsClass(ExtensionCookbookSettings::group(), ExtensionCookbookSettings::class);

    expect($subjects->all())->toHaveCount(1)
        ->and($core->getPageTypes())->toHaveCount(1)
        ->and($settings->getSchemas(ExtensionCookbookSettings::group()))->toHaveCount(1);
});
