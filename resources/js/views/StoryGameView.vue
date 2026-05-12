<template>
    <section>
        <div class="topbar">
            <RouterLink :to="chapterBackUrl" class="btn btn-secondary topbar-back">
                ◀ Kembali
            </RouterLink>

            <span class="topbar-title">
                ⌨️ {{ level ? `Level ${level.level_number} · ${level.title}` : 'Story Game' }}
            </span>

            <div class="topbar-right">
                <ThemeToggle />
                <span class="tag-pill">{{ statusLabel }}</span>
                <span v-if="level?.is_boss_level" class="tag-pill teal">Boss Level</span>
            </div>
        </div>

        <div class="pad">
            <div v-if="loading" class="empty-state">
                Memuat level permainan...
            </div>

            <div v-else-if="error" class="empty-state">
                {{ error }}
            </div>

            <div v-else class="game-page-grid">
                <main class="game-panel">
                    <div class="game-header">
                        <div
                            class="timer-circle"
                            :class="{ danger: remainingSeconds !== null && remainingSeconds <= 10 }"
                        >
                            {{ timerLabel }}
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
                                <div class="lbl">Salah Ketik</div>
                            </div>

                            <div class="game-stat">
                                <div class="val">{{ score }}</div>
                                <div class="lbl">Skor</div>
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
                            :class="getCharacterClass(index)"
                        >{{ character }}</span>
                    </div>

                    <div class="typing-area">
                        <textarea
                            ref="typingInput"
                            :value="typedText"
                            :disabled="!attemptId || inputDisabled || saving"
                            class="typing-input"
                            spellcheck="false"
                            autocomplete="off"
                            autocorrect="off"
                            autocapitalize="off"
                            :placeholder="inputPlaceholder"
                            @input="onTyping"
                        ></textarea>

                        <div class="typing-hint">
                            Tekan Mulai, lalu ketik teks target sampai selesai. Backend akan mengecek teks, WPM, akurasi, batas waktu, dan jumlah salah.
                        </div>
                    </div>

                    <div v-if="submitError" class="form-error">
                        {{ submitError }}
                    </div>

                    <div class="game-footer">
                        <button
                            v-if="!attemptId"
                            type="button"
                            class="btn btn-primary"
                            :disabled="starting"
                            @click="handleStart"
                        >
                            {{ starting ? 'Memulai...' : 'Mulai Level' }}
                        </button>

                        <button
                            v-if="attemptId && status === 'playing'"
                            type="button"
                            class="btn btn-secondary"
                            :disabled="saving"
                            @click="finishCurrentAttempt"
                        >
                            Akhiri & Simpan
                        </button>

                        <button
                            type="button"
                            class="btn btn-secondary"
                            :disabled="saving"
                            @click="retryLevel"
                        >
                            Ulangi
                        </button>

                        <RouterLink
                            v-if="nextLevel"
                            :to="`/story/levels/${nextLevel.id}`"
                            class="btn btn-secondary"
                        >
                            Level Berikutnya
                        </RouterLink>
                    </div>
                </main>

                <aside class="detail-panel game-side-card">
                    <CharacterAvatar :state="characterState" />

                    <h4>Info Level</h4>

                    <p style="font-size:11px;color:var(--muted);margin-bottom:10px;font-family:var(--font-mono);">
                        BAB {{ level.chapter?.number || '-' }} · LEVEL {{ level.level_number }}
                    </p>

                    <p class="detail-narrative">
                        {{ level.story_text }}
                    </p>

                    <div class="detail-meta">🎯 Target WPM: <strong>{{ level.target_wpm }}</strong></div>
                    <div class="detail-meta">✅ Minimum Akurasi: <strong>{{ level.min_accuracy }}%</strong></div>
                    <div class="detail-meta">⏱️ Batas Waktu: <strong>{{ level.time_limit_seconds }} detik</strong></div>
                    <div class="detail-meta">❌ Maksimal Salah: <strong>{{ level.max_mistakes }}</strong></div>

                    <div class="rule-check-card">
                        <strong>Syarat Lulus</strong>
                        <div class="rule-row" :class="{ ok: typedText === targetText && targetText.length > 0 }">
                            <span>Teks sama persis</span>
                            <b>{{ typedText.length }} / {{ targetText.length }}</b>
                        </div>
                        <div class="rule-row" :class="{ ok: wpm >= Number(level.target_wpm) }">
                            <span>WPM minimal</span>
                            <b>{{ Math.round(wpm) }} / {{ level.target_wpm }}</b>
                        </div>
                        <div class="rule-row" :class="{ ok: accuracy >= Number(level.min_accuracy) }">
                            <span>Akurasi minimal</span>
                            <b>{{ accuracy }}% / {{ level.min_accuracy }}%</b>
                        </div>
                        <div class="rule-row" :class="{ ok: level.max_mistakes === null || mistakes <= Number(level.max_mistakes) }">
                            <span>Batas salah</span>
                            <b>{{ mistakes }} / {{ level.max_mistakes ?? '-' }}</b>
                        </div>
                        <div class="rule-row" :class="{ ok: remainingSeconds === null || remainingSeconds > 0 || typedText === targetText }">
                            <span>Batas waktu</span>
                            <b>{{ timerLabel }} detik</b>
                        </div>
                    </div>

                    <div v-if="currentProgress" class="progress-summary" style="margin-top:16px;">
                        <strong>Progress terbaik</strong>
                        <span>{{ currentProgress.best_score }} pts</span>
                        <span>{{ currentProgress.best_wpm }} WPM</span>
                        <span>{{ currentProgress.best_accuracy }}%</span>
                        <span>{{ currentProgress.best_stars }} bintang</span>
                    </div>

                    <div v-if="submitResult" class="result-panel compact-result">
                        <div class="sec-head">Hasil Terakhir</div>

                        <div class="result-status-pill" :class="{ success: submitResult.completed }">
                            {{ submitResult.completed ? 'Lulus' : 'Belum lulus' }}
                        </div>

                        <div class="result-score">{{ submitResult.score }}</div>

                        <div class="result-stars">
                            <span
                                v-for="star in 3"
                                :key="star"
                                :class="{ active: star <= submitResult.stars }"
                            >★</span>
                        </div>

                        <div class="result-grid">
                            <div>
                                <strong>{{ submitResult.wpm }}</strong>
                                <span>WPM</span>
                            </div>
                            <div>
                                <strong>{{ submitResult.accuracy }}%</strong>
                                <span>Akurasi</span>
                            </div>
                            <div>
                                <strong>{{ submitResult.mistakes }}</strong>
                                <span>Salah</span>
                            </div>
                        </div>

                        <div v-if="submitResult.new_achievements?.length" class="new-achievements">
                            <strong>Achievement Baru</strong>
                            <div
                                v-for="achievement in submitResult.new_achievements"
                                :key="achievement.code"
                                class="new-achievement-item"
                            >
                                <span>{{ achievement.icon }}</span>
                                <div>
                                    <b>{{ achievement.name }}</b>
                                    <small>{{ achievement.description }}</small>
                                </div>
                            </div>
                        </div>

                        <div v-if="submitResult.failed_rules?.length" class="failed-rules">
                            <strong>Yang belum terpenuhi</strong>
                            <ul>
                                <li v-for="rule in submitResult.failed_rules" :key="rule.code">
                                    {{ rule.message }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { gameApi } from '@/services/gameApi';
import { levelApi } from '@/services/levelApi';
import { progressApi } from '@/services/progressApi';
import { useTypingGame } from '@/composables/useTypingGame';
import CharacterAvatar from '@/components/CharacterAvatar.vue';
import ThemeToggle from '@/components/ThemeToggle.vue';
import { useAudioStore } from '@/stores/audioStore';
import { useAuthStore } from '@/stores/authStore';

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
});

