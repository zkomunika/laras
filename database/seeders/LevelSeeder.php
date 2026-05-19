<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            // BAB 1 — pengenalan tokoh, kalimat pendek, ritme mengetik ringan
            [1, 1, 'sri baduga', 'sri baduga dikenal sebagai raja sunda di pakuan', 16, 80, 120, 20],
            [1, 2, 'nama siliwangi', 'prabu siliwangi adalah nama populer dalam tradisi sunda', 17, 80, 120, 20],
            [1, 3, 'pakuan', 'pakuan menjadi pusat penting dalam sejarah kerajaan sunda', 18, 81, 115, 19],
            [1, 4, 'pajajaran', 'pajajaran sering merujuk pada pusat kerajaan di pakuan', 19, 82, 115, 19],
            [1, 5, 'jayadewata', 'sri baduga juga dikenal dengan nama jayadewata', 20, 82, 110, 18],
            [1, 6, 'tradisi sunda', 'cerita siliwangi hidup kuat dalam ingatan masyarakat sunda', 21, 83, 110, 18],
            [1, 7, 'fakta awal', 'Sri Baduga dikenal sebagai raja Sunda di Pakuan.', 22, 84, 105, 17],
            [1, 8, 'tokoh sejarah', 'Prabu Siliwangi sering dikaitkan dengan Sri Baduga.', 23, 84, 105, 17],
            [1, 9, 'sejarah lisan', 'Dalam tradisi Sunda, nama Siliwangi menjadi simbol raja ideal.', 24, 85, 100, 16],
            [1, 10, 'boss fakta awal', 'Boss Awal: bedakan fakta sejarah dari legenda Sunda.', 26, 86, 95, 15],

            // BAB 2 — asal-usul, silsilah, dan keterbatasan data historis
            [2, 11, 'kelahiran', 'Data kelahiran Prabu Siliwangi tidak disebut jelas dalam prasasti.', 28, 86, 95, 15],
            [2, 12, 'kawali galuh', 'Tradisi menyebut Kawali dan Galuh sebagai ruang awal kisah Siliwangi.', 29, 86, 92, 15],
            [2, 13, 'silsilah', 'Silsilah Sri Baduga dikaitkan dengan garis raja Sunda dan Galuh.', 30, 87, 92, 14],
            [2, 14, 'niskala wastu', 'Niskala Wastu Kancana sering disebut dalam rekonstruksi dinasti Sunda.', 31, 87, 90, 14],
            [2, 15, 'surawisesa', 'Prasasti Batu Tulis menyebut Surawisesa sebagai penerus Sri Baduga.', 32, 88, 90, 14],
            [2, 16, 'sumber naskah', 'Carita Parahyangan menjadi salah satu bahan penting sejarah Sunda.', 33, 88, 88, 13],
            [2, 17, 'batas data', 'Tahun lahir Sri Baduga belum dapat dipastikan secara prasastis.', 34, 89, 88, 13],
            [2, 18, 'kritik sumber', 'Sejarawan bertanya, "mana yang fakta dan mana yang tradisi?"', 35, 89, 85, 12],
            [2, 19, 'warisan galuh', 'Hubungan Sunda dan Galuh membantu menjelaskan posisi Sri Baduga.', 36, 90, 85, 12],
            [2, 20, 'boss silsilah', 'Boss Silsilah: pahami asal-usul, sumber, dan batas pembuktiannya!', 38, 90, 82, 11],

            // BAB 3 — naik takhta, pemerintahan, dan masa kejayaan
            [3, 21, 'naik takhta', 'Sri Baduga diperkirakan naik takhta sekitar tahun 1482.', 40, 90, 82, 11],
            [3, 22, 'masa kuasa', 'Masa pemerintahannya sering dihitung hingga tahun 1521.', 41, 90, 80, 11],
            [3, 23, 'batu tulis', 'Prasasti Batu Tulis menyebut Sri Baduga sebagai raja besar Sunda.', 42, 91, 80, 10],
            [3, 24, 'pakuan kuat', 'Pakuan Pajajaran dipahami sebagai pusat politik Kerajaan Sunda.', 43, 91, 78, 10],
            [3, 25, 'masa jaya', 'Masa Sri Baduga kerap dipahami sebagai puncak konsolidasi Sunda.', 44, 92, 78, 10],
            [3, 26, 'politik sunda', 'Kekuatan Sunda bertumpu pada pusat pedalaman dan jaringan pesisir.', 45, 92, 76, 9],
            [3, 27, 'ekonomi lada', 'Perdagangan lada dan hasil bumi memperkuat posisi ekonomi Sunda.', 46, 92, 76, 9],
            [3, 28, 'ibu kota', 'Pakuan bukan sekadar tempat tinggal raja, tetapi pusat pemerintahan.', 47, 93, 74, 9],
            [3, 29, 'raja ideal', 'Citra Siliwangi sebagai raja adil dibentuk oleh sejarah dan legenda.', 48, 93, 74, 8],
            [3, 30, 'boss pakuan', 'Boss Pakuan berkata: "kuasai tahun, tokoh, dan bukti sejarah!"', 50, 94, 72, 8],

            // BAB 4 — pencapaian, sumber tertulis, angka, dan konteks politik
            [4, 31, 'periode kuasa', 'Periode 1482-1521 sering dipakai untuk membaca masa Sri Baduga.', 52, 94, 72, 8],
            [4, 32, 'prasasti', 'Prasasti Batu Tulis dibuat oleh Surawisesa sekitar 12 tahun setelah wafatnya Sri Baduga.', 53, 94, 70, 8],
            [4, 33, 'pemerintahan', 'Sri Baduga memerintah kurang lebih 39 tahun menurut rekonstruksi sejarah.', 54, 94, 70, 7],
            [4, 34, 'sunda portugis', 'Sesudah Sri Baduga, Sunda berhadapan dengan perubahan politik pesisir.', 55, 95, 68, 7],
            [4, 35, 'sunda kelapa', 'Sunda Kelapa menjadi simpul dagang penting sebelum dikuasai kekuatan pesisir.', 56, 95, 68, 7],
            [4, 36, 'pesisir', 'Cirebon, Demak, dan Banten memberi tekanan besar pada wilayah Sunda.', 57, 95, 66, 6],
            [4, 37, 'keruntuhan', 'Kerajaan Sunda runtuh sekitar 1579, jauh setelah wafatnya Sri Baduga.', 58, 95, 66, 6],
            [4, 38, 'analisis sebab', 'Keruntuhan Sunda dipengaruhi tekanan eksternal dan pelemahan internal.', 59, 95, 64, 6],
            [4, 39, 'data dan mitos', 'Analisis sejarah harus memisahkan data, tafsir, dan mitos secara hati-hati.', 60, 96, 64, 5],
            [4, 40, 'boss konteks', 'Boss Konteks: 1482, 1521, 1579; pahami urutan sejarahnya!', 62, 96, 62, 5],

            // BAB 5 — akhir hidup, legenda, historiografi kritis, dan sintesis akhir
            [5, 41, 'akhir hayat', 'Sri Baduga wafat sekitar 1521 dan disebut dalam Prasasti Batu Tulis.', 64, 96, 62, 5],
            [5, 42, 'kremasi', 'Bukti sejarah mengarah pada praktik perabuan jenazah sesuai tradisi Hindu.', 65, 96, 60, 5],
            [5, 43, 'legenda moksa', 'Kisah moksa Siliwangi adalah legenda, bukan laporan sejarah langsung.', 66, 97, 60, 4],
            [5, 44, 'macan putih', 'Simbol macan putih memperkuat posisi Siliwangi dalam imajinasi Sunda.', 67, 97, 58, 4],
            [5, 45, 'gelar kolektif', 'Sebagian tafsir melihat Siliwangi sebagai gelar kolektif, bukan satu orang.', 68, 97, 58, 4],
            [5, 46, 'historiografi', 'Historiografi kritis menuntut pemisahan antara prasasti, naskah, dan tutur lisan.', 70, 97, 56, 4],
            [5, 47, 'sintesis tokoh', 'Sri Baduga adalah inti historis, sedangkan Siliwangi juga hidup sebagai simbol budaya.', 72, 98, 56, 3],
            [5, 48, 'warisan sunda', 'Warisan Siliwangi bertahan karena sejarah, legenda, dan identitas Sunda saling bertaut.', 74, 98, 54, 3],
            [5, 49, 'uji akhir', 'Pemain harus memahami bahwa Pajajaran, Sunda, dan Pakuan tidak selalu identik.', 76, 98, 54, 3],
            [5, 50, 'boss akhir siliwangi', 'BOSS FINAL #50: Prabu Siliwangi berada di batas sejarah, legenda, dan ingatan kolektif Sunda.', 80, 98, 52, 2],
        ];

        foreach ($levels as $item) {
            [$chapterNumber, $levelNumber, $title, $targetText, $targetWpm, $minAccuracy, $timeLimit, $maxMistakes] = $item;

            $chapter = Chapter::where('number', $chapterNumber)->firstOrFail();

            $isBossLevel = $levelNumber % 10 === 0;

            Level::updateOrCreate(
                ['level_number' => $levelNumber],
                [
                    'chapter_id' => $chapter->id,
                    'title' => $isBossLevel
                        ? 'Boss Level ' . $levelNumber . ' — ' . ucwords($title)
                        : 'Level ' . $levelNumber . ' — ' . ucwords($title),
                    'story_text' => $this->storyText($chapterNumber, $levelNumber),
                    'target_text' => $targetText,
                    'target_wpm' => $targetWpm,
                    'min_accuracy' => $minAccuracy,
                    'time_limit_seconds' => $timeLimit,
                    'max_mistakes' => $maxMistakes,
                    'is_boss_level' => $isBossLevel,
                    'sort_order' => $levelNumber,
                ]
            );
        }
    }

    private function storyText(int $chapterNumber, int $levelNumber): string
    {
        return match ($chapterNumber) {
            1 => "BAB 1 memperkenalkan Prabu Siliwangi dan Sri Baduga melalui kalimat pendek yang mudah diketik pada level {$levelNumber}.",
            2 => "BAB 2 membahas asal-usul, silsilah, dan keterbatasan bukti sejarah tentang tokoh Siliwangi pada level {$levelNumber}.",
            3 => "BAB 3 menelusuri naik takhta, pusat kekuasaan Pakuan, dan masa kejayaan Kerajaan Sunda pada level {$levelNumber}.",
            4 => "BAB 4 mengajak pemain membaca konteks politik, prasasti, angka tahun, dan proses kemunduran Sunda pada level {$levelNumber}.",
            5 => "BAB 5 membedakan akhir historis Sri Baduga, legenda moksa, dan makna Siliwangi dalam ingatan kolektif Sunda pada level {$levelNumber}.",
            default => "Level {$levelNumber} dalam perjalanan LARAS.",
        };
    }
}
