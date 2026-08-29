<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        if (! $this->migrator->exists('extension_cookbook.enabled')) {
            $this->migrator->add('extension_cookbook.enabled', true);
        }
    }
};
