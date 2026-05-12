<template>
    <section>
        <div class="topbar">
            <RouterLink :to="`/challenge/rooms/${roomId}`" class="btn btn-secondary topbar-back">◀ Room</RouterLink>
            <span class="topbar-title">⚔️ Challenge Race</span>
            <div class="topbar-right">
                <ThemeToggle />
                <span v-if="room" class="tag-pill teal">{{ room.name }}</span>
            </div>
        </div>

        <div class="pad">
            <div v-if="loading" class="empty-state">Memuat challenge...</div>
            <div v-else-if="error" class="empty-state">{{ error }}</div>

            <div v-else-if="room" class="challenge-game-grid">
                <main class="game-panel">
                    <div class="game-header">
                        <div class="timer-circle" :class="{ danger: remainingSeconds <= 10 }">
                            {{ remainingSeconds }}
                        </div>
                        <div class="game-stats">
                            <div class="game-stat">
                                <div class="val">{{ Math.round(wpm) }}</div>
                                <div class="lbl">WPM</div>
                            </div>
                            <div class="game-stat">
                                <div class="val">{{ accuracy }}%</div>
                                <div class="lbl">Akurasi</div>
                            </div>
                            <div class="game-stat">
                                <div class="val">{{ mistakes }}</div>
                                <div class="lbl">Salah</div>
                            </div>
                            <div class="game-stat">
                                <div class="val">{{ progressPercent }}%</div>
                                <div class="lbl">Progress</div>
                            </div>
                        </div>
                    </div>

                    <div class="game-progress-wrap">
                        <div class="game-progress-label">
                            <span>Progress mengetik</span>
                            <span>{{ typedText.length }} / {{ targetText.length }} karakter</span>
                        </div>
                        <div class="prog-bar">
                            <div class="prog-fill" :style="{ width: `${progressPercent}%` }"></div>
                        </div>
                    </div>

                    <div class="narrative-box">
                        <span
                            v-for="(character, index) in targetCharacters"
                            :key="`${character}-${index}`"
                            class="key-char"
                            :class="characterClass(index)"
                        >{{ character }}</span>
                    </div>

                    <div class="typing-area">
                        <textarea
                            ref="typingInput"
                            v-model="typedText"
                            class="typing-input"
                            spellcheck="false"
                            autocomplete="off"
                            autocorrect="off"
                            autocapitalize="off"
                            :disabled="submitting || submitted"
                            placeholder="Ketik teks challenge di sini..."
                            @input="handleInput"
                        ></textarea>
                        <div class="typing-hint">
                            Backend tetap menghitung hasil resmi berdasarkan teks target, waktu mulai room, akurasi, WPM, dan jumlah salah.
                        </div>
                    </div>

                    <div v-if="submitError" class="form-error">{{ submitError }}</div>

                    <div class="game-footer">
                        <button class="btn btn-primary" type="button" :disabled="submitting || submitted" @click="submitResult">
                            {{ submitting ? 'Mengirim...' : 'Selesai & Kirim Hasil' }}
                        </button>
                        <RouterLink v-if="submitted" :to="`/challenge/rooms/${room.id}/results`" class="btn btn-secondary">
                            Lihat Ranking
                        </RouterLink>
                    </div>
                </main>

                <aside class="detail-panel game-side-card">
                    <CharacterAvatar :state="characterState" />

                    <h4>Peserta</h4>
                    <div class="participant-progress-list">
                        <div v-for="participant in room.participants" :key="participant.id" class="participant-progress-item">
                            <div class="participant-line">
                                <strong>{{ participant.name }}</strong>
                                <span>{{ participant.progress_percent }}%</span>
                            </div>
                            <div class="prog-bar small">
                                <div class="prog-fill" :style="{ width: `${participant.progress_percent}%` }"></div>
                            </div>
                            <p>{{ participant.status }} · {{ Math.round(participant.wpm) }} WPM · {{ participant.accuracy }}%</p>
                        </div>
                    </div>

                    <h4 style="margin-top:18px;">Info Level</h4>
                    <p class="detail-narrative">{{ room.level?.story_text }}</p>
                    <div class="detail-meta">🎯 Target WPM: <strong>{{ room.level?.target_wpm }}</strong></div>
                    <div class="detail-meta">✅ Minimum Akurasi: <strong>{{ room.level?.min_accuracy }}%</strong></div>
                    <div class="detail-meta">⏱️ Batas Waktu: <strong>{{ room.level?.time_limit_seconds }} detik</strong></div>
                    <div class="detail-meta">❌ Maksimal Salah: <strong>{{ room.level?.max_mistakes }}</strong></div>

                    <div v-if="lastResult" class="result-panel compact-result">
                        <div class="sec-head">Hasilmu</div>
                        <div class="result-status-pill" :class="{ success: lastResult.completed }">
                            {{ lastResult.completed ? 'Valid' : 'Belum valid' }}
                        </div>
                        <div class="result-score">#{{ lastResult.rank || '-' }}</div>
                        <div class="result-grid">
                            <div><strong>{{ lastResult.wpm }}</strong><span>WPM</span></div>
                            <div><strong>{{ lastResult.accuracy }}%</strong><span>Akurasi</span></div>
                            <div><strong>{{ lastResult.score }}</strong><span>Skor</span></div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ThemeToggle from '@/components/ThemeToggle.vue';
