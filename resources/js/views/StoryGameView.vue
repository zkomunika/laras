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
                    <span>Target WPM: {{ level.target_wpm }}</span>
                    <span>Akurasi Minimal: {{ level.min_accuracy }}%</span>
                    <span>Waktu: {{ level.time_limit_seconds }} detik</span>
                </div>

                <div class="target-text-box">
                    {{ level.target_text }}
                </div>

                <div class="game-placeholder">
                    <p>
                        Typing engine akan dibuat pada tahap berikutnya.
                        Saat ini level sudah berhasil diambil dari database melalui API.
                    </p>

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

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
});

const level = ref(null);
const loading = ref(true);
const error = ref('');

onMounted(async () => {
    try {
        level.value = await levelApi.detail(props.id);
    } catch (err) {
        error.value = 'Gagal memuat level.';
    } finally {
        loading.value = false;
    }
});
</script>