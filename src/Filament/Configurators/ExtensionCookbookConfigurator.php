<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Filament\Configurators;

use Capell\Admin\Contracts\ConfiguratorInterface;
use Capell\Admin\Data\Configurators\ConfiguratorContextData;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class ExtensionCookbookConfigurator implements ConfiguratorInterface
{
    public static function getKey(): string
    {
        return 'extension-cookbook';
    }

    public static function getSort(): int
    {
        return 90;
    }

    public static function configure(Schema $schema, ?ConfiguratorContextData $context = null): Schema
    {
        return $schema->components([...$schema->getComponents(), TextInput::make('reference_entry')->label(__('capell-extension-cookbook::admin.configurator.entry'))->maxLength(120)]);
    }
}
