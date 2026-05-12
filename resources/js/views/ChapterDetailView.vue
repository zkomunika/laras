<template>
    <section>
        <div class="topbar">
            <RouterLink to="/chapters" class="btn btn-secondary topbar-back">
                <ChevronLeft :size="16" style="vertical-align:text-bottom" /> Kembali
            </RouterLink>

            <span class="topbar-title">
                <BookOpen :size="18" style="vertical-align:text-bottom" /> {{ chapter ? `BAB ${chapter.number} · ${chapter.title}` : 'Detail BAB' }}
            </span>

            <div class="topbar-right">
                <span class="tag-pill">{{ completedCount }} / 10 selesai</span>
            </div>
        </div>

        <div class="pad">
            <div v-if="loading" class="empty-state">
                Memuat detail BAB...
            </div>

            <div v-else-if="error" class="empty-state">
                {{ error }}
            </div>

            <div v-else class="chapter-layout">
                <div>
                    <div class="chapter-card selected">
                        <div class="chap-top">
                            <span class="chap-num">BAB {{ chapter.number }}</span>
                            <span class="chap-title">{{ chapter.title }}</span>
                            <span v-if="completedCount === 10" style="color:var(--green);font-size:13px;display:flex;align-items:center;gap:4px;">
                                <Check :size="14" /> Selesai
                            </span>
                        </div>

                        <p class="chap-desc">
                            {{ chapter.description }}
                        </p>

                        <div class="chap-checks">
                            <div
                                v-for="level in chapter.levels"
                                :key="level.id"
                                class="chap-check"
                                :class="{ done: getProgress(level)?.is_completed }"
                            >
                                <Check v-if="getProgress(level)?.is_completed" :size="12" />
                            </div>
                        </div>

                        <div class="chap-meta">
                            <span><Target :size="14" style="vertical-align:middle" /> {{ completedCount }} level selesai</span>
                            <span><BarChart2 :size="14" style="vertical-align:middle" /> {{ chapter.levels.length }} level tersedia</span>
                        </div>

                        <div class="chap-progress">
                            <div style="font-size:12px;color:var(--muted);margin-bottom:4px;">
                                Progress: {{ completedCount }} / 10 level
                            </div>

                            <div class="prog-bar">
                                <div
                                    class="prog-fill"
                                    :style="{ width: `${completedPercent}%` }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="sec-head">Pilih Level</div>

                        <div class="level-tile-grid">
                            <RouterLink
                                v-for="level in chapter.levels"
                                :key="level.id"
                                :to="isLevelUnlocked(level) ? `/story/levels/${level.id}` : '#'"
                                class="level-tile"
                                :class="{
                                    locked: !isLevelUnlocked(level),
                                    completed: getProgress(level)?.is_completed,
                                }"
                                @click.prevent="handleLevelClick(level)"
                            >
                                <div class="lv-num">{{ level.level_number }}</div>

                                <div class="lv-stars">
                                    <span
                                        v-for="star in 3"
                                        :key="star"
                                        :class="{ active: star <= (getProgress(level)?.best_stars || 0) }"
                                    >
                                        <Star :size="12" :fill="star <= (getProgress(level)?.best_stars || 0) ? 'currentColor' : 'none'" />
                                    </span>
                                </div>

                                <div class="lv-pts">
                                    {{ getProgress(level)?.best_score || 0 }} pts
                                </div>
                            </RouterLink>
                        </div>
                    </div>

                    <div class="card">
                        <div class="sec-head">Daftar Level</div>

                        <article
                            v-for="level in chapter.levels"
                            :key="level.id"
                            class="level-row-card"
                            :class="{
                                locked: !isLevelUnlocked(level),
                                completed: getProgress(level)?.is_completed,
                            }"
                        >
                            <div>
                                <span class="chapter-number">
                                    Level {{ level.level_number }}
                                </span>

                                <h2>{{ level.title }}</h2>

                                <p>{{ level.story_text }}</p>

                                <div class="level-meta">
                                    <span>Target WPM: {{ level.target_wpm }}</span>
                                    <span>Akurasi: {{ level.min_accuracy }}%</span>
                                    <span v-if="level.is_boss_level"><Swords :size="14" style="vertical-align:middle" /> Boss Level</span>
                                </div>
                            </div>

                            <div class="level-row-action">
                                <div v-if="getProgress(level)" class="progress-summary">
                                    <strong>{{ getProgress(level).best_score }} pts</strong>
                                    <span>{{ getProgress(level).best_wpm }} WPM</span>
                                    <span>{{ getProgress(level).best_accuracy }}%</span>
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
                            </div>
                        </article>
                    </div>
                </div>

                <aside class="detail-panel">
                    <h4>BAB {{ chapter.number }} — Detail</h4>

                    <p style="font-size:11px;color:var(--muted);margin-bottom:10px;font-family:var(--font-mono);">
                        LATAR BELAKANG
                    </p>

                    <p class="detail-narrative">
                        {{ chapter.description }}
                    </p>

                    <div class="detail-meta"><Gamepad2 :size="14" style="vertical-align:middle" /> <strong>{{ chapter.levels.length }} Level</strong></div>
                    <div class="detail-meta"><Star :size="14" style="vertical-align:middle" /> Kesulitan bertahap</div>
                    <div class="detail-meta"><Timer :size="14" style="vertical-align:middle" /> Waktu menyesuaikan level</div>
                    <div class="detail-meta"><Trophy :size="14" style="vertical-align:middle" /> Progress tersimpan otomatis</div>

                    <div style="margin-top:16px;">
                        <div class="sec-head">Rekomendasi</div>

                        <RouterLink
                            :to="`/story/levels/${firstPlayableLevel.id}`"
                            class="btn btn-primary"
                            style="width:100%; display:flex; align-items:center; justify-content:center; gap:8px;"
                        >
                            <Play :size="16" /> Mainkan Level
                        </RouterLink>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { chapterApi } from '@/services/chapterApi';
import { progressApi } from '@/services/progressApi';
import { ChevronLeft, BookOpen, Check, Target, BarChart2, Star, Swords, Gamepad2, Timer, Trophy, Play } from 'lucide-vue-next';

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
});

const router = useRouter();

const chapter = ref(null);
const progressMap = ref({});
const loading = ref(true);
const error = ref('');

const completedCount = computed(() => {
    if (!chapter.value) {
        return 0;
    }

    return chapter.value.levels.filter((level) => {
        return getProgress(level)?.is_completed;
    }).length;
});

const completedPercent = computed(() => {
    return Math.round((completedCount.value / 10) * 100);
});

const firstPlayableLevel = computed(() => {
    if (!chapter.value) {
        return { id: 1 };
    }

    return chapter.value.levels.find((level) => isLevelUnlocked(level)) || chapter.value.levels[0];
});

onMounted(async () => {
    try {
        const [chapterData, progressData] = await Promise.all([
            chapterApi.detail(props.id),
            progressApi.list().catch(() => []),
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

function handleLevelClick(level) {
    if (!isLevelUnlocked(level)) {
        return;
    }

    router.push(`/story/levels/${level.id}`);
}
</script>