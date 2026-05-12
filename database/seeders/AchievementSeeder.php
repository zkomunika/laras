<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            [
                'code' => 'LRS_ACCOUNT_READY',
                'name' => 'Pengguna Baru',
                'description' => 'Berhasil membuat akun LARAS.',
                'icon' => '🌱',
                'condition_type' => 'account_created',
                'condition_value' => 0,
                'sort_order' => 1,
            ],
            [
                'code' => 'LRS_FIRST_LEVEL',
                'name' => 'Langkah Pertama',
                'description' => 'Selesaikan minimal satu level Story Mode.',
                'icon' => '⚡',
                'condition_type' => 'completed_levels',
                'condition_value' => 1,
                'sort_order' => 2,
            ],
            [
                'code' => 'LRS_CHAPTER_ONE',
                'name' => 'Penjaga Bab Awal',
                'description' => 'Selesaikan seluruh level pada BAB 1.',
                'icon' => '🛡️',
                'condition_type' => 'completed_chapter',
                'condition_value' => 1,
                'sort_order' => 3,
            ],
            [
                'code' => 'LRS_TEN_LEVELS',
                'name' => 'Juru Aksara Tangguh',
                'description' => 'Selesaikan 10 level Story Mode.',
                'icon' => '📜',
                'condition_type' => 'completed_levels',
                'condition_value' => 10,
                'sort_order' => 4,
            ],
            [
                'code' => 'LRS_ZERO_MISTAKE',
                'name' => 'Tanpa Salah',
                'description' => 'Selesaikan satu level tanpa kesalahan.',
                'icon' => '🎯',
                'condition_type' => 'zero_mistake_levels',
                'condition_value' => 1,
                'sort_order' => 5,
            ],
            [
                'code' => 'LRS_FAST_TYPER',
                'name' => 'Cepat dan Tepat',
                'description' => 'Capai best WPM minimal 40.',
                'icon' => '🔥',
                'condition_type' => 'best_wpm',
                'condition_value' => 40,
                'sort_order' => 6,
            ],
            [
                'code' => 'LRS_STAR_COLLECTOR',
                'name' => 'Pengumpul Bintang',
                'description' => 'Kumpulkan minimal 15 bintang dari Story Mode.',
                'icon' => '⭐',
                'condition_type' => 'total_stars',
                'condition_value' => 15,
                'sort_order' => 7,
            ],
            [
                'code' => 'LRS_FINAL_LEVEL',
                'name' => 'Pewaris Siliwangi',
                'description' => 'Selesaikan level 50 sebagai penutup perjalanan.',
                'icon' => '👑',
                'condition_type' => 'level_completed',
                'condition_value' => 50,
                'sort_order' => 8,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::updateOrCreate(
                ['code' => $achievement['code']],
                $achievement + ['is_active' => true]
            );
        }
    }
}
