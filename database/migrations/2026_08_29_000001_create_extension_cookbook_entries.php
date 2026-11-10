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
        if (TableName::existsAndIsOwned('extension_cookbook_entries', basename(__FILE__, '.php'))) {
            return;
        }

        Schema::create(TableName::resolve('extension_cookbook_entries'), function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained(TableName::resolve('sites'))->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->unique('slug', TableName::namedIndex('extension_cookbook_entries', 'extension_cookbook_entries_slug_unique'));
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();
            $table->boolean('enabled')->default(true)->index();
            $table->unsignedBigInteger('related_page_id')->nullable()->index();
            $table->unsignedBigInteger('blueprint_id')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TableName::resolve('extension_cookbook_entries'));
    }
};
