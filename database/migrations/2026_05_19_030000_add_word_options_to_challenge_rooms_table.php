<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('challenge_rooms', function (Blueprint $table) {
            $table->unsignedSmallInteger('word_count')->nullable()->after('capacity');
            $table->unsignedSmallInteger('time_limit_seconds')->nullable()->after('word_count');
            $table->longText('target_text')->nullable()->after('time_limit_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('challenge_rooms', function (Blueprint $table) {
            $table->dropColumn(['target_text', 'time_limit_seconds', 'word_count']);
        });
    }
};
