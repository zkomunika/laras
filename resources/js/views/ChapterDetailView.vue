<template>
    <section class="page-section">
        <div v-if="loading" class="empty-state">
            Memuat detail BAB...
        </div>

        <div v-else-if="error" class="empty-state">
            {{ error }}
        </div>

        <template v-else>
            <div>
                <p class="eyebrow">BAB {{ chapter.number }}</p>
                <h1>{{ chapter.title }}</h1>
                <p class="page-description">{{ chapter.description }}</p>
            </div>

            <div class="level-grid">
                <article
                    v-for="level in chapter.levels"
                    :key="level.id"
                    class="level-card"
                    :class="{
                        'level-locked': !isLevelUnlocked(level),
                        'level-completed': getProgress(level)?.is_completed,
                    }"
                >
                    <span class="chapter-number">
                        Level {{ level.level_number }}
                    </span>

                    <h2>{{ level.title }}</h2>

                    <p>{{ level.story_text }}</p>

                    <div class="level-meta">
                        <span>Target WPM: {{ level.target_wpm }}</span>
                        <span>Akurasi: {{ level.min_accuracy }}%</span>
                        <span v-if="level.is_boss_level">Boss Level</span>
                    </div>

                    <div v-if="getProgress(level)" class="progress-summary">
                        <strong>Best Score: {{ getProgress(level).best_score }}</strong>
                        <span>Best WPM: {{ getProgress(level).best_wpm }}</span>
                        <span>Best Accuracy: {{ getProgress(level).best_accuracy }}%</span>

                        <div class="stars-mini">
                            <span
                                v-for="star in 3"
                                :key="star"
                                :class="{ active: star <= getProgress(level).best_stars }"
                            >
                                ★
                            </span>
                        </div>
                    </div>

                    <RouterLink
                        v-if="isLevelUnlocked(level)"
                        :to="`/story/levels/${level.id}`"
                        class="btn btn-primary"
                    >
                        Mainkan
                    </RouterLink>

                    <span v-else class="btn btn-disabled">
                        Terkunci
                    </span>
                </article>
            </div>
        </template>
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { chapterApi } from '@/services/chapterApi';
import { progressApi } from '@/services/progressApi';

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
});

const chapter = ref(null);
const progressMap = ref({});
const loading = ref(true);
const error = ref('');

onMounted(async () => {
    try {
        const [chapterData, progressData] = await Promise.all([
            chapterApi.detail(props.id),
            progressApi.list(),
        ]);

        chapter.value = chapterData;

        progressMap.value = progressData.reduce((map, item) => {
            map[item.level_id] = item;
            return map;
        }, {});
    } catch (err) {
        error.value = 'Gagal memuat detail BAB.';
    } finally {
        loading.value = false;
    }
});

function getProgress(level) {
    return progressMap.value[level.id] ?? null;
}

function isLevelUnlocked(level) {
    if (level.level_number === 1) {
        return true;
    }

    const progress = getProgress(level);

    return Boolean(progress?.unlocked_at || progress?.is_completed);
}
</script>