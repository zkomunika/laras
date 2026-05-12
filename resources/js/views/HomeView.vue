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
                <div class="avatar pulse" style="background:transparent; padding:0;">
                    <UserAvatar v-if="auth.user" :user="auth.user" :size="36" />
                    <User v-else :size="24" />
                </div>

                <div class="profile-info">
                    <div class="profile-name">
                        {{ auth.user?.name || 'Juru Aksara' }}
                    </div>

                    <div class="profile-sub">
                        <span><Gamepad2 :size="14" /> Story Mode</span>
                        <span><Save :size="14" /> Progress Tersimpan</span>
                        <span><BarChart2 :size="14" /> WPM & Akurasi</span>
                        <span><MessageCircle :size="14" /> Realtime Bonus</span>
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
                    <div class="action-btn-icon"><Play :size="24" /></div>
                    <div class="action-btn-label">Lanjut Bermain</div>
                    <div class="action-btn-sub">Mulai dari Level 1</div>
                </RouterLink>

                <RouterLink
                    :to="auth.isAuthenticated ? '/chapters' : '/login'"
                    class="action-btn"
                >
                    <div class="action-btn-icon"><BookOpen :size="24" /></div>
                    <div class="action-btn-label">Pilih Bab</div>
                    <div class="action-btn-sub">5 BAB · 50 Level</div>
                </RouterLink>

                <RouterLink to="/leaderboard" class="action-btn">
                    <div class="action-btn-icon"><Trophy :size="24" /></div>
                    <div class="action-btn-label">Papan Peringkat</div>
                    <div class="action-btn-sub">Lihat total skor pemain</div>
                </RouterLink>

                <RouterLink
                    :to="auth.isAuthenticated ? '/realtime' : '/login'"
                    class="action-btn"
                >
                    <div class="action-btn-icon"><MessageCircle :size="24" /></div>
                    <div class="action-btn-label">Realtime Room</div>
                    <div class="action-btn-sub">Chat dan status online</div>
                </RouterLink>
            </div>

            <div class="card">
                <div class="sec-head"><BarChart2 :size="18" style="vertical-align:text-bottom" /> Ringkasan Sistem</div>

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
                        <div class="lb-avatar-sm" style="background:transparent; padding:0;">
                            <UserAvatar :user="player" :size="16" />
                        </div>

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
import { User, Gamepad2, Save, BarChart2, MessageCircle, Play, BookOpen, Trophy } from 'lucide-vue-next';
import UserAvatar from '@/components/UserAvatar.vue';

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