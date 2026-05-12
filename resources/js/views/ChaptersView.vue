<template>
    <section>
        <div class="topbar">
            <span class="topbar-title"><BookOpen :size="18" style="vertical-align:text-bottom"/> Pilih Bab</span>

            <div class="topbar-right">
                <span class="tag-pill">5 BAB</span>
                <span class="tag-pill">50 Level</span>
            </div>
        </div>

        <div class="pad">
            <div class="chapter-layout">
                <div>
                    <div class="sec-head">Daftar Bab</div>

                    <div v-if="loading" class="empty-state">
                        Memuat data BAB...
                    </div>

                    <div v-else-if="error" class="empty-state">
                        {{ error }}
                    </div>

                    <template v-else>
                        <article
                            v-for="chapter in chapters"
                            :key="chapter.id"
                            class="chapter-card"
                        >
                            <div class="chap-top">
                                <span class="chap-num">BAB {{ chapter.number }}</span>
                                <span class="chap-title">{{ chapter.title }}</span>
                            </div>

                            <p class="chap-desc">
                                {{ chapter.description }}
                            </p>

                            <div class="chap-meta">
                                <span><Gamepad2 :size="14" style="vertical-align:middle" /> {{ chapter.levels_count }} level</span>
                                <span><MapPin :size="14" style="vertical-align:middle" /> Level {{ chapter.start_level }}–{{ chapter.end_level }}</span>
                            </div>

                            <div class="chap-progress">
                                <div class="prog-bar">
                                    <div
                                        class="prog-fill"
                                        :style="{ width: getChapterProgress(chapter) + '%' }"
                                    ></div>
                                </div>
                            </div>

                            <div class="card-actions">
                                <RouterLink
                                    :to="`/chapters/${chapter.id}`"
                                    class="btn btn-primary"
                                >
                                    Lihat Level
                                </RouterLink>

                                <RouterLink
                                    :to="`/story/levels/${chapter.start_level}`"
                                    class="btn btn-secondary"
                                >
                                    Mulai
                                </RouterLink>
                            </div>
                        </article>
                    </template>
                </div>

                <aside class="detail-panel">
                    <h4>LARAS STORY MODE</h4>

                    <p class="detail-narrative">
                        Perjalanan juru aksara dimulai dari latihan dasar hingga ujian akhir
                        sebagai Pewaris Siliwangi. Setiap BAB memiliki 10 level dengan target WPM,
                        akurasi, dan batas waktu yang meningkat secara bertahap.
                    </p>

                    <div class="detail-meta"><Gamepad2 :size="14" style="vertical-align:middle" /> <strong>50 Level</strong></div>
                    <div class="detail-meta"><BookOpen :size="14" style="vertical-align:middle" /> 5 BAB Cerita</div>
                    <div class="detail-meta"><Swords :size="14" style="vertical-align:middle" /> Boss level setiap akhir BAB</div>
                    <div class="detail-meta"><Trophy :size="14" style="vertical-align:middle" /> Skor tersimpan ke leaderboard</div>

                    <div style="margin-top:16px;">
                        <div class="sec-head">Parameter Game</div>

                        <div class="overall-prog">
                            <div class="overall-prog-row">
                                <span class="overall-prog-name">WPM</span>
                                <div class="overall-prog-bar">
                                    <div class="overall-prog-fill f-gold" style="width:80%"></div>
                                </div>
                            </div>

                            <div class="overall-prog-row">
                                <span class="overall-prog-name">Akurasi</span>
                                <div class="overall-prog-bar">
                                    <div class="overall-prog-fill f-teal" style="width:90%"></div>
                                </div>
                            </div>

                            <div class="overall-prog-row">
                                <span class="overall-prog-name">Waktu</span>
                                <div class="overall-prog-bar">
                                    <div class="overall-prog-fill f-gold" style="width:65%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { chapterApi } from '@/services/chapterApi';
import { progressApi } from '@/services/progressApi';
import { BookOpen, Gamepad2, MapPin, Swords, Trophy } from 'lucide-vue-next';

const chapters = ref([]);
const progress = ref([]);
const loading = ref(true);
const error = ref('');

onMounted(async () => {
    try {
        const [chapterData, progressData] = await Promise.all([
            chapterApi.list(),
            progressApi.list().catch(() => []),
        ]);

        chapters.value = chapterData;
        progress.value = progressData;
    } catch (err) {
        error.value = 'Gagal memuat data BAB.';
    } finally {
        loading.value = false;
    }
});

function getChapterProgress(chapter) {
    const completed = progress.value.filter((item) => {
        return item.level_number >= chapter.start_level
            && item.level_number <= chapter.end_level
            && item.is_completed;
    }).length;

    return Math.round((completed / 10) * 100);
}
</script>