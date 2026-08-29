<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Actions;

use Capell\Core\Models\Site;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Illuminate\Support\Facades\Schema;
use Lorisleiva\Actions\Concerns\AsObject;

final class EnsureExtensionCookbookExampleAction
{
    use AsObject;

    /**
     * @param  array<string, mixed>  $arguments
     * @return list<ReferenceEntry>
     */
    public function handle(array $arguments = []): array
    {
        $table = (new ReferenceEntry)->getTable();

        if (! Schema::hasTable($table) || ! Schema::hasTable('sites')) {
            return [];
        }

        $entries = [];

        foreach ($this->resolveSiteIds($arguments) as $siteId) {
            $entries[] = ReferenceEntry::query()->firstOrCreate(
                [
                    'site_id' => $siteId,
                    'slug' => config()->string('extension-cookbook.example.slug'),
                ],
                [
                    'title' => config()->string('extension-cookbook.example.title'),
                    'summary' => config()->string('extension-cookbook.example.summary'),
                    'body' => config()->string('extension-cookbook.example.body'),
                ],
            );
        }

        return $entries;
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return list<int>
     */
    private function resolveSiteIds(array $arguments): array
    {
        $values = [];

        foreach (['--site-id', 'site_id', '--site-ids', 'site_ids', '--sites', 'sites'] as $key) {
            if (! array_key_exists($key, $arguments)) {
                continue;
            }

            $value = $arguments[$key];
            $values = [...$values, ...(is_array($value) ? $value : [$value])];
        }

        $siteIds = [];

        foreach ($values as $value) {
            $siteId = $this->resolveSiteId($value);

            if ($siteId !== null && ! in_array($siteId, $siteIds, true)) {
                $siteIds[] = $siteId;
            }
        }

        return $siteIds;
    }

    private function resolveSiteId(mixed $value): ?int
    {
        if (is_int($value) && $value > 0) {
            return $this->existingSiteId($value);
        }

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        if (ctype_digit($value) && (int) $value > 0) {
            return $this->existingSiteId((int) $value);
        }

        $resolvedSiteId = Site::query()->where('name', $value)->value('id');

        return is_numeric($resolvedSiteId) && (int) $resolvedSiteId > 0
            ? (int) $resolvedSiteId
            : null;
    }

    private function existingSiteId(int $siteId): ?int
    {
        return Site::query()->whereKey($siteId)->exists() ? $siteId : null;
    }
}
