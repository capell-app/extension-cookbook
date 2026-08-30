<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Filament\Resources\ReferenceEntries;

use BackedEnum;
use Capell\Admin\Support\SiteScope;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Override;

final class ReferenceEntryResource extends Resource
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    #[Override]
    public static function getModel(): string
    {
        return ReferenceEntry::class;
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')
                ->label(__('capell-extension-cookbook::admin.fields.title'))
                ->searchable(),
            TextColumn::make('slug')
                ->label(__('capell-extension-cookbook::admin.fields.slug'))
                ->searchable(),
            IconColumn::make('enabled')
                ->label(__('capell-extension-cookbook::admin.fields.enabled'))
                ->boolean(),
        ]);
    }

    #[Override]
    public static function getNavigationLabel(): string
    {
        return (string) __('capell-extension-cookbook::admin.navigation.entries');
    }

    #[Override]
    public static function getNavigationGroup(): string
    {
        return (string) __('capell-extension-cookbook::admin.navigation.group');
    }

    #[Override]
    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof Authenticatable
            && Gate::forUser($user)->allows('viewAny', ReferenceEntry::class);
    }

    #[Override]
    public static function canViewAny(): bool
    {
        return self::canAccess();
    }

    /**
     * @return Builder<ReferenceEntry>
     */
    #[Override]
    public static function getEloquentQuery(): Builder
    {
        /** @var Builder<ReferenceEntry> $query */
        $query = parent::getEloquentQuery();

        return SiteScope::applyForCurrentActor($query, 'site_id', denyWhenMissingActor: true);
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReferenceEntries::route('/'),
        ];
    }
}
