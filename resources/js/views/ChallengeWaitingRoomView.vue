<template>
    <section>
        <div class="topbar">
            <RouterLink to="/challenge" class="btn btn-secondary topbar-back">◀ Lobby</RouterLink>
            <span class="topbar-title">🛡️ Waiting Room Challenge</span>
            <div class="topbar-right">
                <ThemeToggle />
                <span v-if="room" class="tag-pill">{{ statusLabel(room.status) }}</span>
            </div>
        </div>

        <div class="pad">
            <div v-if="loading" class="empty-state">Memuat room...</div>
            <div v-else-if="error" class="empty-state">{{ error }}</div>

            <div v-else-if="room" class="waiting-grid">
                <main class="waiting-card">
                    <div class="room-title-block">
                        <div>
                            <h2>{{ room.is_private ? '🔒' : '🌐' }} {{ room.name }}</h2>
                            <p>Master: {{ room.master?.name || '-' }}</p>
                        </div>
                        <span class="tag-pill teal">{{ room.participants_count }}/{{ room.capacity }} pemain</span>
                    </div>

                    <div v-if="room.is_private" class="room-code-box">
                        <span>Kode private room</span>
                        <strong>{{ room.code }}</strong>
                    </div>

                    <div class="player-slot-grid">
                        <article v-for="participant in room.participants" :key="participant.id" class="player-slot-card">
                            <div class="player-avatar">{{ participant.is_master ? '👑' : '🧑' }}</div>
                            <div>
                                <strong>{{ participant.name }}</strong>
                                <p>{{ participant.is_master ? 'Room Master' : 'Pemain' }} · {{ participant.status }}</p>
                            </div>
                        </article>

                        <article v-for="slot in emptySlots" :key="slot" class="player-slot-card empty">
                            <div class="player-avatar">＋</div>
                            <div>
                                <strong>Slot kosong</strong>
                                <p>Menunggu pemain lain</p>
                            </div>
                        </article>
                    </div>

                    <div v-if="actionError" class="form-error">{{ actionError }}</div>

                    <div class="room-actions large-actions">
                        <button
                            v-if="room.is_master && room.status === 'waiting'"
                            class="btn btn-primary"
                            type="button"
                            :disabled="acting"
                            @click="startChallenge"
                        >
                            {{ acting ? 'Memulai...' : 'Mulai Challenge' }}
                        </button>

                        <RouterLink
                            v-if="room.status === 'playing'"
                            :to="`/challenge/rooms/${room.id}/game`"
                            class="btn btn-primary"
                        >
                            Masuk Game
                        </RouterLink>

                        <RouterLink
                            v-if="room.status === 'finished'"
                            :to="`/challenge/rooms/${room.id}/results`"
                            class="btn btn-primary"
                        >
                            Lihat Hasil
                        </RouterLink>

                        <button class="btn btn-secondary" type="button" :disabled="acting" @click="leaveRoom">
                            Keluar Room
                        </button>
                    </div>
                </main>

                <aside class="detail-panel">
                    <h4>Info Challenge</h4>
                    <p class="detail-narrative">{{ room.level?.story_text }}</p>
                    <div class="detail-meta">📘 Level: <strong>{{ room.level?.level_number }} · {{ room.level?.title }}</strong></div>
                    <div class="detail-meta">🎯 Target WPM: <strong>{{ room.level?.target_wpm }}</strong></div>
                    <div class="detail-meta">✅ Minimum Akurasi: <strong>{{ room.level?.min_accuracy }}%</strong></div>
                    <div class="detail-meta">⏱️ Batas Waktu: <strong>{{ room.level?.time_limit_seconds }} detik</strong></div>
                    <div class="detail-meta">❌ Maksimal Salah: <strong>{{ room.level?.max_mistakes }}</strong></div>

                    <div class="mini-note boxed-note">
                        Tahap ini memakai sistem challenge basic. Room, join, start, game, dan result sudah berjalan. Realtime WebSocket masuk tahap berikutnya.
                    </div>
                </aside>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ThemeToggle from '@/components/ThemeToggle.vue';
import { challengeApi } from '@/services/challengeApi';

const route = useRoute();
const router = useRouter();
const room = ref(null);
const loading = ref(false);
const acting = ref(false);
const error = ref('');
const actionError = ref('');
let poller = null;

const roomId = computed(() => route.params.id);
const emptySlots = computed(() => {
    const count = Math.max(0, Number(room.value?.capacity || 0) - Number(room.value?.participants?.length || 0));
    return Array.from({ length: count }, (_, index) => index + 1);
});

function statusLabel(status) {
    return {
        waiting: 'Menunggu',
        playing: 'Berjalan',
        finished: 'Selesai',
        cancelled: 'Dibatalkan',
    }[status] || status;
}

async function loadRoom() {
    try {
        const data = await challengeApi.room(roomId.value);
        room.value = data;
        if (data.status === 'playing') {
            router.push(`/challenge/rooms/${data.id}/game`);
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Gagal memuat waiting room.';
    }
}

async function startChallenge() {
    acting.value = true;
    actionError.value = '';
    try {
        const data = await challengeApi.start(room.value.id);
        room.value = data;
        router.push(`/challenge/rooms/${room.value.id}/game`);
    } catch (err) {
        actionError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {})?.[0]?.[0] || 'Gagal memulai challenge.';
    } finally {
        acting.value = false;
    }
}

async function leaveRoom() {
    acting.value = true;
    actionError.value = '';
    try {
        await challengeApi.leave(room.value.id);
        router.push('/challenge');
    } catch (err) {
        actionError.value = err.response?.data?.message || 'Gagal keluar room.';
    } finally {
        acting.value = false;
    }
}

onMounted(async () => {
    loading.value = true;
    await loadRoom();
    loading.value = false;
    poller = setInterval(loadRoom, 4000);
});

onBeforeUnmount(() => {
    if (poller) clearInterval(poller);
});
</script>
