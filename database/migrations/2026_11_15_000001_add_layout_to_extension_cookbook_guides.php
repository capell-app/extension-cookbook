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

        if (! Schema::hasTable($table) || Schema::hasColumn($table, 'layout_id')) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->foreignId('layout_id')->nullable()->after('blueprint_id')->constrained(TableName::resolve('layouts'))->nullOnDelete();
        });
    }

    public function down(): void
    {
        $table = TableName::resolve('extension_cookbook_guides');

        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'layout_id')) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->dropConstrainedForeignId('layout_id');
        });
    }
};
