<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Filament\Pages;

use Capell\ExtensionCookbook\Support\Admin\ExtensionCookbookCoverageCatalogue;
use Filament\Pages\Page;
use Filament\Panel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Override;

final class ExtensionCookbookPage extends Page
{
    protected string $view = 'capell-extension-cookbook::filament.pages.extension-cookbook';

    #[Override]
    public static function getNavigationLabel(): string
    {
        return (string) __('capell-extension-cookbook::admin.navigation.extension_cookbook');
    }

    #[Override]
    public static function getNavigationGroup(): string
    {
        return (string) __('capell-extension-cookbook::admin.navigation.group');
    }

    #[Override]
    public static function getSlug(?Panel $panel = null): string
    {
        return 'extension-cookbook';
    }

    #[Override]
    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof Authenticatable
            && Gate::forUser($user)->allows('View:ExtensionCookbook');
    }

    /** @return array<string, list<array{type: string, status: string, purpose: string}>> */
    public function coverage(): array
    {
        return self::canAccess() ? ExtensionCookbookCoverageCatalogue::entries() : [];
    }

    /**
     * @return array<string, list<array{method: string, status: string, reason: string}>>
     */
    public function registrarCoverage(): array
    {
        return self::canAccess() ? ExtensionCookbookCoverageCatalogue::registrarCoverage() : [];
    }
}
