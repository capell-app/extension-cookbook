<?php

declare(strict_types=1);

use Capell\ExtensionCookbook\Models\ReferenceGuide;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

afterEach(function (): void {
    Schema::dropIfExists('extension_cookbook_guides');
    Schema::dropIfExists('layouts');
    Schema::dropIfExists('sites');
});

/**
 * The admin Layouts list counts the rows of every registered page variation that
 * reference a layout, so a variation table without layout_id fails every page that
 * renders the layout selector (the page editor returned HTTP 500).
 */
it('gives reference guides the layout_id column the layout usage count reads', function (): void {
    Schema::create('sites', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
    });
    Schema::create('layouts', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
    });

    foreach ([
        '2026_11_14_000001_create_extension_cookbook_guides',
        '2026_11_14_000002_add_publish_dates_to_extension_cookbook_guides',
        '2026_11_15_000001_add_layout_to_extension_cookbook_guides',
    ] as $migrationName) {
        (require dirname(__DIR__, 3) . '/database/migrations/' . $migrationName . '.php')->up();
    }

    $table = (new ReferenceGuide)->getTable();

    expect(Schema::hasColumn($table, 'layout_id'))->toBeTrue()
        ->and(DB::select(
            'select count(*) as total from ' . $table . ' where ' . $table . '.layout_id = ?',
            [1],
        ))->toHaveCount(1);
});

it('can run the migration twice and roll it back', function (): void {
    Schema::create('sites', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
    });
    Schema::create('layouts', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
    });

    (require dirname(__DIR__, 3) . '/database/migrations/2026_11_14_000001_create_extension_cookbook_guides.php')->up();
    $migration = require dirname(__DIR__, 3) . '/database/migrations/2026_11_15_000001_add_layout_to_extension_cookbook_guides.php';

    $migration->up();
    $migration->up();
    expect(Schema::hasColumn('extension_cookbook_guides', 'layout_id'))->toBeTrue();

    $migration->down();
    expect(Schema::hasColumn('extension_cookbook_guides', 'layout_id'))->toBeFalse();
});