const route = useRoute();
const router = useRouter();
const audio = useAudioStore();
const auth = useAuthStore();

const level = ref(null);
const levels = ref([]);
const progressMap = ref({});
const attemptId = ref(null);
const submitResult = ref(null);
const loading = ref(true);
const starting = ref(false);
const saving = ref(false);
const submitted = ref(false);
const error = ref('');
const submitError = ref('');
const typingInput = ref(null);
const feedbackState = ref('idle');
let feedbackTimer = null;

const {
    targetText,
    targetCharacters,
    typedText,
    status,
    statusLabel,
    elapsedSeconds,
    remainingSeconds,
    mistakes,
    officialMistakeEstimate,
    accuracy,
    wpm,
    score,
    inputDisabled,
    setupGame,
    resetGame,
    startGame,
    finishGame,
    updateTypedText,
    getCharacterClass,
} = useTypingGame();

const characterState = computed(() => {
    if (feedbackState.value === 'mistake' || feedbackState.value === 'combo') {
        return feedbackState.value;
    }

    if (submitResult.value) {
        return submitResult.value.completed ? 'win' : 'lose';
    }

    if (status.value === 'failed') {
        return 'lose';
    }

    if (status.value === 'finished') {
        return 'win';
    }

    if (status.value === 'playing') {
        return typedText.value.length > 0 ? 'typing' : 'idle';
    }

    return 'idle';
});

const chapterBackUrl = computed(() => {
    if (!level.value?.chapter_id) {
        return '/chapters';
    }

    return `/chapters/${level.value.chapter_id}`;
});

const currentProgress = computed(() => {
    if (!level.value) {
        return null;
    }

    return progressMap.value[level.value.id] ?? null;
});

const nextLevel = computed(() => {
    if (!level.value || !submitResult.value?.completed) {
        return null;
    }

    return levels.value.find((item) => item.level_number === level.value.level_number + 1) ?? null;
});

const progressPercent = computed(() => {
    if (!targetText.value.length) {
        return 0;
    }

    return Math.min(100, Math.round((typedText.value.length / targetText.value.length) * 100));
});

const timerLabel = computed(() => {
    if (remainingSeconds.value !== null) {
        return remainingSeconds.value;
    }

    return Math.floor(elapsedSeconds.value);
});

