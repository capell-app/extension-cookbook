<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Bridges;

use Capell\ContentSections\Contracts\SectionDefinitionProvider;
use Capell\ContentSections\Data\SectionDefinitionData;
use Capell\Core\Facades\CapellCore;
use Capell\ExtensionCookbook\Filament\Configurators\ExtensionCookbookSectionConfigurator;
use Override;

/**
 * Recipe: contribute a content section. Content Sections is optional, so the
 * service provider only tags this class when Content Sections is available.
 */
final class ExtensionCookbookSectionDefinitionProvider implements SectionDefinitionProvider
{
    public const string KEY = 'extension-cookbook-note';

    /** @return list<SectionDefinitionData> */
    #[Override]
    public function definitions(): array
    {
        if (! CapellCore::isPackageAvailable('capell-app/content-sections')) {
            return [];
        }

        return [
            new SectionDefinitionData(
                key: self::KEY,
                label: (string) __('capell-extension-cookbook::admin.section.label'),
                description: (string) __('capell-extension-cookbook::admin.section.description'),
                icon: 'heroicon-o-book-open',
                group: 'main',
                configurator: ExtensionCookbookSectionConfigurator::class,
                component: 'capell-extension-cookbook::section.reference-note',
                defaults: [],
            ),
        ];
    }
}
