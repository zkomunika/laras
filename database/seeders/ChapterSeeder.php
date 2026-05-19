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
                'title' => 'Pengenalan Prabu Siliwangi',
                'slug' => 'pengenalan-prabu-siliwangi',
                'description' => 'Pengenalan awal tentang Prabu Siliwangi, Sri Baduga Maharaja, Pakuan, dan posisi tokoh dalam tradisi Sunda.',
                'theme' => 'pengenalan-sejarah',
                'start_level' => 1,
                'end_level' => 10,
            ],
            [
                'number' => 2,
                'title' => 'Asal-Usul dan Silsilah',
                'slug' => 'asal-usul-dan-silsilah',
                'description' => 'Pembahasan asal-usul, garis keturunan, sumber naskah, serta batas pembuktian historis tentang Sri Baduga dan Siliwangi.',
                'theme' => 'silsilah',
                'start_level' => 11,
                'end_level' => 20,
            ],
            [
                'number' => 3,
                'title' => 'Naik Takhta dan Kejayaan',
                'slug' => 'naik-takhta-dan-kejayaan',
                'description' => 'Penelusuran masa naik takhta Sri Baduga, pusat kekuasaan Pakuan Pajajaran, dan konsolidasi Kerajaan Sunda.',
                'theme' => 'kejayaan',
                'start_level' => 21,
                'end_level' => 30,
            ],
            [
                'number' => 4,
                'title' => 'Konteks Politik dan Kemunduran',
                'slug' => 'konteks-politik-dan-kemunduran',
                'description' => 'Pembacaan konteks politik pesisir, tekanan Cirebon-Demak-Banten, serta perbedaan antara wafatnya Sri Baduga dan runtuhnya Kerajaan Sunda.',
                'theme' => 'kemunduran',
                'start_level' => 31,
                'end_level' => 40,
            ],
            [
                'number' => 5,
                'title' => 'Akhir Hayat dan Legenda',
                'slug' => 'akhir-hayat-dan-legenda',
                'description' => 'Pemisahan antara fakta historis akhir Sri Baduga, legenda moksa, simbol macan putih, dan makna Siliwangi dalam ingatan kolektif Sunda.',
                'theme' => 'historiografi-kritis',
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
