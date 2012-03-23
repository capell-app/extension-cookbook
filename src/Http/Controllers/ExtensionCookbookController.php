<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Http\Controllers;

use Capell\ExtensionCookbook\Actions\ReadReferenceEntriesAction;
use Capell\ExtensionCookbook\Data\ReferenceEntryData;
use Capell\Frontend\Facades\Frontend;
use Illuminate\Contracts\View\View;

final class ExtensionCookbookController
{
    public function __invoke(): View
    {
        $siteId = Frontend::site()?->getKey();
        $siteId = is_numeric($siteId) ? (int) $siteId : null;

        return view('capell-extension-cookbook::frontend.index', [
            'examples' => ReadReferenceEntriesAction::run($siteId)->map(static fn (ReferenceEntryData $entry): string => $entry->title)->all(),
        ]);
    }
}
