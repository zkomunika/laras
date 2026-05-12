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
            // BAB 1 — huruf kecil, kata pendek, tanpa simbol berat
            [1, 1, 'jejak huruf kecil', 'aksara pagi mulai hidup di ladang siliwangi', 16, 80, 120, 20],
            [1, 2, 'napas pertama', 'juru aksara belajar menata kata dengan tenang', 17, 80, 120, 20],
            [1, 3, 'ladang sunyi', 'daun muda bergerak pelan saat pena mulai menulis', 18, 81, 115, 19],
            [1, 4, 'ritme jemari', 'setiap huruf membawa langkah kecil menuju cerita', 19, 82, 115, 19],
            [1, 5, 'kata sederhana', 'sang murid menjaga fokus agar tulisan tetap rapi', 20, 82, 110, 18],
            [1, 6, 'baris pertama', 'latihan yang sabar membuat jemari semakin percaya', 21, 83, 110, 18],
            [1, 7, 'kalimat terang', 'Aksara pertama mulai hidup di bawah cahaya pagi.', 22, 84, 105, 17],
            [1, 8, 'huruf kapital awal', 'Sang Juru Aksara menulis pesan untuk penjaga ladang.', 23, 84, 105, 17],
            [1, 9, 'tanda baca ringan', 'Di ladang pagi, pena tua bergerak dengan hati-hati.', 24, 85, 100, 16],
            [1, 10, 'boss awal aksara', 'Prasasti tua berkata: jagalah aksara, jagalah cerita.', 26, 86, 95, 15],

            // BAB 2 — kalimat lebih panjang, kapital, koma, titik, tanda tanya
            [2, 11, 'jalan ladang', 'Jejak Ladang Aksara membawa murid menuju hutan pengetahuan.', 28, 86, 95, 15],
            [2, 12, 'daun lontar', 'Angin pagi menyapu daun lontar, lalu membuka pesan rahasia.', 29, 86, 92, 15],
            [2, 13, 'tempo naik', 'Setiap kalimat menuntut fokus, tempo, dan ketelitian jemari.', 30, 87, 92, 14],
            [2, 14, 'persimpangan', 'Di persimpangan jalan, sang penulis bertanya: lanjut atau menyerah?', 31, 87, 90, 14],
            [2, 15, 'cerita terbuka', 'Naskah kerajaan mulai terbuka, tetapi maknanya belum lengkap.', 32, 88, 90, 14],
            [2, 16, 'akurasi dijaga', 'Kecepatan boleh meningkat, namun akurasi tetap harus dijaga.', 33, 88, 88, 13],
            [2, 17, 'kalimat majemuk', 'Sang juru aksara membaca cepat, menulis cermat, dan tetap tenang.', 34, 89, 88, 13],
            [2, 18, 'tanya penjaga', 'Penjaga bertanya, "Siapkah kamu menjaga aksara kerajaan?"', 35, 89, 85, 12],
            [2, 19, 'ritme cerita', 'Di ujung ladang, cahaya emas muncul; babak baru segera dimulai.', 36, 90, 85, 12],
            [2, 20, 'boss ladang', 'Boss Ladang menguji fokus: cepat, tepat, dan jangan banyak salah!', 38, 90, 82, 11],

            // BAB 3 — struktur lebih kompleks, kutip, titik dua, tanda hubung
            [3, 21, 'ruang naskah', 'Pujangga kerajaan menulis kisah panjang tentang keberanian dan kebijaksanaan.', 40, 90, 82, 11],
            [3, 22, 'tanda bermakna', 'Di ruang naskah istana, setiap tanda baca memiliki makna penting.', 41, 90, 80, 11],
            [3, 23, 'pesan istana', 'Pesan itu berbunyi: "Tulislah dengan jujur, cepat, dan teliti."', 42, 91, 80, 10],
            [3, 24, 'kalimat panjang', 'Kalimat yang lebih panjang menuntut konsentrasi penuh dari sang juru aksara muda.', 43, 91, 78, 10],
            [3, 25, 'gamelan malam', 'Suara gamelan terdengar pelan saat naskah kerajaan dibuka kembali.', 44, 92, 78, 10],
            [3, 26, 'kesalahan fatal', 'Akurasi adalah kehormatan; satu kesalahan dapat mengubah pesan.', 45, 92, 76, 9],
            [3, 27, 'kata terikat', 'Pena bergerak cepat, tetapi pikiran harus tetap tertata dan waspada.', 46, 92, 76, 9],
            [3, 28, 'naskah berlapis', 'Setiap paragraf menyimpan petunjuk tentang warisan ilmu masa lalu.', 47, 93, 74, 9],
            [3, 29, 'ujian pujangga', 'Istana menunggu tulisan yang bersih, cepat, rapi, dan penuh ketelitian.', 48, 93, 74, 8],
            [3, 30, 'boss pujangga', 'Boss Pujangga berkata: "Jaga tempo-mu, baca tanda-baca, lalu selesaikan!"', 50, 94, 72, 8],

            // BAB 4 — angka, tahun, persen, waktu, tanda kurung, slash
            [4, 31, 'lonceng malam', 'Ujian Pajajaran dimulai pada pukul 21:30, saat lonceng kerajaan berbunyi.', 52, 94, 72, 8],
            [4, 32, 'batas waktu', 'Sang juru aksara harus menyalin 3 naskah sebelum obor terakhir padam.', 53, 94, 70, 8],
            [4, 33, 'tekanan meningkat', 'Waktu tersisa 60 detik; kalimat makin panjang, padat, dan rumit.', 54, 94, 70, 7],
            [4, 34, 'nilai berkurang', 'Kesalahan kecil mengurangi 5% nilai, maka fokus menjadi senjata utama.', 55, 95, 68, 7],
            [4, 35, 'kode penjaga', 'Kode gerbang adalah PJ-04/15; masukkan dengan tepat tanpa tertukar.', 56, 95, 68, 7],
            [4, 36, 'data istana', 'Catatan istana mencatat 27 prajurit, 8 penjaga, dan 1 naskah rahasia.', 57, 95, 66, 6],
            [4, 37, 'format laporan', 'Laporan harian: akurasi 92%, WPM 58, kesalahan 4, status aman.', 58, 95, 66, 6],
            [4, 38, 'perintah cepat', 'Perintah: buka ruang arsip (A-12), ambil naskah, lalu tutup kembali.', 59, 95, 64, 6],
            [4, 39, 'gerbang pajajaran', 'Gerbang Pajajaran hanya terbuka bagi pemain dengan skor 1.500+ poin.', 60, 96, 64, 5],
            [4, 40, 'boss pajajaran', 'Boss Pajajaran menguji: 3 angka, 2 simbol, 1 fokus, dan 0 alasan!', 62, 96, 62, 5],

            // BAB 5 — kapital campuran, simbol, angka, slash, hashtag, versi, tekanan tinggi
            [5, 41, 'naskah terakhir', 'Pewaris Siliwangi berdiri di depan Naskah Terakhir #01.', 64, 96, 62, 5],
            [5, 42, 'warisan agung', 'Cerita panjang tentang keberanian, kebijaksanaan, dan kesetiaan harus ditulis tanpa ragu.', 65, 96, 60, 5],
            [5, 43, 'kode akhir', 'Masukkan kode LARAS-50/FINAL sebelum waktu menunjukkan 00:45.', 66, 97, 60, 4],
            [5, 44, 'huruf campuran', 'Sang Pewaris membaca: Prabu Siliwangi, Pajajaran, dan Aksara Sunda.', 67, 97, 58, 4],
            [5, 45, 'simbol kerajaan', 'Tanda kerajaan @Pajajaran menyala bersama simbol #Aksara dan %Kemenangan.', 68, 97, 58, 4],
            [5, 46, 'versi naskah', 'Naskah v2.0 berisi 5 bab, 50 level, 100% tekad, dan 0 keraguan.', 70, 97, 56, 4],
            [5, 47, 'perintah final', 'Perintah FINAL: ketik cepat, jaga akurasi, hindari typo, lalu tekan selesai.', 72, 98, 56, 3],
            [5, 48, 'ujian pewaris', 'Warisan aksara hanya dijaga oleh pemain yang tekun, cermat, dan konsisten.', 74, 98, 54, 3],
            [5, 49, 'gerbang akhir', 'Di depan Gerbang Akhir, seluruh kemampuan mengetik diuji dalam satu perjalanan.', 76, 98, 54, 3],
            [5, 50, 'boss akhir siliwangi', 'BOSS FINAL #50: Prabu Siliwangi menanti; ketik LARAS-2076 dengan akurasi 100%!', 80, 98, 52, 2],
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
            1 => "BAB 1 mengajarkan dasar mengetik melalui huruf kecil, kata pendek, dan kalimat sederhana pada level {$levelNumber}.",
            2 => "BAB 2 mulai memperkenalkan kalimat lebih panjang, huruf kapital, koma, titik, dan tanda tanya pada level {$levelNumber}.",
            3 => "BAB 3 membawa pemain ke naskah kerajaan dengan struktur kalimat lebih kompleks dan tanda baca yang lebih beragam pada level {$levelNumber}.",
            4 => "BAB 4 menjadi Ujian Pajajaran dengan angka, waktu, persen, kode, dan simbol teknis pada level {$levelNumber}.",
            5 => "BAB 5 adalah tahap Pewaris Siliwangi dengan kombinasi huruf kapital, angka, simbol, tekanan waktu, dan akurasi tinggi pada level {$levelNumber}.",
            default => "Level {$levelNumber} dalam perjalanan LARAS.",
        };
    }
}