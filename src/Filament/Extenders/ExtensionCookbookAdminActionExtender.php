<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Filament\Extenders;

use Capell\Admin\Contracts\Extenders\ResourceHeaderActionExtender;
use Capell\ExtensionCookbook\Filament\Pages\ExtensionCookbookPage;
use Capell\ExtensionCookbook\Filament\Resources\ReferenceEntries\Pages\ListReferenceEntries;
use Filament\Actions\Action;

final class ExtensionCookbookAdminActionExtender implements ResourceHeaderActionExtender
{
    public function supports(string $pageClass): bool
    {
        return $pageClass === ListReferenceEntries::class;
    }

    /** @return array<int, Action> */
    public function actions(): array
    {
        return [
            Action::make('open-extension-cookbook')
                ->label(__('capell-extension-cookbook::admin.actions.open'))
                ->url(fn (): string => ExtensionCookbookPage::getUrl())
                ->icon('heroicon-o-squares-2x2'),
        ];
    }
}
