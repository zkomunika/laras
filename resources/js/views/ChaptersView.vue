<template>
    <section class="page-section">
        <div>
            <p class="eyebrow">Story Mode</p>
            <h1>Daftar BAB</h1>
        </div>

        <div v-if="loading" class="empty-state">
            Memuat data BAB...
        </div>

        <div v-else-if="error" class="empty-state">
            {{ error }}
        </div>

        <div v-else class="chapter-grid">
            <article
                v-for="chapter in chapters"
                :key="chapter.id"
                class="chapter-card"
            >
                <span class="chapter-number">BAB {{ chapter.number }}</span>

                <h2>{{ chapter.title }}</h2>

                <p>{{ chapter.description }}</p>

                <div class="chapter-meta">
                    {{ chapter.levels_count }} level
                </div>

                <div class="card-actions">
                    <RouterLink :to="`/chapters/${chapter.id}`" class="text-link">
                        Lihat Level
                    </RouterLink>

                    <RouterLink :to="`/story/levels/${chapter.start_level}`" class="text-link">
                        Mulai
                    </RouterLink>
                </div>
            </article>
        </div>
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { chapterApi } from '@/services/chapterApi';

const chapters = ref([]);
const loading = ref(true);
const error = ref('');

onMounted(async () => {
    try {
        chapters.value = await chapterApi.list();
    } catch (err) {
        error.value = 'Gagal memuat data BAB. Pastikan server Laravel dan database aktif.';
    } finally {
        loading.value = false;
    }
});
</script>