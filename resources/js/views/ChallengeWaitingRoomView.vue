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

                    <div v-if="room.is_private && room.code" class="room-code-box shareable-room-code-box">
                        <div class="room-code-main">
                            <span>Kode private room</span>
                            <strong>{{ room.code }}</strong>
                        </div>

                        <div class="room-code-actions">
                            <button class="btn btn-secondary" type="button" @click="copyRoomCode">
                                Salin Kode
                            </button>
                            <button class="btn btn-secondary" type="button" @click="copyInviteMessage">
                                Salin Undangan
                            </button>
                            <button class="btn btn-primary" type="button" @click="shareInvite">
                                Bagikan
                            </button>
                        </div>
                    </div>

                    <div v-if="room.is_private && room.code" class="share-preview-box">
                        <span>Format undangan</span>
                        <p>{{ inviteMessage }}</p>
                    </div>

                    <div v-if="shareFeedback" class="share-feedback">
                        {{ shareFeedback }}
                    </div>

                    <div v-if="room.is_private && !room.is_joined && room.status === 'waiting'" class="join-code-panel">
                        <div>
                            <h3>Masuk private room</h3>
                            <p>Masukkan kode undangan dari room master untuk bergabung ke challenge ini.</p>
                        </div>

                        <div class="join-code-row">
                            <input
                                v-model="joinCodeInput"
                                class="form-input compact-input join-code-input"
                                maxlength="12"
                                placeholder="Kode room"
                                @keyup.enter="joinPrivateFromRoom"
                            />
                            <button class="btn btn-primary" type="button" :disabled="acting" @click="joinPrivateFromRoom">
                                {{ acting ? 'Memproses...' : 'Gabung' }}
                            </button>
                        </div>
                    </div>

                    <div v-else-if="!room.is_private && !room.is_joined && room.status === 'waiting'" class="join-code-panel">
                        <div>
                            <h3>Gabung public room</h3>
                            <p>Room ini terbuka untuk semua pemain selama slot masih tersedia.</p>
                        </div>

                        <button class="btn btn-primary" type="button" :disabled="acting || room.is_full" @click="joinPublicFromRoom">
                            {{ room.is_full ? 'Room Penuh' : acting ? 'Memproses...' : 'Gabung Room' }}
                        </button>
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
                            v-if="room.status === 'playing' && room.is_joined"
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

                        <button v-if="room.is_joined" class="btn btn-secondary" type="button" :disabled="acting" @click="leaveRoom">
                            Keluar Room
                        </button>
                    </div>
                </main>

                <aside class="detail-panel">
                    <h4>Info Challenge</h4>
                    <p class="detail-narrative">
                        Teks challenge akan diacak dari kata-kata yang tersedia di story mode. Pemilihan level tidak lagi digunakan untuk challenge.
                    </p>
                    <div class="detail-meta">🧩 Jumlah kata: <strong>{{ room.challenge?.word_count || room.word_count }} kata</strong></div>
                    <div class="detail-meta">⏱️ Batas waktu: <strong>{{ room.challenge?.time_limit_seconds || room.time_limit_seconds }} detik</strong></div>
                    <div class="detail-meta">📚 Sumber kata: <strong>Story mode</strong></div>
                    <div class="detail-meta">🏁 Validasi: <strong>Teks sama persis dan selesai sebelum waktu habis</strong></div>

                    <div class="mini-note boxed-note">
                        Private room tetap bisa dibagikan memakai kode, pesan undangan, atau Web Share API bila browser mendukung.
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
const shareFeedback = ref('');
const joinCodeInput = ref('');
const autoJoinAttempted = ref(false);
let poller = null;
let feedbackTimer = null;

const roomId = computed(() => route.params.id);
const emptySlots = computed(() => {
    const count = Math.max(0, Number(room.value?.capacity || 0) - Number(room.value?.participants?.length || 0));
    return Array.from({ length: count }, (_, index) => index + 1);
});

