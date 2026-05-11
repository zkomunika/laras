<?php

namespace Database\Seeders;

use App\Models\Chapter;
use Illuminate\Database\Seeder;

class ChapterSeeder extends Seeder
{
    public function run(): void
    {
        $chapters = [
            [
                'number' => 1,
                'title' => 'Awal Sang Juru Aksara',
                'slug' => 'awal-sang-juru-aksara',
                'description' => 'Pengenalan dunia Siliwangi, latihan dasar mengetik, teks pendek, dan target WPM rendah.',
                'theme' => 'adaptasi',
                'start_level' => 1,
                'end_level' => 10,
            ],
            [
                'number' => 2,
                'title' => 'Jejak Ladang Aksara',
                'slug' => 'jejak-ladang-aksara',
                'description' => 'Teks mulai lebih panjang, tempo permainan meningkat, dan cerita mulai berkembang.',
                'theme' => 'tantangan-awal',
                'start_level' => 11,
                'end_level' => 20,
            ],
            [
                'number' => 3,
                'title' => 'Pujangga Kerajaan',
                'slug' => 'pujangga-kerajaan',
                'description' => 'Kalimat lebih kompleks dan target akurasi mulai lebih tinggi.',
                'theme' => 'kompleksitas',
                'start_level' => 21,
                'end_level' => 30,
            ],
            [
                'number' => 4,
                'title' => 'Ujian Pajajaran',
                'slug' => 'ujian-pajajaran',
                'description' => 'Waktu lebih terbatas dan kesalahan lebih berpengaruh terhadap skor.',
                'theme' => 'ujian',
                'start_level' => 31,
                'end_level' => 40,
            ],
            [
                'number' => 5,
                'title' => 'Pewaris Siliwangi',
                'slug' => 'pewaris-siliwangi',
                'description' => 'Level tersulit dengan teks panjang, target WPM tinggi, dan boss level akhir.',
                'theme' => 'pewaris',
                'start_level' => 41,
                'end_level' => 50,
            ],
        ];

        foreach ($chapters as $chapter) {
            Chapter::updateOrCreate(
                ['number' => $chapter['number']],
                $chapter
            );
        }
    }
}