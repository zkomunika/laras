<template>
    <section>
        <div class="topbar">
            <span class="topbar-title">LARAS — Ladang Aksara Siliwangi</span>

            <div class="topbar-right">
                <span class="tag-pill teal">
                    <span class="live-dot"></span>
                    Live
                </span>

                <span class="tag-pill">
                    Story Mode
                </span>
            </div>
        </div>

        <div class="pad">
            <div class="profile-banner">
                <div class="avatar pulse">👤</div>

                <div class="profile-info">
                    <div class="profile-name">
                        {{ auth.user?.name || 'Juru Aksara' }}
                    </div>

                    <div class="profile-sub">
                        <span>🎮 Story Mode</span>
                        <span>⭐ Progress Tersimpan</span>
                        <span>📊 WPM & Akurasi</span>
                        <span>💬 Realtime Bonus</span>
                    </div>
                </div>

                <RouterLink
                    v-if="auth.isAuthenticated"
                    to="/profile"
                    class="btn btn-secondary"
                    style="flex-shrink:0;"
                >
                    Lihat Profil →
                </RouterLink>

                <RouterLink
                    v-else
                    to="/login"
                    class="btn btn-primary"
                    style="flex-shrink:0;"
                >
                    Login
                </RouterLink>
            </div>

            <div class="sec-head">Quick Actions</div>

            <div class="action-grid">
                <RouterLink
                    :to="auth.isAuthenticated ? '/story/levels/1' : '/login'"
                    class="action-btn primary"
                >
                    <div class="action-btn-icon">▶️</div>
                    <div class="action-btn-label">Lanjut Bermain</div>
                    <div class="action-btn-sub">Mulai dari Level 1</div>
                </RouterLink>

                <RouterLink
                    :to="auth.isAuthenticated ? '/chapters' : '/login'"
                    class="action-btn"
                >
                    <div class="action-btn-icon">📖</div>
                    <div class="action-btn-label">Pilih Bab</div>
                    <div class="action-btn-sub">5 BAB · 50 Level</div>
                </RouterLink>

                <RouterLink to="/leaderboard" class="action-btn">
                    <div class="action-btn-icon">🏆</div>
                    <div class="action-btn-label">Papan Peringkat</div>
                    <div class="action-btn-sub">Lihat total skor pemain</div>
                </RouterLink>

                <RouterLink
                    :to="auth.isAuthenticated ? '/realtime' : '/login'"
                    class="action-btn"
                >
                    <div class="action-btn-icon">💬</div>
                    <div class="action-btn-label">Realtime Room</div>
                    <div class="action-btn-sub">Chat dan status online</div>
                </RouterLink>
            </div>

            <div class="card">
                <div class="sec-head">📊 Ringkasan Sistem</div>

                <div class="card-grid">
                    <div class="stat-block">
                        <span class="stat-val">5</span>
                        <div class="stat-lbl">BAB Cerita</div>
                    </div>

                    <div class="stat-block">
                        <span class="stat-val">50</span>
                        <div class="stat-lbl">Level Typing</div>
                    </div>

                    <div class="stat-block">
                        <span class="stat-val">WPM</span>
                        <div class="stat-lbl">Kecepatan</div>
                    </div>

                    <div class="stat-block">
                        <span class="stat-val">%</span>
                        <div class="stat-lbl">Akurasi</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="sec-head">
                    <span class="live-dot"></span>
                    Papan Peringkat Live
                </div>

                <div v-if="leaderboard.length === 0" class="empty-state">
                    Belum ada data leaderboard. Selesaikan level pertama untuk masuk peringkat.
                </div>

                <div v-else>
                    <div
                        v-for="player in leaderboard.slice(0, 6)"
                        :key="player.user_id"
                        class="lb-row"
                    >
                        <span class="lb-rank">#{{ player.rank }}</span>
                        <div class="lb-avatar-sm">👤</div>

                        <div class="lb-name">
                            {{ player.name }}
                            <div class="sub">
                                {{ player.completed_levels }} level selesai
                            </div>
                        </div>

                        <span class="lb-score">
                            {{ player.total_score }}
                        </span>
                    </div>
                </div>

                <div style="margin-top:12px;">
                    <RouterLink to="/leaderboard" class="btn btn-secondary">
                        Lihat Peringkat Lengkap →
                    </RouterLink>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { leaderboardApi } from '@/services/leaderboardApi';

const auth = useAuthStore();
const leaderboard = ref([]);

onMounted(async () => {
    try {
        leaderboard.value = await leaderboardApi.list();
    } catch (error) {
        leaderboard.value = [];
    }
});
</script>