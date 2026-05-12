<template>
    <section>
        <div class="topbar">
            <RouterLink to="/challenge" class="btn btn-secondary topbar-back">◀ Lobby</RouterLink>
            <span class="topbar-title">🏁 Hasil Challenge</span>
            <div class="topbar-right">
                <ThemeToggle />
                <span v-if="room" class="tag-pill">{{ room.name }}</span>
            </div>
        </div>

        <div class="pad">
            <div v-if="loading" class="empty-state">Memuat hasil challenge...</div>
            <div v-else-if="error" class="empty-state">{{ error }}</div>

            <div v-else class="result-page-grid">
                <main class="ranking-card">
                    <div class="section-row">
                        <div>
                            <div class="sec-head">Ranking Akhir</div>
                            <p class="mini-note">
                                Ranking dihitung backend berdasarkan validitas teks, durasi, akurasi, WPM, kesalahan, dan skor.
                            </p>
                        </div>
                        <span class="tag-pill teal">{{ results.length }} hasil</span>
                    </div>

                    <div v-if="results.length === 0" class="empty-state">
                        Belum ada hasil. Selesaikan challenge dulu.
                    </div>

                    <div v-else class="ranking-list">
                        <article v-for="result in results" :key="result.id" class="ranking-row">
                            <div class="rank-badge">#{{ result.rank || '-' }}</div>
                            <div class="ranking-main">
                                <strong>{{ result.name }}</strong>
                                <span>{{ result.completed ? 'Valid' : 'Belum valid' }}</span>
                            </div>
                            <div class="ranking-metrics">
                                <span>{{ result.wpm }} WPM</span>
                                <span>{{ result.accuracy }}%</span>
                                <span>{{ result.mistakes }} salah</span>
                                <span>{{ result.score }} pts</span>
                            </div>
                        </article>
                    </div>
                </main>

                <aside class="detail-panel">
                    <h4>Ringkasan Room</h4>
                    <div class="detail-meta">Nama: <strong>{{ room?.name }}</strong></div>
                    <div class="detail-meta">Mode: <strong>{{ room?.type }}</strong></div>
                    <div class="detail-meta">Status: <strong>{{ statusLabel(room?.status) }}</strong></div>
                    <div class="detail-meta">Pemain: <strong>{{ room?.participants_count }}/{{ room?.capacity }}</strong></div>
                    <div class="detail-meta">Level: <strong>{{ room?.level_summary?.level_number }} · {{ room?.level_summary?.title }}</strong></div>

                    <div class="room-actions large-actions">
                        <RouterLink to="/challenge" class="btn btn-primary">Kembali ke Lobby</RouterLink>
                        <RouterLink v-if="room" :to="`/challenge/rooms/${room.id}`" class="btn btn-secondary">Buka Room</RouterLink>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import ThemeToggle from '@/components/ThemeToggle.vue';
import { challengeApi } from '@/services/challengeApi';

const route = useRoute();
const room = ref(null);
const results = ref([]);
const loading = ref(false);
const error = ref('');

function statusLabel(status) {
    return {
        waiting: 'Menunggu',
        playing: 'Berjalan',
        finished: 'Selesai',
        cancelled: 'Dibatalkan',
    }[status] || status;
}

onMounted(async () => {
    loading.value = true;
    try {
        const data = await challengeApi.results(route.params.id);
        room.value = data.room;
        results.value = data.results;
    } catch (err) {
        error.value = err.response?.data?.message || 'Gagal memuat hasil challenge.';
    } finally {
        loading.value = false;
    }
});
</script>
