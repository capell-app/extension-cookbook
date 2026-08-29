<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('extension_cookbook_entries')) {
            return;
        }
        Schema::create('extension_cookbook_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->unique('slug', 'extension_cookbook_entries_slug_unique');
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
        Schema::dropIfExists('extension_cookbook_entries');
    }
};