const roomUrl = computed(() => {
    if (!room.value || typeof window === 'undefined') return '';

    const url = new URL(`/challenge/rooms/${room.value.id}`, window.location.origin);

    if (room.value.is_private && room.value.code) {
        url.searchParams.set('code', room.value.code);
    }

    return url.toString();
});

const inviteMessage = computed(() => {
    if (!room.value?.code) return '';

    return `Ayo masuk private room LARAS: ${room.value.name}\nKode room: ${room.value.code}\nLink room: ${roomUrl.value}`;
});

function statusLabel(status) {
    return {
        waiting: 'Menunggu',
        playing: 'Berjalan',
        finished: 'Selesai',
        cancelled: 'Dibatalkan',
    }[status] || status;
}

function showShareFeedback(message) {
    shareFeedback.value = message;

    if (feedbackTimer) {
        clearTimeout(feedbackTimer);
    }

    feedbackTimer = setTimeout(() => {
        shareFeedback.value = '';
    }, 2600);
}

async function copyText(text, successMessage) {
    if (!text) return;

    try {
        if (navigator?.clipboard?.writeText) {
            await navigator.clipboard.writeText(text);
        } else {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
        }

        showShareFeedback(successMessage);
    } catch (err) {
        showShareFeedback('Gagal menyalin. Salin kode secara manual.');
    }
}

async function copyRoomCode() {
    await copyText(room.value?.code || '', 'Kode room berhasil disalin.');
}

async function copyInviteMessage() {
    await copyText(inviteMessage.value, 'Undangan private room berhasil disalin.');
}

async function shareInvite() {
    if (!inviteMessage.value) return;

    if (navigator?.share) {
        try {
            await navigator.share({
                title: `Private Room LARAS - ${room.value.name}`,
                text: inviteMessage.value,
                url: roomUrl.value,
            });
            showShareFeedback('Undangan private room siap dibagikan.');
            return;
        } catch (err) {
            if (err?.name === 'AbortError') return;
        }
    }

    await copyInviteMessage();
}

async function loadRoom() {
    try {
        const data = await challengeApi.room(roomId.value);
        room.value = data;

        if (!joinCodeInput.value && typeof route.query.code === 'string') {
            joinCodeInput.value = route.query.code;
        }

        if (
            data.is_private &&
            !data.is_joined &&
            data.status === 'waiting' &&
            joinCodeInput.value &&
            !autoJoinAttempted.value
        ) {
            await joinPrivateFromRoom(true);
            return;
        }

        if (data.status === 'playing' && data.is_joined) {
            router.push(`/challenge/rooms/${data.id}/game`);
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Gagal memuat waiting room.';
    }
}

async function joinPrivateFromRoom(isAutoJoin = false) {
    const automatic = isAutoJoin === true;

    acting.value = true;
    actionError.value = '';
    autoJoinAttempted.value = true;

    try {
        const data = await challengeApi.joinCode(roomId.value, joinCodeInput.value || '');
        room.value = data;
        showShareFeedback(automatic ? 'Berhasil masuk melalui kode undangan.' : 'Berhasil masuk private room.');

        if (data.status === 'playing') {
            router.push(`/challenge/rooms/${data.id}/game`);
        }
    } catch (err) {
        actionError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {})?.[0]?.[0] || 'Kode room tidak valid.';
    } finally {
        acting.value = false;
    }
}

async function joinPublicFromRoom() {
    acting.value = true;
    actionError.value = '';

    try {
        const data = await challengeApi.join(roomId.value);
        room.value = data;
    } catch (err) {
        actionError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {})?.[0]?.[0] || 'Gagal masuk room.';
    } finally {
        acting.value = false;
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
    if (typeof route.query.code === 'string') {
        joinCodeInput.value = route.query.code;
    }
    await loadRoom();
    loading.value = false;
    poller = setInterval(loadRoom, 4000);
});

onBeforeUnmount(() => {
    if (poller) clearInterval(poller);
    if (feedbackTimer) clearTimeout(feedbackTimer);
});
</script>
