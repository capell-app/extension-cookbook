<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Actions;

use Capell\Core\Models\Page;
use Capell\ExtensionCookbook\Data\ReferenceEntryData;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use InvalidArgumentException;
use Lorisleiva\Actions\Concerns\AsAction;

final class CreateReferenceEntryAction
{
    use AsAction;

    public function handle(ReferenceEntryData $data): ReferenceEntry
    {
        if ($data->siteId === null) {
            throw new InvalidArgumentException('A site is required for a reference entry.');
        }

        if ($data->relatedPageId !== null && ! Page::query()
            ->whereKey($data->relatedPageId)
            ->where('site_id', $data->siteId)
            ->exists()) {
            throw new InvalidArgumentException('The related page must belong to the entry site.');
        }

        return ReferenceEntry::query()->create([
            'title' => $data->title, 'slug' => $data->slug, 'summary' => $data->summary,
            'body' => $data->body, 'enabled' => $data->enabled, 'related_page_id' => $data->relatedPageId,
            'site_id' => $data->siteId,
            'blueprint_id' => $data->blueprintId, 'metadata' => $data->metadata,
        ]);
    }
}
