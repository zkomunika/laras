<template>
    <section>
        <div class="topbar">
            <span class="topbar-title">⚔️ Challenge Lobby</span>
            <div class="topbar-right">
                <ThemeToggle />
                <button class="btn btn-secondary" type="button" :disabled="loading" @click="loadRooms">
                    Refresh
                </button>
            </div>
        </div>

        <div class="pad challenge-layout">
            <aside class="challenge-create-card">
                <div class="sec-head">Buat Room</div>

                <label class="form-label">Nama room</label>
                <input v-model="form.name" class="form-input" placeholder="Balap Aksara Pagi" />

                <label class="form-label">Tipe room</label>
                <select v-model="form.type" class="form-input">
                    <option value="public">Public</option>
                    <option value="private">Private</option>
                </select>

                <label class="form-label">Kapasitas</label>
                <select v-model.number="form.capacity" class="form-input">
                    <option :value="2">2 pemain</option>
                    <option :value="4">4 pemain</option>
                    <option :value="8">8 pemain</option>
                </select>

                <label class="form-label">Level teks challenge</label>
                <select v-model.number="form.level_id" class="form-input">
                    <option v-for="level in levels" :key="level.id" :value="level.id">
                        Level {{ level.level_number }} · {{ level.title }}
                    </option>
                </select>

                <p class="mini-note">
                    Private room tetap tampil di lobby, tetapi butuh kode room untuk masuk.
                </p>

                <div v-if="error" class="form-error">{{ error }}</div>

                <button class="btn btn-primary w-full" type="button" :disabled="creating" @click="createRoom">
                    {{ creating ? 'Membuat...' : 'Buat Room' }}
                </button>
            </aside>

            <main class="challenge-list-card">
                <div class="section-row">
                    <div>
                        <div class="sec-head">Daftar Room</div>
                        <p class="mini-note">Tahap 6 masih basic. Update room memakai refresh/polling, belum WebSocket.</p>
                    </div>
                    <span class="tag-pill">{{ rooms.length }} room aktif</span>
                </div>

                <div v-if="loading" class="empty-state">Memuat lobby challenge...</div>
                <div v-else-if="rooms.length === 0" class="empty-state">Belum ada room aktif. Buat room baru untuk mulai.</div>

                <div v-else class="room-grid">
                    <article v-for="room in rooms" :key="room.id" class="room-card">
                        <div class="room-card-head">
                            <div>
                                <h3>{{ room.is_private ? '🔒' : '🌐' }} {{ room.name }}</h3>
                                <p>Master: {{ room.master?.name || '-' }}</p>
                            </div>
                            <span class="tag-pill" :class="{ teal: room.status === 'playing' }">
                                {{ statusLabel(room.status) }}
                            </span>
                        </div>

                        <div class="room-meta-grid">
                            <span>Mode <strong>{{ room.type }}</strong></span>
                            <span>Pemain <strong>{{ room.participants_count }}/{{ room.capacity }}</strong></span>
                            <span>Level <strong>{{ room.level_summary?.level_number || '-' }}</strong></span>
                            <span>Target <strong>{{ room.level_summary?.target_wpm || '-' }} WPM</strong></span>
                        </div>

                        <div v-if="room.is_private && !room.is_joined" class="private-join-row">
                            <input
                                v-model="privateCodes[room.id]"
                                class="form-input compact-input"
                                maxlength="12"
                                placeholder="Kode room"
                            />
                            <button class="btn btn-secondary" type="button" @click="joinPrivate(room)">
                                Masuk
                            </button>
                        </div>

                        <div class="room-actions">
                            <RouterLink
                                v-if="room.is_joined"
                                :to="room.status === 'playing' ? `/challenge/rooms/${room.id}/game` : `/challenge/rooms/${room.id}`"
                                class="btn btn-primary"
                            >
                                Masuk Room
                            </RouterLink>

                            <button
                                v-else-if="!room.is_private"
                                class="btn btn-primary"
                                type="button"
                                :disabled="room.is_full || room.status !== 'waiting'"
                                @click="joinPublic(room)"
                            >
                                {{ room.is_full ? 'Penuh' : 'Gabung' }}
                            </button>

                            <RouterLink
                                v-if="room.status === 'finished'"
                                :to="`/challenge/rooms/${room.id}/results`"
                                class="btn btn-secondary"
                            >
                                Hasil
                            </RouterLink>
                        </div>
                    </article>
                </div>
            </main>
        </div>
    </section>
</template>

<script setup>
import { onMounted, onBeforeUnmount, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import ThemeToggle from '@/components/ThemeToggle.vue';
import { challengeApi } from '@/services/challengeApi';
import { levelApi } from '@/services/levelApi';

const router = useRouter();
const rooms = ref([]);
const levels = ref([]);
const loading = ref(false);
const creating = ref(false);
const error = ref('');
const privateCodes = reactive({});
let poller = null;

const form = reactive({
    name: 'Balap Aksara Pagi',
    type: 'public',
    capacity: 2,
    level_id: null,
});

function statusLabel(status) {
    return {
        waiting: 'Menunggu',
        playing: 'Berjalan',
        finished: 'Selesai',
        cancelled: 'Dibatalkan',
    }[status] || status;
}

async function loadRooms() {
    loading.value = true;
    error.value = '';
    try {
        rooms.value = await challengeApi.rooms();
    } catch (err) {
        error.value = err.response?.data?.message || 'Gagal memuat room challenge.';
    } finally {
        loading.value = false;
    }
}

async function loadLevels() {
    const data = await levelApi.list();
    levels.value = data;
    form.level_id = data[0]?.id || null;
}

async function createRoom() {
    creating.value = true;
    error.value = '';
    try {
        const room = await challengeApi.createRoom({ ...form });
        router.push(`/challenge/rooms/${room.id}`);
    } catch (err) {
        error.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {})?.[0]?.[0] || 'Gagal membuat room.';
    } finally {
        creating.value = false;
    }
}

async function joinPublic(room) {
    try {
        await challengeApi.join(room.id);
        router.push(`/challenge/rooms/${room.id}`);
    } catch (err) {
        error.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {})?.[0]?.[0] || 'Gagal masuk room.';
    }
}

async function joinPrivate(room) {
    try {
        await challengeApi.joinCode(room.id, privateCodes[room.id] || '');
        router.push(`/challenge/rooms/${room.id}`);
    } catch (err) {
        error.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {})?.[0]?.[0] || 'Kode room tidak valid.';
    }
}

onMounted(async () => {
    await Promise.all([loadLevels(), loadRooms()]);
    poller = setInterval(loadRooms, 5000);
});

onBeforeUnmount(() => {
    if (poller) clearInterval(poller);
});
</script>
