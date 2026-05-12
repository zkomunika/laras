<template>
    <section>
        <div class="topbar">
            <span class="topbar-title"><Trophy :size="18" style="vertical-align:text-bottom" /> Papan Peringkat</span>

            <div class="topbar-right">
                <span class="tag-pill">Global</span>
                <span class="tag-pill teal">
                    <span class="live-dot"></span>
                    Live
                </span>
            </div>
        </div>

        <div class="pad">
            <div v-if="loading" class="empty-state">
                Memuat leaderboard...
            </div>

            <div v-else-if="error" class="empty-state">
                {{ error }}
            </div>

            <div v-else-if="leaderboard.length === 0" class="empty-state">
                Belum ada data leaderboard. Selesaikan satu level terlebih dahulu.
            </div>

            <template v-else>
                <div class="leaderboard-podium">
                    <div v-if="second" class="podium-item">
                        <div class="podium-medal"><Medal :size="24" color="#cbd5e1" /></div>
                        <div class="podium-card second">
                            <UserAvatar :user="second" :size="32" style="margin-bottom:8px" />
                            <strong>{{ second.name }}</strong>
                            <span>{{ formatScore(second.total_score) }} pts</span>
                        </div>
                    </div>

                    <div v-if="first" class="podium-item">
                        <div class="podium-medal first-medal"><Crown :size="28" color="#fbbf24" /></div>
                        <div class="podium-card first">
                            <UserAvatar :user="first" :size="40" style="margin-bottom:8px" class="pulse" />
                            <strong>{{ first.name }}</strong>
                            <span>{{ formatScore(first.total_score) }} pts</span>
                        </div>
                    </div>

                    <div v-if="third" class="podium-item">
                        <div class="podium-medal"><Medal :size="24" color="#b45309" /></div>
                        <div class="podium-card third">
                            <UserAvatar :user="third" :size="32" style="margin-bottom:8px" />
                            <strong>{{ third.name }}</strong>
                            <span>{{ formatScore(third.total_score) }} pts</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="sec-head">Peringkat Lengkap</div>

                    <div
                        v-for="player in leaderboard"
                        :key="player.user_id"
                        class="lb-row"
                    >
                        <span class="lb-rank">
                            {{ medal(player.rank) }}
                        </span>

                        <div class="lb-avatar-sm" style="background:transparent; padding:0;">
                            <UserAvatar :user="player" :size="16" />
                        </div>

                        <div class="lb-name">
                            {{ player.name }}
                            <div class="sub">
                                {{ player.completed_levels }} level selesai · {{ player.best_wpm }} WPM
                            </div>
                        </div>

                        <span class="lb-score">
                            {{ formatScore(player.total_score) }}
                        </span>
                    </div>
                </div>
            </template>
        </div>
    </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { leaderboardApi } from '@/services/leaderboardApi';
import { Trophy, Crown, Medal, User, Sword } from 'lucide-vue-next';
import UserAvatar from '@/components/UserAvatar.vue';

const leaderboard = ref([]);
const loading = ref(true);
const error = ref('');

const first = computed(() => leaderboard.value[0] ?? null);
const second = computed(() => leaderboard.value[1] ?? null);
const third = computed(() => leaderboard.value[2] ?? null);

onMounted(async () => {
    try {
        leaderboard.value = await leaderboardApi.list();
    } catch (err) {
        error.value = 'Gagal memuat leaderboard.';
    } finally {
        loading.value = false;
    }
});

function medal(rank) {
    if (rank === 1) return '🥇';
    if (rank === 2) return '🥈';
    if (rank === 3) return '🥉';

    return `#${rank}`;
}

function formatScore(value) {
    return Number(value || 0).toLocaleString('id-ID');
}
</script>