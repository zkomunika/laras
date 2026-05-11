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

                    <RouterLink :to="`/story/levels/${level.id}`" class="btn btn-primary">
                        Mainkan
                    </RouterLink>
                </article>
            </div>
        </template>
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { chapterApi } from '@/services/chapterApi';

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
});

const chapter = ref(null);
const loading = ref(true);
const error = ref('');

onMounted(async () => {
    try {
        chapter.value = await chapterApi.detail(props.id);
    } catch (err) {
        error.value = 'Gagal memuat detail BAB.';
    } finally {
        loading.value = false;
    }
});
</script>