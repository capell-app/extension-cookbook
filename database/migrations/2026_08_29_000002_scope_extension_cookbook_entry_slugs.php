<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const string TABLE = 'extension_cookbook_entries';

    private const string UNIQUE_INDEX = 'extension_cookbook_site_slug_unique';

    private const string LEGACY_INDEX = 'extension_cookbook_entries_slug_unique';

    public function up(): void
    {
        if (! Schema::hasTable(self::TABLE)
            || ! Schema::hasColumn(self::TABLE, 'site_id')
            || ! Schema::hasColumn(self::TABLE, 'slug')) {
            return;
        }

        foreach ($this->globalSlugIndexes() as $index) {
            Schema::table(self::TABLE, function (Blueprint $table) use ($index): void {
                $table->dropUnique($index);
            });
        }

        if (! Schema::hasIndex(self::TABLE, self::UNIQUE_INDEX)) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                $table->unique(['site_id', 'slug'], self::UNIQUE_INDEX);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable(self::TABLE)
            || ! Schema::hasColumn(self::TABLE, 'site_id')
            || ! Schema::hasColumn(self::TABLE, 'slug')) {
            return;
        }

        if (DB::table(self::TABLE)
            ->select('slug')
            ->whereNotNull('slug')
            ->groupBy('slug')
            ->havingRaw('COUNT(*) > 1')
            ->exists()) {
            throw new RuntimeException(
                'Extension Cookbook cannot restore global slug uniqueness while duplicate site-scoped slugs exist.',
            );
        }

        if (Schema::hasIndex(self::TABLE, self::UNIQUE_INDEX)) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                $table->dropUnique(self::UNIQUE_INDEX);
            });
        }

        if ($this->globalSlugIndexes() === []) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                $table->unique('slug', self::LEGACY_INDEX);
            });
        }
    }

    /** @return list<string> */
    private function globalSlugIndexes(): array
    {
        $indexes = [];

        foreach (Schema::getIndexes(self::TABLE) as $index) {
            if ((bool) ($index['unique'] ?? false) !== true || ($index['columns'] ?? []) !== ['slug']) {
                continue;
            }

            $name = $index['name'] ?? null;

            if (is_string($name) && $name !== '') {
                $indexes[] = $name;
            }
        }

        return $indexes;
    }
};
