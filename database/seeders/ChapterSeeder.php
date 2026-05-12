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
                'title' => 'Akar Sang Pamanah Rasa',
                'slug' => 'akar-sang-pamanah-rasa',
                'description' => 'Kelahiran dan Masa Kecil: Menceritakan latar belakang kelahirannya dengan nama asli Raden Pamanah Rasa (atau Jayadewata). Berfokus pada silsilahnya sebagai putra dari Prabu Dewa Niskala dari Kerajaan Galuh dan cucu dari Niskala Wastu Kancana.',
                'theme' => 'kelahiran',
                'start_level' => 1,
                'end_level' => 10,
            ],
            [
                'number' => 2,
                'title' => 'Pengembaraan dan Ikatan',
                'slug' => 'pengembaraan-dan-ikatan',
                'description' => 'Masa Muda dan Keluarga: Menceritakan masa mudanya yang dihabiskan untuk mengembara dan memperluas relasi. Membahas pernikahan strategis dan bersejarahnya.',
                'theme' => 'pengembaraan',
                'start_level' => 11,
                'end_level' => 20,
            ],
            [
                'number' => 3,
                'title' => 'Penyatuan Dua Mahkota',
                'slug' => 'penyatuan-dua-mahkota',
                'description' => 'Naik Takhta: Fokus pada proses epik penyatuan kembali Kerajaan Sunda dan Kerajaan Galuh. Dinobatkan dengan gelar Sri Baduga Maharaja.',
                'theme' => 'takhta',
                'start_level' => 21,
                'end_level' => 30,
            ],
            [
                'number' => 4,
                'title' => 'Kejayaan Pakuan Pajajaran',
                'slug' => 'kejayaan-pakuan-pajajaran',
                'description' => 'Masa Keemasan: Menceritakan pencapaian terbesarnya sebagai raja. Berisi sejarah pembangunan infrastruktur, militer, kesejahteraan dan toleransi beragama.',
                'theme' => 'kejayaan',
                'start_level' => 31,
                'end_level' => 40,
            ],
            [
                'number' => 5,
                'title' => 'Warisan Sang Baduga',
                'slug' => 'warisan-sang-baduga',
                'description' => 'Akhir Hayat dan Prasasti: Menjelaskan akhir masa pemerintahannya (wafat 1521 M), penerusan takhta, dan peninggalan sejarah abadi berupa Prasasti Batutulis.',
                'theme' => 'warisan',
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