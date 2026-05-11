<template>
    <section class="page-section">
        <div>
            <p class="eyebrow">Leaderboard</p>
            <h1>Peringkat Pemain</h1>
        </div>

        <div v-if="loading" class="empty-state">
            Memuat leaderboard...
        </div>

        <div v-else-if="error" class="empty-state">
            {{ error }}
        </div>

        <div v-else-if="leaderboard.length === 0" class="empty-state">
            Belum ada data leaderboard. Selesaikan satu level terlebih dahulu.
        </div>

        <div v-else class="leaderboard-table">
            <div class="leaderboard-row leaderboard-head">
                <span>Rank</span>
                <span>Pemain</span>
                <span>Level Selesai</span>
                <span>Best WPM</span>
                <span>Akurasi Rata-rata</span>
                <span>Total Score</span>
            </div>

            <div
                v-for="player in leaderboard"
                :key="player.user_id"
                class="leaderboard-row"
            >
                <span>#{{ player.rank }}</span>
                <span>{{ player.name }}</span>
                <span>{{ player.completed_levels }}</span>
                <span>{{ player.best_wpm }}</span>
                <span>{{ player.average_accuracy }}%</span>
                <strong>{{ player.total_score }}</strong>
            </div>
        </div>
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { leaderboardApi } from '@/services/leaderboardApi';

const leaderboard = ref([]);
const loading = ref(true);
const error = ref('');

onMounted(async () => {
    try {
        leaderboard.value = await leaderboardApi.list();
    } catch (err) {
        error.value = 'Gagal memuat leaderboard.';
    } finally {
        loading.value = false;
    }
});
</script>