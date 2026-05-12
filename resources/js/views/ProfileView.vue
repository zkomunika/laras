<template>
    <section>
        <div class="topbar">
            <span class="topbar-title">👤 Profil Saya</span>

            <div class="topbar-right">
                <span class="tag-pill">Story Mode</span>
                <span class="tag-pill teal">Achievement</span>
            </div>
        </div>

        <div class="pad">
            <div v-if="loading" class="empty-state">
                Memuat profil...
            </div>

            <div v-else-if="error" class="empty-state">
                {{ error }}
            </div>

            <template v-else>
                <div class="card">
                    <div class="profile-big">
                        <div class="avatar-lg pulse">👤</div>

                        <div class="profile-details">
                            <h2>{{ profile.user.name }}</h2>

                            <div style="font-size:14px;color:var(--muted);">
                                {{ profile.user.email }}
                            </div>

                            <div style="font-size:13px;color:var(--text2);margin-top:4px;">
                                Juru Aksara LARAS
                            </div>

                            <div class="badge-row">
                                <span class="badge">🎮 {{ profile.stats.completed_levels }} Level</span>
                                <span class="badge">⭐ {{ formatScore(profile.stats.total_score) }} pts</span>
                                <span class="badge teal">⚡ {{ profile.stats.best_wpm }} WPM</span>
                                <span class="badge">🏅 {{ profile.stats.achievement_unlocked }} / {{ profile.stats.achievement_total }}</span>
                                <span v-if="profile.stats.unread_notifications" class="badge teal">
                                    🔔 {{ profile.stats.unread_notifications }} Baru
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sec-head">Statistik Permainan</div>

                <div class="card-grid" style="margin-bottom:20px;">
                    <div class="stat-block">
                        <span class="stat-val">{{ formatScore(profile.stats.total_score) }}</span>
                        <div class="stat-lbl">Total Skor</div>
                    </div>

                    <div class="stat-block">
                        <span class="stat-val">{{ profile.stats.average_accuracy }}%</span>
                        <div class="stat-lbl">Rata-rata Akurasi</div>
                    </div>

                    <div class="stat-block">
                        <span class="stat-val">{{ profile.stats.completed_levels }} / 50</span>
                        <div class="stat-lbl">Level Selesai</div>
                    </div>

                    <div class="stat-block">
                        <span class="stat-val">{{ profile.stats.best_wpm }}</span>
                        <div class="stat-lbl">Best WPM</div>
                    </div>

                    <div class="stat-block">
                        <span class="stat-val">{{ profile.stats.total_stars }}</span>
                        <div class="stat-lbl">Total Bintang</div>
                    </div>

                    <div class="stat-block">
                        <span class="stat-val">{{ profile.stats.total_attempts }}</span>
                        <div class="stat-lbl">Total Percobaan</div>
                    </div>
                </div>

                <div class="card">
                    <div class="sec-head">📈 Progress Aksara</div>

                    <div class="chart-bar-row">
                        <span class="chart-bar-label">Level</span>
                        <div class="chart-bar-outer">
                            <div
                                class="chart-bar-inner"
                                :style="{ width: `${completedPercent}%` }"
                            ></div>
                        </div>
                        <span class="chart-bar-val">{{ completedPercent }}%</span>
                    </div>

                    <div class="chart-bar-row">
                        <span class="chart-bar-label">Akurasi</span>
                        <div class="chart-bar-outer">
                            <div
                                class="chart-bar-inner"
                                :style="{ width: `${profile.stats.average_accuracy}%` }"
                            ></div>
                        </div>
                        <span class="chart-bar-val">{{ profile.stats.average_accuracy }}%</span>
                    </div>

                    <div class="chart-bar-row">
                        <span class="chart-bar-label">WPM</span>
                        <div class="chart-bar-outer">
                            <div
                                class="chart-bar-inner"
                                :style="{ width: `${bestWpmPercent}%` }"
                            ></div>
                        </div>
                        <span class="chart-bar-val">{{ profile.stats.best_wpm }}</span>
                    </div>
                </div>

                <div class="profile-two-column">
                    <div class="card">
                        <div class="sec-head">🏅 Achievement</div>

                        <div
                            v-for="achievement in profile.achievements"
                            :key="achievement.code"
                            class="ach-item"
                            :class="{ locked: !achievement.unlocked }"
                        >
                            <div class="ach-icon">{{ achievement.icon }}</div>
                            <div>
                                <div class="ach-name">{{ achievement.name }}</div>
                                <div class="ach-sub">{{ achievement.description }}</div>
                            </div>
                            <span class="ach-badge" :class="{ locked: !achievement.unlocked }">
                                {{ achievement.unlocked ? '✓ Diperoleh' : 'Terkunci' }}
                            </span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="sec-head">🔔 Notifikasi Terbaru</div>

                        <div v-if="!profile.recent_notifications.length" class="empty-mini">
                            Belum ada notifikasi.
                        </div>

                        <div
                            v-for="notification in profile.recent_notifications"
                            :key="notification.id"
                            class="notif-item"
                            :class="{ unread: !notification.read_at }"
                        >
                            <div class="notif-title">{{ notification.title }}</div>
                            <div class="notif-message">{{ notification.message }}</div>
                            <div class="notif-time">{{ formatDate(notification.created_at) }}</div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { profileApi } from '@/services/profileApi';

const profile = ref(null);
const loading = ref(true);
const error = ref('');

const completedPercent = computed(() => {
    if (!profile.value) return 0;

    return Math.min(100, Math.round((profile.value.stats.completed_levels / 50) * 100));
});

const bestWpmPercent = computed(() => {
    if (!profile.value) return 0;

    return Math.min(100, Math.round((profile.value.stats.best_wpm / 100) * 100));
});

onMounted(async () => {
    try {
        profile.value = await profileApi.show();
    } catch (err) {
        error.value = 'Gagal memuat profil.';
    } finally {
        loading.value = false;
    }
});

function formatScore(value) {
    return Number(value || 0).toLocaleString('id-ID');
}

function formatDate(value) {
    if (!value) {
        return '-';
    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value));
}
</script>
