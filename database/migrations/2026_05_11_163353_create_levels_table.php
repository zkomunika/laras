<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('level_number')->unique();
            $table->string('title');
            $table->text('story_text')->nullable();
            $table->text('target_text');
            $table->unsignedSmallInteger('target_wpm')->default(20);
            $table->decimal('min_accuracy', 5, 2)->default(80);
            $table->unsignedInteger('time_limit_seconds')->nullable();
            $table->unsignedInteger('max_mistakes')->nullable();
            $table->boolean('is_boss_level')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['chapter_id', 'level_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};