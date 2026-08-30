<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Filament\Settings;

use Capell\Admin\Filament\Contracts\HasSchema;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ExtensionCookbookSettingsSchema implements HasSchema
{
    public static function make(Schema $schema): array
    {
        return [Section::make(__('capell-extension-cookbook::admin.settings.title'))->schema([
            Toggle::make('enabled')->label(__('capell-extension-cookbook::admin.settings.enabled'))->default(true),
        ])];
    }
}
