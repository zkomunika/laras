<template>
    <section>
        <div class="topbar">
            <span class="topbar-title"><User :size="18" style="vertical-align:text-bottom" /> Profil Saya</span>

            <div class="topbar-right">
                <span class="tag-pill">Story Mode</span>
                <span class="tag-pill teal">Progress</span>
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
                        <div class="avatar-lg pulse" style="background:transparent; padding:0;">
                            <UserAvatar :user="profile.user" :size="48" />
                        </div>

                        <div class="profile-details">
                            <h2>{{ profile.user.name }}</h2>

                            <div style="font-size:14px;color:var(--muted);">
                                {{ profile.user.email }}
                            </div>

                            <div style="font-size:13px;color:var(--text2);margin-top:4px;">
                                Juru Aksara LARAS
                            </div>

                            <div class="badge-row">
                                <span class="badge"><Gamepad2 :size="14" style="vertical-align:middle" /> {{ profile.stats.completed_levels }} Level</span>
                                <span class="badge"><Star :size="14" style="vertical-align:middle" /> {{ formatScore(profile.stats.total_score) }} pts</span>
                                <span class="badge teal"><Zap :size="14" style="vertical-align:middle" /> {{ profile.stats.best_wpm }} WPM</span>
                                <span class="badge"><BarChart2 :size="14" style="vertical-align:middle" /> {{ profile.stats.average_accuracy }}%</span>
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
                        <span class="stat-val">{{ profile.stats.unlocked_levels }}</span>
                        <div class="stat-lbl">Level Terbuka</div>
                    </div>

                    <div class="stat-block">
                        <span class="stat-val">{{ profile.stats.total_attempts }}</span>
                        <div class="stat-lbl">Total Percobaan</div>
                    </div>
                </div>

                <div class="card">
                    <div class="sec-head"><TrendingUp :size="18" style="vertical-align:text-bottom" /> Progress Aksara</div>

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

                <div class="card">
                    <div class="sec-head"><Medal :size="18" style="vertical-align:text-bottom" /> Achievement</div>

                    <div class="ach-item">
                        <div class="ach-icon"><Medal :size="24" /></div>
                        <div>
                            <div class="ach-name">Pengguna Baru</div>
                            <div class="ach-sub">Berhasil membuat akun LARAS</div>
                        </div>
                        <span class="ach-badge">✓ Diperoleh</span>
                    </div>

                    <div class="ach-item" :style="{ opacity: profile.stats.completed_levels > 0 ? 1 : 0.45 }">
                        <div class="ach-icon"><Zap :size="24" /></div>
                        <div>
                            <div class="ach-name">Langkah Pertama</div>
                            <div class="ach-sub">Selesaikan minimal satu level</div>
                        </div>
                        <span class="ach-badge">
                            {{ profile.stats.completed_levels > 0 ? '✓ Diperoleh' : 'Terkunci' }}
                        </span>
                    </div>

                    <div class="ach-item" :style="{ opacity: profile.stats.completed_levels >= 10 ? 1 : 0.45 }">
                        <div class="ach-icon"><Crown :size="24" /></div>
                        <div>
                            <div class="ach-name">Penjaga Bab Awal</div>
                            <div class="ach-sub">Selesaikan 10 level pertama</div>
                        </div>
                        <span class="ach-badge">
                            {{ profile.stats.completed_levels >= 10 ? '✓ Diperoleh' : 'Terkunci' }}
                        </span>
                    </div>
                </div>
            </template>
        </div>
    </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { profileApi } from '@/services/profileApi';
import { User, Gamepad2, Star, Zap, BarChart2, TrendingUp, Medal, Crown } from 'lucide-vue-next';
import UserAvatar from '@/components/UserAvatar.vue';

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
</script>