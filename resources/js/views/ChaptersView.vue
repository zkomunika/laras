<template>
    <section class="page-section">
        <div>
            <p class="eyebrow">Story Mode</p>
            <h1>Daftar BAB</h1>
        </div>

        <div class="chapter-grid">
            <article
                v-for="chapter in chapters"
                :key="chapter.id"
                class="chapter-card"
            >
                <span class="chapter-number">BAB {{ chapter.id }}</span>

                <h2>{{ chapter.title }}</h2>

                <p>{{ chapter.description }}</p>

                <RouterLink :to="`/story/levels/${chapter.startLevel}`" class="text-link">
                    Masuk Level {{ chapter.startLevel }}
                </RouterLink>
            </article>
        </div>
    </section>
</template>

<script setup>
const chapters = [
    {
        id: 1,
        title: 'Awal Sang Juru Aksara',
        description: 'Pengenalan dunia Siliwangi, teks pendek, dan target WPM rendah.',
        startLevel: 1,
    },
    {
        id: 2,
        title: 'Jejak Ladang Aksara',
        description: 'Teks mulai lebih panjang, tempo permainan meningkat, dan cerita mulai berkembang.',
        startLevel: 11,
    },
    {
        id: 3,
        title: 'Pujangga Kerajaan',
        description: 'Kalimat lebih kompleks dan target akurasi mulai lebih tinggi.',
        startLevel: 21,
    },
    {
        id: 4,
        title: 'Ujian Pajajaran',
        description: 'Waktu lebih terbatas dan kesalahan lebih berpengaruh terhadap skor.',
        startLevel: 31,
    },
    {
        id: 5,
        title: 'Pewaris Siliwangi',
        description: 'Level tersulit dengan teks panjang, target WPM tinggi, dan boss level akhir.',
        startLevel: 41,
    },
];
</script>