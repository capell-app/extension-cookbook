<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

afterEach(function (): void {
    Schema::dropIfExists('settings');
});

it('does nothing when the settings repository table is absent', function (): void {
    Schema::dropIfExists('settings');

    $migration = require dirname(__DIR__, 3) . '/database/settings/2026_08_29_000001_add_extension_cookbook_settings.php';

    expect(fn (): mixed => $migration->up())->not->toThrow(Throwable::class)
        ->and(Schema::hasTable('settings'))->toBeFalse();
});

it('adds its default setting once when the repository table exists', function (): void {
    Schema::create('settings', function (Blueprint $table): void {
        $table->id();
        $table->string('group');
        $table->string('name');
        $table->boolean('locked')->default(false);
        $table->json('payload');
        $table->timestamps();
        $table->unique(['group', 'name']);
    });

    $migration = require dirname(__DIR__, 3) . '/database/settings/2026_08_29_000001_add_extension_cookbook_settings.php';

    $migration->up();
    $migration->up();

    $payload = DB::table('settings')->where('group', 'extension_cookbook')->where('name', 'enabled')->value('payload');

    if (! is_string($payload)) {
        throw new RuntimeException('Expected a JSON settings payload.');
    }

    expect(DB::table('settings')->where('group', 'extension_cookbook')->where('name', 'enabled')->count())->toBe(1)
        ->and(json_decode($payload, true, 512, JSON_THROW_ON_ERROR))->toBeTrue();
});
