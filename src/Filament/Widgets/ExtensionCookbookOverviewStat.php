<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Filament\Widgets;

use Capell\Admin\Support\SiteScope;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Override;

final class ExtensionCookbookOverviewStat extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    #[Override]
    public static function canView(): bool
    {
        $user = auth()->user();

        return $user instanceof Authenticatable
            && Gate::forUser($user)->allows('View:ExtensionCookbook');
    }

    /** @return array<int, Stat> */
    protected function getStats(): array
    {
        if (! self::canView()) {
            return [];
        }

        $total = SiteScope::applyForCurrentActor(ReferenceEntry::query(), 'site_id', denyWhenMissingActor: true)->count();
        $enabled = SiteScope::applyForCurrentActor(ReferenceEntry::query(), 'site_id', denyWhenMissingActor: true)
            ->where('enabled', true)
            ->count();

        return [
            Stat::make(
                __('capell-extension-cookbook::admin.stats.enabled'),
                sprintf('%d/%d', $enabled, $total),
            ),
        ];
    }
}
