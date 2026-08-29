<?php

declare(strict_types=1);

use Capell\Core\Data\PackageData;
use Capell\Core\Enums\PackageTypeEnum;
use Capell\Core\Support\Packages\PackageLifecycleRunner;
use Capell\ExtensionCookbook\Actions\AfterInstallExtensionCookbookPackageAction;
use Capell\ExtensionCookbook\Actions\SetupExtensionCookbookPackageAction;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

afterEach(function (): void {
    Schema::dropIfExists('extension_cookbook_entries');
    Schema::dropIfExists('sites');
});

it('upgrades a global slug index to site-scoped uniqueness for lifecycle setup', function (): void {
    Schema::create('sites', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
        $table->timestamp('deleted_at')->nullable();
    });

    DB::table('sites')->insert([
        ['id' => 1, 'name' => 'Reference Site'],
        ['id' => 2, 'name' => 'Second Reference Site'],
    ]);

    Schema::create('extension_cookbook_entries', function (Blueprint $table): void {
        $table->id();
        $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
        $table->string('title');
        $table->string('slug');
        $table->unique('slug', 'extension_cookbook_entries_slug_unique');
        $table->text('summary')->nullable();
        $table->longText('body')->nullable();
        $table->boolean('enabled')->default(true);
        $table->unsignedBigInteger('related_page_id')->nullable();
        $table->unsignedBigInteger('blueprint_id')->nullable();
        $table->json('metadata')->nullable();
        $table->timestamps();
    });

    $migration = require dirname(__DIR__, 3) . '/database/migrations/2026_08_29_000002_scope_extension_cookbook_entry_slugs.php';

    $migration->up();
    $migration->up();

    expect(Schema::hasIndex('extension_cookbook_entries', 'extension_cookbook_entries_slug_unique'))->toBeFalse()
        ->and(Schema::hasIndex('extension_cookbook_entries', 'extension_cookbook_site_slug_unique'))->toBeTrue();

    config()->set('extension-cookbook.example', [
        'title' => 'Example entry',
        'slug' => 'extension-cookbook-example',
        'summary' => 'Example summary',
        'body' => 'Example body',
    ]);

    $package = new PackageData('capell-app/extension-cookbook', PackageTypeEnum::Plugin);
    $runner = resolve(PackageLifecycleRunner::class);

    $runner->run(
        package: $package,
        phase: 'setup',
        command: null,
        actionClass: SetupExtensionCookbookPackageAction::class,
        arguments: ['--sites' => ['Reference Site', 'Second Reference Site']],
    );
    $runner->run(
        package: $package,
        phase: 'after-install',
        command: null,
        actionClass: AfterInstallExtensionCookbookPackageAction::class,
        arguments: ['--site-ids' => [1, 2]],
    );

    expect(DB::table('extension_cookbook_entries')->count())->toBe(2)
        ->and(DB::table('extension_cookbook_entries')->pluck('site_id')->sort()->values()->all())->toBe([1, 2]);

    expect(fn (): mixed => $migration->down())
        ->toThrow(RuntimeException::class)
        ->and(Schema::hasIndex('extension_cookbook_entries', 'extension_cookbook_site_slug_unique'))->toBeTrue();

    // The legacy global constraint cannot represent duplicate cross-site rows;
    // remove the second fixture before asserting a reversible schema rollback.
    DB::table('extension_cookbook_entries')->where('site_id', 2)->delete();

    $migration->down();
    $migration->down();

    expect(Schema::hasIndex('extension_cookbook_entries', 'extension_cookbook_site_slug_unique'))->toBeFalse();
    expect(Schema::hasIndex('extension_cookbook_entries', 'extension_cookbook_entries_slug_unique'))->toBeTrue();
});
