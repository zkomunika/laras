<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('level_id')->constrained()->cascadeOnDelete();
            $table->decimal('best_wpm', 8, 2)->default(0);
            $table->decimal('best_accuracy', 5, 2)->default(0);
            $table->unsignedInteger('best_score')->default(0);
            $table->unsignedTinyInteger('best_stars')->default(0);
            $table->unsignedInteger('attempts_count')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'level_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_progress');
    }
};