<template>
    <section class="page-section">
        <div>
            <p class="eyebrow">Profile</p>
            <h1>Profil Pemain</h1>
        </div>

        <div v-if="loading" class="empty-state">
            Memuat profil...
        </div>

        <div v-else-if="error" class="empty-state">
            {{ error }}
        </div>

        <div v-else class="profile-panel">
            <div>
                <p class="eyebrow">Demo Player</p>
                <h2>{{ profile.user.name }}</h2>
                <p>{{ profile.user.email }}</p>
            </div>

            <div class="profile-stats">
                <div>
                    <strong>{{ profile.stats.completed_levels }}</strong>
                    <span>Level Selesai</span>
                </div>

                <div>
                    <strong>{{ profile.stats.unlocked_levels }}</strong>
                    <span>Level Terbuka</span>
                </div>

                <div>
                    <strong>{{ profile.stats.total_attempts }}</strong>
                    <span>Total Percobaan</span>
                </div>

                <div>
                    <strong>{{ profile.stats.best_wpm }}</strong>
                    <span>Best WPM</span>
                </div>

                <div>
                    <strong>{{ profile.stats.average_accuracy }}%</strong>
                    <span>Rata-rata Akurasi</span>
                </div>

                <div>
                    <strong>{{ profile.stats.total_score }}</strong>
                    <span>Total Score</span>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { profileApi } from '@/services/profileApi';

const profile = ref(null);
const loading = ref(true);
const error = ref('');

onMounted(async () => {
    try {
        profile.value = await profileApi.show();
    } catch (err) {
        error.value = 'Gagal memuat profil.';
    } finally {
        loading.value = false;
    }
});
</script>