<?php

declare(strict_types=1);

namespace Capell\ExtensionCookbook\Models;

use Capell\Core\Models\Concerns\HasBlueprint;
use Capell\Core\Models\Contracts\Blueprintable;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $title
 * @property string $slug
 * @property string|null $summary
 * @property int $site_id
 * @property int|null $related_page_id
 */
final class ReferenceEntry extends Model implements Blueprintable
{
    use HasBlueprint;

    protected $table = 'extension_cookbook_entries';

    protected $fillable = ['title', 'slug', 'summary', 'body', 'enabled', 'site_id', 'related_page_id', 'blueprint_id', 'metadata'];

    /** @return BelongsTo<Site, $this> */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /** @return BelongsTo<Page, $this> */
    public function relatedPage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'related_page_id');
    }

    /** @param Builder<self> $query */
    public function scopeEnabled(Builder $query): void
    {
        $query->where('enabled', true);
    }

    protected function casts(): array
    {
        return ['enabled' => 'bool', 'site_id' => 'int', 'related_page_id' => 'int', 'blueprint_id' => 'int', 'metadata' => 'array'];
    }
}
