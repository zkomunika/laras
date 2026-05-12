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
            // Bab 1: Akar Sang Pamanah Rasa
            [1, 1, 'Nama Asli', 'Nama asli Prabu Siliwangi adalah Raden Pamanah Rasa.', 16, 80, 120, 20],
            [1, 2, 'Panggilan', 'Beliau juga sering dipanggil dengan nama Jayadewata.', 17, 80, 120, 20],
            [1, 3, 'Kelahiran', 'Jayadewata lahir dan besar di Keraton Galuh Kawali.', 18, 81, 115, 19],
            [1, 4, 'Sang Ayahanda', 'Ayahandanya adalah raja Galuh bernama Dewa Niskala.', 19, 82, 115, 19],
            [1, 5, 'Sang Kakek', 'Kakeknya adalah raja besar Niskala Wastu Kancana.', 20, 82, 110, 18],
            [1, 6, 'Pendidikan', 'Sejak kecil ia dididik untuk menjadi seorang ksatria.', 21, 83, 110, 18],
            [1, 7, 'Ilmu Tata Negara', 'Pamanah Rasa tekun belajar ilmu tata negara.', 22, 84, 105, 17],
            [1, 8, 'Sang Ksatria', 'Ia tumbuh menjadi pemuda yang gagah dan pemberani.', 23, 84, 105, 17],
            [1, 9, 'Kasih Sayang', 'Kasih sayang pada rakyat sudah tumbuh sejak dini.', 24, 85, 100, 16],
            [1, 10, 'Boss: Kepemimpinan', 'Sifat kepemimpinan Jayadewata terlihat sejak remaja.', 26, 86, 95, 15],

            // Bab 2: Pengembaraan dan Ikatan
            [2, 11, 'Masa Muda', 'Masa muda Pamanah Rasa banyak dihabiskan dengan mengembara ke berbagai wilayah.', 28, 86, 95, 15],
            [2, 12, 'Tujuan', 'Perjalanan jauh ini bertujuan untuk mencari ilmu dan menambah pengalaman hidup.', 29, 86, 92, 15],
            [2, 13, 'Sindangkasih', 'Di daerah Sindangkasih, ia bertemu dengan putri cantik bernama Nyi Ambetkasih.', 30, 87, 92, 14],
            [2, 14, 'Pernikahan Pertama', 'Pernikahan pertamanya adalah dengan putri dari pemimpin daerah Ki Gedeng Sindangkasih.', 31, 87, 90, 14],
            [2, 15, 'Karawang', 'Dalam perjalanannya, ia juga singgah di daerah Karawang yang cukup ramai.', 32, 88, 90, 14],
            [2, 16, 'Nyi Subang Larang', 'Di sana Pamanah Rasa terpikat oleh Nyi Subang Larang yang salehah.', 33, 88, 88, 13],
            [2, 17, 'Murid Syekh Quro', 'Nyi Subang Larang merupakan murid kesayangan dari ulama besar Syekh Quro.', 34, 89, 88, 13],
            [2, 18, 'Kelahiran Putra', 'Dari pernikahan ini lahirlah pangeran yang diberi nama Raden Walangsungsang.', 35, 89, 85, 12],
            [2, 19, 'Kelahiran Putri', 'Lahir pula seorang putri yang cantik jelita bernama Nyi Rara Santang.', 36, 90, 85, 12],
            [2, 20, 'Boss: Keraton', 'Ia juga mempersunting Nyi Kentring Manik dari lingkungan Keraton Kerajaan Sunda.', 38, 90, 82, 11],

            // Bab 3: Penyatuan Dua Mahkota
            [3, 21, 'Kembali Pulang', 'Setelah melalui perjalanan panjang yang penuh makna, Pamanah Rasa akhirnya kembali ke Keraton Galuh.', 40, 90, 82, 11],
            [3, 22, 'Takhta Galuh', 'Prabu Dewa Niskala kemudian menyerahkan takhta Kerajaan Galuh kepada putranya yang gagah berani tersebut.', 41, 90, 80, 11],
            [3, 23, 'Takhta Sunda', 'Di sisi lain, mertuanya yaitu Prabu Susuktunggal, juga menyerahkan takhta Kerajaan Sunda kepadanya.', 42, 91, 80, 10],
            [3, 24, 'Dua Kerajaan', 'Peristiwa bersejarah ini berhasil menyatukan kembali dua kerajaan besar yang sempat terpisah sangat lama.', 43, 91, 78, 10],
            [3, 25, 'Harapan Baru', 'Penyatuan dua mahkota ini membawa harapan baru bagi kemakmuran seluruh rakyat di tanah Pasundan.', 44, 92, 78, 10],
            [3, 26, 'Penobatan', 'Pada saat penobatan, ia resmi menggunakan gelar kebesaran yaitu Sri Baduga Maharaja.', 45, 92, 76, 9],
            [3, 27, 'Gelar Lengkap', 'Gelar lengkapnya adalah Sri Baduga Maharaja Ratu Haji di Pakuan Pajajaran Sri Sang Ratu Dewata.', 46, 92, 76, 9],
            [3, 28, 'Ibu Kota', 'Setelah resmi memegang kekuasaan tertinggi, pusat pemerintahan kemudian dipindahkan ke wilayah Pakuan Pajajaran.', 47, 93, 74, 9],
            [3, 29, 'Tujuan Pemindahan', 'Pemindahan ibu kota ini bertujuan untuk memperkuat strategi pertahanan, keamanan, dan juga pusat perdagangan.', 48, 93, 74, 8],
            [3, 30, 'Boss: Awal Kejayaan', 'Kepemimpinan Sri Baduga Maharaja ini menandai awal mula masa kejayaan yang gilang-gemilang bagi Pajajaran.', 50, 94, 72, 8],

            // Bab 4: Kejayaan Pakuan Pajajaran
            [4, 31, 'Parit Pertahanan', 'Untuk memperkuat sistem pertahanan ibu kota Pakuan Pajajaran, Sri Baduga Maharaja memerintahkan pembuatan parit pertahanan yang dalam.', 52, 94, 72, 8],
            [4, 32, 'Jalan Raya', 'Selain membangun parit pertahanan, beliau juga memperteguh akses jalan raya yang menghubungkan pusat kerajaan dengan wilayah pesisir.', 53, 94, 70, 8],
            [4, 33, 'Danau Megah', 'Salah satu pencapaian infrastruktur terbesarnya adalah pembuatan sebuah danau buatan megah yang diberi nama Sanghyang Talaga Rena Mahawijaya.', 54, 94, 70, 7],
            [4, 34, 'Fungsi Danau', 'Danau buatan yang indah tersebut berfungsi penting sebagai sumber air bersih, sarana irigasi pertanian, dan tempat rekreasi kerajaan.', 55, 95, 68, 7],
            [4, 35, 'Kekuatan Militer', 'Sri Baduga Maharaja juga berfokus membangun kekuatan militer yang tangguh dengan melatih pasukan telik sandi dan prajurit berkuda.', 56, 95, 68, 7],
            [4, 36, 'Kesejahteraan', 'Di bawah pemerintahannya yang bijaksana, rakyat senantiasa menikmati hasil panen yang melimpah, sistem pajak yang adil, dan keamanan.', 57, 95, 66, 6],
            [4, 37, 'Toleransi Beragama', 'Meskipun memegang teguh ajaran leluhur, sang raja sangat menjunjung tinggi nilai-nilai toleransi beragama bagi seluruh penduduk Pajajaran.', 58, 95, 66, 6],
            [4, 38, 'Kebebasan', 'Beliau memberikan kebebasan penuh kepada umat Islam, termasuk keturunan Nyi Subang Larang, untuk beribadah dan menyebarkan ajaran agamanya.', 59, 95, 64, 6],
            [4, 39, 'Keadilan', 'Keadilan senantiasa ditegakkan tanpa pandang bulu melalui aturan kerajaan yang ketat, memastikan rakyat merasa aman dari segala penindasan.', 60, 96, 64, 5],
            [4, 40, 'Boss: Era Keemasan', 'Masa pemerintahan Sri Baduga Maharaja ini dikenang abadi oleh sejarah sebagai era keemasan, kejayaan, dan kemakmuran Kerajaan Pajajaran.', 62, 96, 62, 5],

            // Bab 5: Warisan Sang Baduga
            [5, 41, 'Titik Purna', 'Masa pemerintahan gemilang Sri Baduga Maharaja akhirnya mencapai titik purnanya ketika beliau wafat pada tahun 1521 Masehi.', 64, 96, 62, 5],
            [5, 42, 'Penerus Takhta', 'Sebelum mangkat, takhta kerajaan diwariskan kepada putra mahkota kebanggaannya yang bernama Prabu Surawisesa untuk meneruskan kepemimpinan Pakuan Pajajaran.', 65, 96, 60, 5],
            [5, 43, 'Tugu Peringatan', 'Sebagai bentuk penghormatan dan pengabdian kepada mendiang ayahandanya, Prabu Surawisesa memerintahkan pembuatan sebuah tugu peringatan yang sangat bersejarah.', 66, 97, 60, 4],
            [5, 44, 'Prasasti Batutulis', 'Tugu peringatan yang diukir menggunakan aksara dan bahasa Sunda Kuno tersebut kini dikenal luas oleh masyarakat sebagai Prasasti Batutulis.', 67, 97, 58, 4],
            [5, 45, 'Lokasi Prasasti', 'Prasasti penting peninggalan abad keenam belas ini ditemukan dan terpelihara di wilayah Kelurahan Batutulis, Kecamatan Bogor Selatan, Kota Bogor.', 68, 97, 58, 4],
            [5, 46, 'Kutipan Prasasti 1', '"Semoga selamat, ini tanda peringatan Prabu Ratu almarhum, dinobatkan dia dengan nama Prabu Guru Dewataprana," demikian kutipan pembuka prasasti tersebut.', 70, 97, 56, 4],
            [5, 47, 'Kutipan Prasasti 2', 'Prasasti itu juga mengabadikan gelar kebesaran: "Dinobatkan dia menjadi Sri Baduga Maharaja Ratu Haji di Pakuan Pajajaran Sri Sang Ratu Dewata."', 72, 98, 56, 3],
            [5, 48, 'Kutipan Prasasti 3', '"Dialah yang membuat parit pertahanan Pakuan, dia putra Rahiyang Dewa Niskala yang mendiang di Gunatiga, cucu Rahiyang Niskala Wastu Kancana."', 74, 98, 54, 3],
            [5, 49, 'Kutipan Prasasti 4', '"Dialah yang membuat tanda peringatan gunung-gunungan, memperkeras jalan raya dengan batu, membuat hutan Samida, dan membuat Sanghyang Talaga Rena Mahawijaya."', 76, 98, 54, 3],
            [5, 50, 'Boss: Abadi', 'Meskipun raganya telah tiada, jejak langkah dan keteladanan Sri Baduga Maharaja Prabu Siliwangi akan terus hidup abadi dalam sanubari urang Sunda.', 80, 98, 52, 2],
        ];

        foreach ($levels as $item) {
            [$chapterNumber, $levelNumber, $title, $targetText, $oldWpm, $minAccuracy, $oldTimeLimit, $maxMistakes] = $item;

            $chapter = Chapter::where('number', $chapterNumber)->firstOrFail();

            $isBossLevel = $levelNumber % 10 === 0;

            // Recalculate reasonable WPM and Time Limit based on difficulty and string length.
            // A word is ~5 characters.
            $wordCount = max(1, strlen($targetText) / 5);
            
            // Scaled WPM from 15 (Level 1) to 65 (Level 50)
            $targetWpm = (int) round(15 + (($levelNumber - 1) / 49) * 50);
            
            // Time limit based on how long it should take at target WPM + buffer
            $expectedMinutes = $wordCount / max(1, $targetWpm);
            $bufferSeconds = 30 - (($levelNumber - 1) / 49) * 20; // 30s buffer at lvl 1, 10s buffer at lvl 50
            $timeLimit = (int) round(($expectedMinutes * 60) + $bufferSeconds);

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
            1 => "BAB 1 mengajarkan dasar mengetik melalui huruf kecil, kata pendek, dan kalimat sederhana pada level {$levelNumber}.",
            2 => "BAB 2 mulai memperkenalkan kalimat lebih panjang, huruf kapital, koma, titik, dan tanda tanya pada level {$levelNumber}.",
            3 => "BAB 3 membawa pemain ke naskah kerajaan dengan struktur kalimat lebih kompleks dan tanda baca yang lebih beragam pada level {$levelNumber}.",
            4 => "BAB 4 menjadi Ujian Pajajaran dengan angka, waktu, persen, kode, dan simbol teknis pada level {$levelNumber}.",
            5 => "BAB 5 adalah tahap Pewaris Siliwangi dengan kombinasi huruf kapital, angka, simbol, tekanan waktu, dan akurasi tinggi pada level {$levelNumber}.",
            default => "Level {$levelNumber} dalam perjalanan LARAS.",
        };
    }
}