import CharacterAvatar from '@/components/CharacterAvatar.vue';
import { challengeApi } from '@/services/challengeApi';

const route = useRoute();
const router = useRouter();
const roomId = computed(() => route.params.id);
const room = ref(null);
const loading = ref(false);
const error = ref('');
const submitError = ref('');
const submitting = ref(false);
const submitted = ref(false);
const lastResult = ref(null);
const typedText = ref('');
const elapsedSeconds = ref(0);
const typingInput = ref(null);
let timer = null;
let poller = null;
let progressTimer = null;

const targetText = computed(() => room.value?.level?.target_text || '');
const targetCharacters = computed(() => Array.from(targetText.value));
const targetLength = computed(() => targetText.value.length || 1);
const progressPercent = computed(() => Math.min(100, Math.round((typedText.value.length / targetLength.value) * 100)));
const limitSeconds = computed(() => Number(room.value?.level?.time_limit_seconds || 0));
const remainingSeconds = computed(() => Math.max(0, limitSeconds.value - elapsedSeconds.value));

const mistakes = computed(() => {
    const max = Math.max(typedText.value.length, targetText.value.length);
    let wrong = 0;
    for (let i = 0; i < max; i++) {
        const typed = typedText.value[i];
        const target = targetText.value[i];
        if (typed === undefined) continue;
        if (typed !== target) wrong++;
    }
    return wrong;
});

const correctChars = computed(() => {
    let correct = 0;
    for (let i = 0; i < typedText.value.length; i++) {
        if (typedText.value[i] === targetText.value[i]) correct++;
    }
    return correct;
});

const accuracy = computed(() => {
    if (typedText.value.length === 0) return 0;
    return Math.max(0, Math.round((correctChars.value / typedText.value.length) * 100));
});

const wpm = computed(() => {
    const minutes = Math.max(1, elapsedSeconds.value) / 60;
    return Number(((correctChars.value / 5) / minutes).toFixed(2));
});

const characterState = computed(() => {
    if (submitted.value && lastResult.value?.completed) return 'win';
    if (submitted.value) return 'lose';
    if (mistakes.value > 0) return 'mistake';
    if (typedText.value.length > 0 && typedText.value.length % 12 === 0) return 'combo';
    if (typedText.value.length > 0) return 'typing';
    return 'idle';
});

function characterClass(index) {
    if (index === typedText.value.length) return 'active';
    if (typedText.value[index] === undefined) return '';
    return typedText.value[index] === targetText.value[index] ? 'correct' : 'wrong';
}

function refreshElapsed() {
    const started = room.value?.started_at ? new Date(room.value.started_at).getTime() : Date.now();
    elapsedSeconds.value = Math.max(0, Math.floor((Date.now() - started) / 1000));
}

async function loadRoom() {
    const data = await challengeApi.room(roomId.value);
    room.value = data;

    if (data.status === 'waiting') {
        router.push(`/challenge/rooms/${data.id}`);
    }
}

async function pollRoom() {
    try {
        const data = await challengeApi.room(roomId.value);
        room.value = data;
    } catch (_) {
        // polling failure is non-blocking
    }
}

async function sendProgress() {
    if (!room.value || submitted.value || room.value.status !== 'playing') return;

    try {
        await challengeApi.progress(room.value.id, {
            progress_percent: progressPercent.value,
            typed_chars: typedText.value.length,
            mistakes: mistakes.value,
            wpm: wpm.value,
            accuracy: accuracy.value,
        });
        await pollRoom();
    } catch (_) {
        // progress update is best effort in tahap 6 basic
    }
}

function handleInput() {
    if (typedText.value === targetText.value && !submitted.value) {
        submitResult();
    }
}

async function submitResult() {
    if (submitting.value || submitted.value) return;

    submitting.value = true;
    submitError.value = '';

    try {
        const data = await challengeApi.submit(room.value.id, {
            typed_text: typedText.value,
        });
        lastResult.value = data.result;
        room.value = data.room;
        submitted.value = true;
    } catch (err) {
        submitError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {})?.[0]?.[0] || 'Gagal mengirim hasil challenge.';
    } finally {
        submitting.value = false;
    }
}

watch(remainingSeconds, (value) => {
    if (value === 0 && !submitted.value && room.value?.status === 'playing') {
        submitResult();
    }
});

onMounted(async () => {
    loading.value = true;
    try {
        await loadRoom();
        timer = setInterval(refreshElapsed, 1000);
        poller = setInterval(pollRoom, 4000);
        progressTimer = setInterval(sendProgress, 2000);
        refreshElapsed();
        await nextTick();
        typingInput.value?.focus();
    } catch (err) {
        error.value = err.response?.data?.message || 'Gagal memuat challenge game.';
    } finally {
        loading.value = false;
    }
});

onBeforeUnmount(() => {
    if (timer) clearInterval(timer);
    if (poller) clearInterval(poller);
    if (progressTimer) clearInterval(progressTimer);
});
</script>
