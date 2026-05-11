<template>
    <section class="page-section">
        <div v-if="loading" class="empty-state">
            Memuat level...
        </div>

        <div v-else-if="error" class="empty-state">
            {{ error }}
        </div>

        <template v-else>
            <div>
                <p class="eyebrow">
                    BAB {{ level.chapter?.number }} — Story Game
                </p>

                <h1>Level {{ level.level_number }}</h1>

                <p class="page-description">
                    {{ level.story_text }}
                </p>
            </div>

            <div class="game-panel">
                <div class="game-stat-row">
                    <span>Status: {{ statusLabel }}</span>
                    <span>Target WPM: {{ level.target_wpm }}</span>
                    <span>Waktu: {{ remainingSeconds }} detik</span>
                    <span>WPM: {{ wpm }}</span>
                    <span>Akurasi: {{ accuracy }}%</span>
                    <span>Kesalahan: {{ mistakes }}</span>
                    <span>Skor: {{ score }}</span>
                </div>

                <div class="target-text-box">
                    <span
                        v-for="(character, index) in targetCharacters"
                        :key="index"
                        class="target-char"
                        :class="getCharacterClass(index)"
                    >{{ character }}</span>
                </div>

                <textarea
                    class="typing-input"
                    :value="typedText"
                    :maxlength="targetText.length"
                    :disabled="inputDisabled"
                    placeholder="Mulai ketik teks di sini..."
                    autofocus
                    @input="handleTypingInput"
                    @paste.prevent
                ></textarea>

                <div
                    v-if="status === 'finished' || status === 'failed'"
                    class="result-panel"
                >
                    <p class="eyebrow">Hasil Permainan</p>

                    <h2 v-if="status === 'finished'">Level selesai!</h2>
                    <h2 v-else>Waktu habis!</h2>

                    <div class="result-grid">
                        <div>
                            <strong>{{ wpm }}</strong>
                            <span>WPM</span>
                        </div>

                        <div>
                            <strong>{{ accuracy }}%</strong>
                            <span>Akurasi</span>
                        </div>

                        <div>
                            <strong>{{ mistakes }}</strong>
                            <span>Kesalahan</span>
                        </div>

                        <div>
                            <strong>{{ score }}</strong>
                            <span>Skor</span>
                        </div>
                    </div>

                    <div class="stars-row">
                        <span
                            v-for="star in 3"
                            :key="star"
                            :class="{ active: star <= stars }"
                        >
                            ★
                        </span>
                    </div>
                </div>

                <div class="game-actions">
                    <button type="button" class="btn btn-primary" @click="resetGame">
                        Ulangi Level
                    </button>

                    <RouterLink :to="`/chapters/${level.chapter_id}`" class="btn btn-secondary">
                        Kembali ke BAB
                    </RouterLink>
                </div>
            </div>
        </template>
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { levelApi } from '@/services/levelApi';
import { useTypingGame } from '@/composables/useTypingGame';

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
});

const level = ref(null);
const loading = ref(true);
const error = ref('');

const {
    targetText,
    targetCharacters,
    typedText,
    status,
    statusLabel,
    remainingSeconds,
    mistakes,
    accuracy,
    wpm,
    score,
    stars,
    inputDisabled,
    setupGame,
    resetGame,
    updateTypedText,
    getCharacterClass,
} = useTypingGame();

onMounted(async () => {
    try {
        level.value = await levelApi.detail(props.id);
        setupGame(level.value);
    } catch (err) {
        error.value = 'Gagal memuat level.';
    } finally {
        loading.value = false;
    }
});

function handleTypingInput(event) {
    updateTypedText(event.target.value);
    event.target.value = typedText.value;
}
</script>