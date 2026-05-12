<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenge_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_room_id')->constrained('challenge_rooms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_ready')->default(false);
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->unsignedInteger('typed_chars')->default(0);
            $table->unsignedInteger('mistakes')->default(0);
            $table->decimal('wpm', 8, 2)->default(0);
            $table->decimal('accuracy', 5, 2)->default(0);
            $table->string('status', 20)->default('waiting');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->unique(['challenge_room_id', 'user_id']);
            $table->index(['challenge_room_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_participants');
    }
};
