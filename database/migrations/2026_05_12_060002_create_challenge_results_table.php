<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenge_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_room_id')->constrained('challenge_rooms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete();
            $table->longText('typed_text')->nullable();
            $table->decimal('wpm', 8, 2)->default(0);
            $table->decimal('accuracy', 5, 2)->default(0);
            $table->unsignedInteger('mistakes')->default(0);
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('duration_ms')->default(0);
            $table->unsignedInteger('rank')->nullable();
            $table->boolean('completed')->default(false);
            $table->json('failed_rules')->nullable();
            $table->timestamps();

            $table->unique(['challenge_room_id', 'user_id']);
            $table->index(['challenge_room_id', 'rank']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_results');
    }
};
