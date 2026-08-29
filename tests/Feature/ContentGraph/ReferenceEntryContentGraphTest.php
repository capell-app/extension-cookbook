<?php

declare(strict_types=1);

use Capell\Core\Enums\ContentGraph\ContentGraphEdgeKind;
use Capell\Core\Enums\ContentGraph\ContentGraphEdgeStrength;
use Capell\Core\Models\Page;
use Capell\ExtensionCookbook\Actions\CreateReferenceEntryAction;
use Capell\ExtensionCookbook\ContentGraph\ReferenceEntryContentGraphExtractor;
use Capell\ExtensionCookbook\Data\ReferenceEntryData;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

function prepareReferenceEntryGraphSchema(): void
{
    if (! Schema::hasTable('sites')) {
        Schema::create('sites', static function (Blueprint $table): void {
            $table->id();
        });
    }

    if (! Schema::hasTable('pages')) {
        Schema::create('pages', static function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('site_id');
            $table->softDeletes();
        });
    }

    if (! Schema::hasTable('extension_cookbook_entries')) {
        $migration = require dirname(__DIR__, 3) . '/database/migrations/2026_08_29_000001_create_extension_cookbook_entries.php';
        $migration->up();
    }
}

it('emits a weak page relation only within the persisted entry site', function (): void {
    prepareReferenceEntryGraphSchema();

    DB::table('sites')->insertOrIgnore([['id' => 501], ['id' => 502]]);
    DB::table('pages')->insertOrIgnore([['id' => 601, 'site_id' => 501], ['id' => 602, 'site_id' => 502]]);

    $entry = ReferenceEntry::query()->create([
        'title' => 'Public example',
        'slug' => 'public-example',
        'site_id' => 501,
        'related_page_id' => 601,
    ]);

    $graph = (new ReferenceEntryContentGraphExtractor)->extract($entry);

    expect($graph->edges)->toHaveCount(1)
        ->and($graph->edges[0]->source->modelType)->toBe(ReferenceEntry::class)
        ->and($graph->edges[0]->source->modelId)->toBe($entry->getKey())
        ->and($graph->edges[0]->target->modelType)->toBe(Page::class)
        ->and($graph->edges[0]->target->modelId)->toBe(601)
        ->and($graph->edges[0]->kind)->toBe(ContentGraphEdgeKind::RelatesToPage)
        ->and($graph->edges[0]->strength)->toBe(ContentGraphEdgeStrength::Weak)
        ->and($graph->edges[0]->siteId)->toBe(501);

    $crossSiteEntry = ReferenceEntry::query()->create([
        'title' => 'Cross site example',
        'slug' => 'cross-site-example',
        'site_id' => 501,
        'related_page_id' => 602,
    ]);

    expect((new ReferenceEntryContentGraphExtractor)->extract($crossSiteEntry)->edges)->toBe([]);
});

it('returns no graph edges when an entry has no related page', function (): void {
    prepareReferenceEntryGraphSchema();
    DB::table('sites')->insertOrIgnore(['id' => 503]);
    $entry = ReferenceEntry::query()->create([
        'title' => 'No page example',
        'slug' => 'no-page-example',
        'site_id' => 503,
    ]);

    expect((new ReferenceEntryContentGraphExtractor)->extract($entry)->edges)->toBe([]);
});

it('rejects a related page from another site at the write boundary', function (): void {
    prepareReferenceEntryGraphSchema();
    DB::table('sites')->insertOrIgnore([['id' => 504], ['id' => 505]]);
    DB::table('pages')->insertOrIgnore(['id' => 604, 'site_id' => 505]);

    expect(fn (): ReferenceEntry => (new CreateReferenceEntryAction)->handle(new ReferenceEntryData(
        title: 'Invalid cross site example',
        slug: 'invalid-cross-site-example',
        siteId: 504,
        relatedPageId: 604,
    )))->toThrow(InvalidArgumentException::class);
});
