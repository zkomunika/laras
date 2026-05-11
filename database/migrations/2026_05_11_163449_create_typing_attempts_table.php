<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('typing_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('level_id')->constrained()->cascadeOnDelete();
            $table->longText('typed_text')->nullable();
            $table->unsignedInteger('correct_chars')->default(0);
            $table->unsignedInteger('wrong_chars')->default(0);
            $table->unsignedInteger('mistakes')->default(0);
            $table->decimal('accuracy', 5, 2)->default(0);
            $table->decimal('wpm', 8, 2)->default(0);
            $table->unsignedInteger('score')->default(0);
            $table->unsignedTinyInteger('stars')->default(0);
            $table->boolean('completed')->default(false);
            $table->unsignedInteger('duration_ms')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'level_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('typing_attempts');
    }
};