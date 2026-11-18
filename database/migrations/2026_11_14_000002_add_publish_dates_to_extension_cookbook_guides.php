<?php

declare(strict_types=1);

use Capell\Core\Support\TableName;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = TableName::resolve('extension_cookbook_guides');

        if (! Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($table): void {
            if (! Schema::hasColumn($table, 'visible_from')) {
                $blueprint->dateTime('visible_from')->nullable()->index();
            }

            if (! Schema::hasColumn($table, 'visible_until')) {
                $blueprint->dateTime('visible_until')->nullable();
            }

            if (! Schema::hasColumn($table, 'deleted_at')) {
                $blueprint->softDeletes();
            }
        });
    }

    public function down(): void
    {
        $table = TableName::resolve('extension_cookbook_guides');

        if (! Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($table): void {
            foreach (['visible_from', 'visible_until', 'deleted_at'] as $column) {
                if (Schema::hasColumn($table, $column)) {
                    $blueprint->dropColumn($column);
                }
            }
        });
    }
};
