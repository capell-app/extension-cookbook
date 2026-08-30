<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Filament\Widgets;

use Capell\ExtensionCookbook\Support\Admin\ExtensionCookbookCoverageCatalogue;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Override;

final class ExtensionCookbookDashboardWidget extends Widget
{
    protected string $view = 'capell-extension-cookbook::filament.widgets.dashboard';

    #[Override]
    public static function canView(): bool
    {
        $user = auth()->user();

        return $user instanceof Authenticatable
            && Gate::forUser($user)->allows('View:ExtensionCookbook');
    }

    /** @return array<string, list<array{type: string, status: string, purpose: string}>> */
    public function coverage(): array
    {
        return self::canView() ? ExtensionCookbookCoverageCatalogue::entries() : [];
    }
}
