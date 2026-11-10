<?php

declare(strict_types=1);

use Capell\Core\Support\TableName;
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
        if (! Schema::hasTable(TableName::resolve(self::TABLE))
            || ! Schema::hasColumn(TableName::resolve(self::TABLE), 'site_id')
            || ! Schema::hasColumn(TableName::resolve(self::TABLE), 'slug')) {
            return;
        }

        foreach ($this->globalSlugIndexes() as $index) {
            Schema::table(TableName::resolve(self::TABLE), function (Blueprint $table) use ($index): void {
                $table->dropUnique($index);
            });
        }

        if (! Schema::hasIndex(TableName::resolve(self::TABLE), TableName::namedIndex('extension_cookbook_entries', self::UNIQUE_INDEX))) {
            Schema::table(TableName::resolve(self::TABLE), function (Blueprint $table): void {
                $table->unique(['site_id', 'slug'], TableName::namedIndex('extension_cookbook_entries', self::UNIQUE_INDEX));
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable(TableName::resolve(self::TABLE))
            || ! Schema::hasColumn(TableName::resolve(self::TABLE), 'site_id')
            || ! Schema::hasColumn(TableName::resolve(self::TABLE), 'slug')) {
            return;
        }

        if (DB::table(TableName::resolve(self::TABLE))
            ->select('slug')
            ->whereNotNull('slug')
            ->groupBy('slug')
            ->havingRaw('COUNT(*) > 1')
            ->exists()) {
            throw new RuntimeException(
                'Extension Cookbook cannot restore global slug uniqueness while duplicate site-scoped slugs exist.',
            );
        }

        if (Schema::hasIndex(TableName::resolve(self::TABLE), TableName::namedIndex('extension_cookbook_entries', self::UNIQUE_INDEX))) {
            if (! $this->hasIndexForColumn('site_id', TableName::namedIndex('extension_cookbook_entries', self::UNIQUE_INDEX))) {
                Schema::table(TableName::resolve(self::TABLE), function (Blueprint $table): void {
                    $table->index('site_id', TableName::namedIndex('extension_cookbook_entries', 'extension_cookbook_entries_site_id_index'));
                });
            }

            Schema::table(TableName::resolve(self::TABLE), function (Blueprint $table): void {
                $table->dropUnique(TableName::namedIndex('extension_cookbook_entries', self::UNIQUE_INDEX));
            });
        }

        if ($this->globalSlugIndexes() === []) {
            Schema::table(TableName::resolve(self::TABLE), function (Blueprint $table): void {
                $table->unique('slug', TableName::namedIndex('extension_cookbook_entries', self::LEGACY_INDEX));
            });
        }
    }

    /** @return list<string> */
    private function globalSlugIndexes(): array
    {
        $indexes = [];

        foreach (Schema::getIndexes(TableName::resolve(self::TABLE)) as $index) {
            if (! (bool) ($index['unique'] ?? false) || ($index['columns'] ?? []) !== ['slug']) {
                continue;
            }

            $name = $index['name'] ?? null;

            if (is_string($name) && $name !== '') {
                $indexes[] = $name;
            }
        }

        return $indexes;
    }

    private function hasIndexForColumn(string $column, ?string $except = null): bool
    {
        foreach (Schema::getIndexes(TableName::resolve(self::TABLE)) as $index) {
            if ($except !== null && ($index['name'] ?? null) === $except) {
                continue;
            }

            if (($index['columns'] ?? []) !== [] && ($index['columns'][0] ?? null) === $column) {
                return true;
            }
        }

        return false;
    }
};
