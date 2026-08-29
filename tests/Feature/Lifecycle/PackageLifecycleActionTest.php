<?php

declare(strict_types=1);

use Capell\Core\Contracts\PackageLifecycleAction as PackageLifecycleActionContract;
use Capell\Core\Data\PackageData;
use Capell\Core\Enums\PackageTypeEnum;
use Capell\Core\Support\Packages\PackageLifecycleRunner;
use Capell\ExtensionCookbook\Actions\AfterInstallExtensionCookbookPackageAction;
use Capell\ExtensionCookbook\Actions\InstallExtensionCookbookPackageAction;
use Capell\ExtensionCookbook\Actions\SetupExtensionCookbookPackageAction;
use Capell\ExtensionCookbook\Actions\UninstallExtensionCookbookPackageAction;
use Capell\ExtensionCookbook\Models\ReferenceEntry;
use Illuminate\Support\Facades\Schema;

it('uses phase-specific Actions for the current Core lifecycle contract', function (): void {
    expect([
        InstallExtensionCookbookPackageAction::class,
        SetupExtensionCookbookPackageAction::class,
        AfterInstallExtensionCookbookPackageAction::class,
        UninstallExtensionCookbookPackageAction::class,
    ])->each->toImplement(PackageLifecycleActionContract::class);
});

beforeEach(function (): void {
    config()->set('extension-cookbook.example', [
        'title' => 'Example entry',
        'slug' => 'extension-cookbook-example',
        'summary' => 'Example summary',
        'body' => 'Example body',
    ]);

    Schema::create('sites', function ($table): void {
        $table->id();
        $table->string('name');
        $table->timestamp('deleted_at')->nullable();
    });

    Schema::getConnection()->table('sites')->insert([
        ['id' => 1, 'name' => 'Reference Site'],
        ['id' => 2, 'name' => 'Second Reference Site'],
    ]);

    Schema::create('extension_cookbook_entries', function ($table): void {
        $table->id();
        $table->foreignId('site_id');
        $table->string('title');
        $table->string('slug');
        $table->unique(['site_id', 'slug'], 'extension_cookbook_site_slug_unique');
        $table->text('summary')->nullable();
        $table->longText('body')->nullable();
        $table->boolean('enabled')->default(true);
        $table->unsignedBigInteger('related_page_id')->nullable();
        $table->unsignedBigInteger('blueprint_id')->nullable();
        $table->json('metadata')->nullable();
        $table->timestamps();
    });
});

afterEach(function (): void {
    Schema::dropIfExists('extension_cookbook_entries');
    Schema::dropIfExists('sites');
});

it('keeps setup and after-install idempotent', function (): void {
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
    $runner->run(
        package: $package,
        phase: 'setup',
        command: null,
        actionClass: SetupExtensionCookbookPackageAction::class,
        arguments: ['--sites' => ['Reference Site', 'Second Reference Site']],
    );

    expect(ReferenceEntry::query()->count())->toBe(2)
        ->and(ReferenceEntry::query()->pluck('site_id')->sort()->values()->all())->toBe([1, 2])
        ->and(ReferenceEntry::query()->where('site_id', 1)->value('title'))->toBe('Example entry')
        ->and(ReferenceEntry::query()->where('site_id', 2)->value('title'))->toBe('Example entry');
});

it('keeps install and uninstall non-destructive', function (): void {
    $package = new PackageData('capell-app/extension-cookbook', PackageTypeEnum::Plugin);
    $runner = resolve(PackageLifecycleRunner::class);
    $entry = ReferenceEntry::query()->create([
        'title' => 'Existing entry',
        'slug' => 'existing-entry',
        'site_id' => 1,
        'summary' => 'Existing summary',
        'body' => 'Existing body',
    ]);

    $runner->run(
        package: $package,
        phase: 'install',
        command: null,
        actionClass: InstallExtensionCookbookPackageAction::class,
    );
    $runner->run(
        package: $package,
        phase: 'uninstall',
        command: null,
        actionClass: UninstallExtensionCookbookPackageAction::class,
    );

    $existingEntry = ReferenceEntry::query()->find($entry->getKey());

    if (! $existingEntry instanceof ReferenceEntry) {
        throw new RuntimeException('Expected the existing reference entry to remain.');
    }

    expect(ReferenceEntry::query()->count())->toBe(1)
        ->and($existingEntry->title)->toBe('Existing entry');
});
