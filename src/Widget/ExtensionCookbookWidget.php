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
