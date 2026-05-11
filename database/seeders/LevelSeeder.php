<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $texts = [
            1 => [
                'Aksara kecil membuka jalan bagi juru tulis muda.',
                'Di ladang pagi, pena tua mulai menari perlahan.',
                'Sang murid menjaga napas dan mengetik dengan tenang.',
                'Huruf demi huruf menjadi jejak awal perjalanan.',
                'Di bawah cahaya pagi, aksara pertama mulai hidup.',
                'Juru aksara belajar membaca ritme jemari sendiri.',
                'Kesabaran adalah kunci untuk membuka gerbang cerita.',
                'Setiap kata membawa langkah kecil menuju istana.',
                'Prasasti tua menyimpan pesan yang harus dituliskan.',
                'Boss awal menguji ketenangan sang juru aksara.',
            ],
            2 => [
                'Jejak ladang aksara membawa murid menuju hutan pengetahuan.',
                'Angin pagi menyapu daun lontar yang penuh pesan rahasia.',
                'Setiap kalimat menuntut fokus, tempo, dan ketelitian jemari.',
                'Di persimpangan jalan, sang penulis memilih terus melangkah.',
                'Aksara yang rapi menjadi tanda bahwa pikiran tetap jernih.',
                'Cerita kerajaan mulai terbuka melalui baris-baris naskah.',
                'Kecepatan meningkat, tetapi akurasi tetap harus dijaga.',
                'Sang juru aksara memahami bahwa latihan membentuk keteguhan.',
                'Di ujung ladang, cahaya emas menunjukkan babak baru.',
                'Boss ladang menguji kecepatan, fokus, dan keberanian.',
            ],
            3 => [
                'Pujangga kerajaan menulis kisah panjang tentang keberanian dan kebijaksanaan.',
                'Di ruang naskah istana, setiap tanda baca memiliki makna yang harus dijaga.',
                'Kalimat yang lebih panjang menuntut konsentrasi penuh dari sang juru aksara.',
                'Suara gamelan terdengar pelan saat lembar naskah kerajaan dibuka kembali.',
                'Akurasi menjadi kehormatan, sebab satu kesalahan dapat mengubah pesan.',
                'Pena bergerak cepat, tetapi pikiran harus tetap tertata dan waspada.',
                'Setiap paragraf menyimpan petunjuk tentang warisan ilmu masa lalu.',
                'Sang pujangga muda mulai memahami tanggung jawab menjaga aksara.',
                'Istana menunggu hasil tulisan yang bersih, cepat, dan penuh ketelitian.',
                'Boss pujangga menantang pemain menulis dengan tempo tinggi dan akurat.',
            ],
            4 => [
                'Ujian Pajajaran dimulai ketika lonceng kerajaan berbunyi di tengah malam.',
                'Sang juru aksara harus menyelesaikan naskah sebelum obor terakhir padam.',
                'Waktu semakin sempit, sedangkan kalimat terus bertambah panjang dan rumit.',
                'Kesalahan kecil dapat mengurangi nilai, maka fokus menjadi senjata utama.',
                'Di hadapan para penjaga, pemain harus membuktikan ketahanan dan ketelitian.',
                'Setiap tekanan waktu mengajarkan cara mengatur napas dan strategi mengetik.',
                'Naskah ujian memuat tanda baca, jeda, dan susunan kata yang menantang.',
                'Sang murid tidak lagi sekadar belajar, tetapi mulai diuji sebagai penjaga aksara.',
                'Gerbang Pajajaran hanya terbuka bagi mereka yang cepat dan cermat.',
                'Boss Pajajaran menjadi ujian berat sebelum memasuki babak pewaris.',
            ],
            5 => [
                'Pewaris Siliwangi berdiri di depan naskah terakhir yang menentukan masa depan aksara.',
                'Cerita panjang tentang keberanian, kebijaksanaan, dan kesetiaan harus ditulis tanpa ragu.',
                'Setiap huruf menjadi bukti bahwa latihan panjang telah membentuk kemampuan sejati.',
                'Langit keemasan menyinari istana saat sang juru aksara menuntaskan tugasnya.',
                'Kecepatan tinggi tidak berarti apa-apa tanpa akurasi yang tetap terjaga.',
                'Warisan aksara hanya dapat dijaga oleh pemain yang tekun dan berani belajar.',
                'Naskah terakhir memadukan panjang teks, tekanan waktu, dan ketelitian penuh.',
                'Sang pewaris memahami bahwa kemenangan lahir dari latihan yang konsisten.',
                'Di depan gerbang akhir, seluruh kemampuan mengetik diuji dalam satu perjalanan.',
                'Boss akhir Siliwangi menuntut kecepatan, akurasi, fokus, dan ketenangan tertinggi.',
            ],
        ];

        foreach ($texts as $chapterNumber => $chapterTexts) {
            $chapter = Chapter::where('number', $chapterNumber)->first();

            foreach ($chapterTexts as $index => $targetText) {
                $levelNumber = (($chapterNumber - 1) * 10) + ($index + 1);
                $isBossLevel = ($index + 1) === 10;

                Level::updateOrCreate(
                    ['level_number' => $levelNumber],
                    [
                        'chapter_id' => $chapter->id,
                        'title' => $isBossLevel
                            ? "Boss Level {$levelNumber}"
                            : "Level {$levelNumber}",
                        'story_text' => "BAB {$chapterNumber} membawa pemain ke tahap latihan level {$levelNumber}.",
                        'target_text' => $targetText,
                        'target_wpm' => 18 + ($chapterNumber * 5) + $index,
                        'min_accuracy' => min(80 + ($chapterNumber * 2), 95),
                        'time_limit_seconds' => max(120 - ($chapterNumber * 10), 60),
                        'max_mistakes' => max(20 - ($chapterNumber * 2), 8),
                        'is_boss_level' => $isBossLevel,
                        'sort_order' => $levelNumber,
                    ]
                );
            }
        }
    }
}