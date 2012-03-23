<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Actions;

use Capell\ExtensionCookbook\Data\ReferenceEntryData;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsFake;
use Lorisleiva\Actions\Concerns\AsObject;

final class ReadReferenceEntriesAction
{
    use AsFake;
    use AsObject;

    /** @return Collection<int, ReferenceEntryData> */
    public function handle(?int $siteId = null): Collection
    {
        if ($siteId === null) {
            return collect();
        }

        return ReferenceEntry::query()->enabled()->where('site_id', $siteId)->orderBy('title')->get()
            ->map(static fn (ReferenceEntry $entry): ReferenceEntryData => new ReferenceEntryData(
                title: (string) $entry->title,
                slug: (string) $entry->slug,
                summary: $entry->summary === null ? null : (string) $entry->summary,
                siteId: (int) $entry->site_id,
            ))->values();
    }
}
