<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenge_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete();
            $table->string('name', 120);
            $table->string('type', 20)->default('public');
            $table->string('code', 12)->nullable()->unique();
            $table->unsignedTinyInteger('capacity')->default(2);
            $table->string('status', 20)->default('waiting');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['master_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_rooms');
    }
};
