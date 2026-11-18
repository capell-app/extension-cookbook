<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Models;

use Capell\Core\Concerns\HasCapellTable;
use Capell\Core\Contracts\Pageable;
use Capell\Core\Enums\PageOrderEnum;
use Capell\Core\Models\Blueprint;
use Capell\Core\Models\Concerns\HasBlueprint;
use Capell\Core\Models\Concerns\HasPublishDates;
use Capell\Core\Models\Concerns\HasTranslations;
use Capell\Core\Models\Contracts\Blueprintable;
use Capell\Core\Models\Contracts\Publishable;
use Capell\Core\Models\Contracts\Translatable;
use Capell\Core\Models\Language;
use Capell\Core\Models\Layout;
use Capell\Core\Models\Page;
use Capell\Core\Models\PageUrl;
use Capell\Core\Models\Site;
use Capell\ExtensionCookbook\Database\Factories\ReferenceGuideFactory;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

/**
 * Recipe model for a page variation: a record that owns public URLs and
 * translated content in its own table. The title comes from its translations.
 *
 * @property int $id
 * @property int $site_id
 * @property int|null $blueprint_id
 * @property int|null $layout_id
 * @property string $name
 * @property array<string, mixed>|null $meta
 * @property CarbonImmutable|null $visible_from
 * @property CarbonImmutable|null $visible_until
 * @property CarbonImmutable|null $deleted_at
 */
final class ReferenceGuide extends Model implements Blueprintable, Pageable, Publishable, Translatable
{
    use HasBlueprint;
    use HasCapellTable;

    /** @use HasFactory<ReferenceGuideFactory> */
    use HasFactory;

    use HasPublishDates;
    use HasTranslations;
    use SoftDeletes;

    protected $table = 'extension_cookbook_guides';

    protected $fillable = ['site_id', 'blueprint_id', 'layout_id', 'name', 'meta', 'visible_from', 'visible_until'];

    protected static string $factory = ReferenceGuideFactory::class;

    #[Override]
    public static function defaultOrdering(): PageOrderEnum
    {
        return PageOrderEnum::Latest;
    }

    #[Override]
    public static function hasPageHierarchy(): bool
    {
        return false;
    }

    #[Override]
    public static function getDefaultType(?string $group): ?Blueprint
    {
        $query = Blueprint::query()
            ->pageType()
            ->where('key', 'extension-cookbook.entry');

        if ($group !== null) {
            $query->adminResource($group);
        }

        return $query->ordered()->first();
    }

    #[Override]
    public function shouldLogVisit(): bool
    {
        return false;
    }

    #[Override]
    public function getParentUrl(Language $language, bool $fullUrl = false): string
    {
        $url = $fullUrl ? $this->site->getSiteDomainUrl($language) : '/';

        return $url . 'extension-cookbook/guides';
    }

    /** @return BelongsTo<Layout, $this> */
    public function layout(): BelongsTo
    {
        return $this->belongsTo(Layout::class);
    }

    /** @return BelongsTo<Site, Model> */
    #[Override]
    public function site(): BelongsTo
    {
        /** @var BelongsTo<Site, Model> $relation */
        $relation = $this->belongsTo(Site::class);

        return $relation;
    }

    /** @return MorphOne<PageUrl, Model> */
    #[Override]
    public function pageUrl(): MorphOne
    {
        /** @var MorphOne<PageUrl, Model> $relation */
        $relation = $this->morphOne(PageUrl::class, 'pageable');

        return $relation;
    }

    /** @return MorphMany<PageUrl, Model> */
    #[Override]
    public function pageUrls(): MorphMany
    {
        /** @var MorphMany<PageUrl, Model> $relation */
        $relation = $this->morphMany(PageUrl::class, 'pageable');

        return $relation;
    }

    /** @return MorphMany<Page, Model> */
    #[Override]
    public function canonicalPages(): MorphMany
    {
        /** @var MorphMany<Page, Model> $relation */
        $relation = $this->morphMany(
            Page::class,
            'canonical_pageable',
            'meta->canonical_pageable_type',
            'meta->canonical_pageable_id',
        );

        return $relation;
    }

    /** @return MorphTo<Model, Model> */
    #[Override]
    public function canonicalPage(): MorphTo
    {
        /** @var MorphTo<Model, Model> $relation */
        $relation = $this->morphTo(type: 'meta->canonical_pageable_type', id: 'meta->canonical_pageable_id');

        return $relation;
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'site_id' => 'int',
            'blueprint_id' => 'int',
            'meta' => 'array',
            'visible_from' => 'datetime',
            'visible_until' => 'datetime',
        ];
    }
}
