<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Filament\Resources\ReferenceEntries\Pages;

use Capell\ExtensionCookbook\Filament\Resources\ReferenceEntries\ReferenceEntryResource;
use Filament\Resources\Pages\ListRecords;

final class ListReferenceEntries extends ListRecords
{
    protected static string $resource = ReferenceEntryResource::class;
}
