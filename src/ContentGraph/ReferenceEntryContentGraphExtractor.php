<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\ContentGraph;

use Capell\Core\Contracts\ContentGraph\ContentGraphExtractor;
use Capell\Core\Data\ContentGraph\ContentGraphEdgeCollectionData;
use Capell\Core\Data\ContentGraph\ContentGraphEdgeData;
use Capell\Core\Data\ContentGraph\ContentGraphNodeData;
use Capell\Core\Enums\ContentGraph\ContentGraphEdgeKind;
use Capell\Core\Enums\ContentGraph\ContentGraphEdgeStrength;
use Capell\Core\Models\Page;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Illuminate\Database\Eloquent\Model;

/** @property int $site_id @property int|null $related_page_id */
final class ReferenceEntryContentGraphExtractor implements ContentGraphExtractor
{
    private const string SOURCE_PACKAGE = 'capell-app/extension-cookbook';

    public static function sourceModel(): string
    {
        return ReferenceEntry::class;
    }

    public function extract(Model $model): ContentGraphEdgeCollectionData
    {
        if (! $model instanceof ReferenceEntry || ! is_numeric($model->site_id) || ! is_numeric($model->related_page_id)) {
            return ContentGraphEdgeCollectionData::make();
        }

        $source = ContentGraphNodeData::fromModel($model);
        $siteId = (int) $model->site_id;
        $pageId = (int) $model->related_page_id;

        if (! Page::query()->whereKey($pageId)->where('site_id', $siteId)->exists()) {
            return ContentGraphEdgeCollectionData::make();
        }

        $target = ContentGraphNodeData::fromModelIdentity(Page::class, $pageId);

        return ContentGraphEdgeCollectionData::make([new ContentGraphEdgeData(
            source: $source,
            target: $target,
            kind: ContentGraphEdgeKind::RelatesToPage,
            strength: ContentGraphEdgeStrength::Weak,
            sourcePackage: self::SOURCE_PACKAGE,
            siteId: $siteId,
        )]);
    }
}
