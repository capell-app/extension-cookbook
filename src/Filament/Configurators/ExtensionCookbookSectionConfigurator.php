<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Filament\Configurators;

use Capell\Admin\Contracts\ConfiguratorInterface;
use Capell\Admin\Data\Configurators\ConfiguratorContextData;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class ExtensionCookbookSectionConfigurator implements ConfiguratorInterface
{
    public static function getKey(): string
    {
        return 'extension-cookbook-note';
    }

    public static function getSort(): int
    {
        return 91;
    }

    public static function configure(Schema $schema, ?ConfiguratorContextData $context = null): Schema
    {
        return $schema->components([
            ...$schema->getComponents(),
            TextInput::make('meta.note')
                ->label(__('capell-extension-cookbook::admin.section.note'))
                ->maxLength(160),
        ]);
    }
}