const inputPlaceholder = computed(() => {
    if (!attemptId.value) {
        return 'Klik Mulai Level untuk mengaktifkan area ketik.';
    }

    if (saving.value) {
        return 'Menyimpan hasil...';
    }

    return 'Mulai ketik teks target di sini...';
});

onMounted(() => {
    loadLevel();
});

watch(
    () => route.params.id,
    () => {
        loadLevel();
    }
);

watch(status, (newStatus) => {
    if (newStatus === 'finished') {
        audio.playSfx('win');
    }

    if (newStatus === 'failed') {
        audio.playSfx('lose');
    }

    if ((newStatus === 'finished' || newStatus === 'failed') && attemptId.value && !submitted.value) {
        submitAttempt();
    }
});

async function loadLevel() {
    loading.value = true;
    error.value = '';
    submitError.value = '';
    attemptId.value = null;
    submitResult.value = null;
    submitted.value = false;
    setFeedbackState('idle', 0);
    resetGame();

    try {
        const [levelData, allLevels, progressData] = await Promise.all([
            levelApi.detail(props.id),
            levelApi.list().catch(() => []),
            progressApi.list().catch(() => []),
        ]);

        level.value = levelData;
        levels.value = allLevels;
        progressMap.value = progressData.reduce((map, item) => {
            map[item.level_id] = item;
            return map;
        }, {});

        if (!isLevelUnlocked(levelData)) {
            error.value = 'Level ini masih terkunci. Selesaikan level sebelumnya dulu.';
            level.value = null;
            return;
        }

        setupGame(levelData);
    } catch (err) {
        error.value = 'Gagal memuat level permainan.';
    } finally {
        loading.value = false;
    }
}

function isLevelUnlocked(levelData) {
    if (levelData.level_number === 1) {
        return true;
    }

    const progress = progressMap.value[levelData.id];

    return Boolean(progress?.unlocked_at || progress?.is_completed);
}

async function handleStart() {
    if (!level.value || starting.value) {
        return;
    }

    starting.value = true;
    submitError.value = '';
    submitResult.value = null;
    submitted.value = false;
    setFeedbackState('idle', 0);
    resetGame();

    try {
        const attempt = await gameApi.start(level.value.id);
        attemptId.value = attempt.id;
        audio.startMusic();
        audio.playSfx('click');
        startGame();

        await nextTick();
        typingInput.value?.focus();
    } catch (err) {
        if (err.response?.status === 401) {
            auth.clearSession();
            submitError.value = 'Sesi login habis. Silakan login ulang.';
            router.push('/login');
            return;
        }

        submitError.value = err.response?.data?.message || 'Gagal memulai level. Pastikan kamu sudah login.';
    } finally {
        starting.value = false;
    }
}

function onTyping(event) {
    const previousLength = typedText.value.length;
    updateTypedText(event.target.value);

    if (typedText.value.length <= previousLength || !typedText.value.length) {
        return;
    }

    const latestIndex = typedText.value.length - 1;
    const isCorrect = typedText.value[latestIndex] === targetText.value[latestIndex];

    if (!isCorrect) {
        audio.playSfx('wrong');
        setFeedbackState('mistake');
        return;
    }

    const cleanCombo = typedText.value.length >= 8 && mistakes.value === 0 && typedText.value.length % 8 === 0;
    audio.playSfx(cleanCombo ? 'combo' : 'correct');
    setFeedbackState(cleanCombo ? 'combo' : 'typing');
}

function finishCurrentAttempt() {
    if (targetText.value === typedText.value) {
        finishGame('finished');
        return;
    }

    finishGame('failed');
}

async function submitAttempt() {
    if (!attemptId.value || submitted.value) {
        return;
    }

    saving.value = true;
    submitError.value = '';
    submitted.value = true;

    try {
        submitResult.value = await gameApi.submit(attemptId.value, typedText.value);
        await refreshProgress();
    } catch (err) {
        submitted.value = false;

        if (err.response?.status === 401) {
            auth.clearSession();
            submitError.value = 'Sesi login habis. Silakan login ulang.';
            router.push('/login');
            return;
        }

        submitError.value = err.response?.data?.message || 'Hasil gagal disimpan. Coba klik Ulangi lalu mainkan lagi.';
    } finally {
        saving.value = false;
    }
}

async function refreshProgress() {
    const progressData = await progressApi.list().catch(() => []);

    progressMap.value = progressData.reduce((map, item) => {
        map[item.level_id] = item;
        return map;
    }, {});
}

function retryLevel() {
    attemptId.value = null;
    submitResult.value = null;
    submitError.value = '';
    submitted.value = false;
    setFeedbackState('idle', 0);
    resetGame();
}

function setFeedbackState(state, duration = 480) {
    feedbackState.value = state;

    if (feedbackTimer) {
        clearTimeout(feedbackTimer);
        feedbackTimer = null;
    }

    if (duration > 0) {
        feedbackTimer = setTimeout(() => {
            feedbackState.value = 'idle';
            feedbackTimer = null;
        }, duration);
    }
}
</script>
