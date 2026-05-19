<template>
    <RouterView v-if="isAuthPage" />

    <div v-else class="shell">
        <nav class="sidebar">
            <RouterLink to="/" class="sidebar-logo">
                ⚔️<br>
                LARAS
            </RouterLink>

            <RouterLink to="/" class="nav-btn" title="Home">
                🏠
                <span class="tooltip">Home</span>
            </RouterLink>

            <RouterLink to="/chapters" class="nav-btn" title="Pilih Bab">
                📖
                <span class="tooltip">Pilih Bab</span>
            </RouterLink>

            <button
                class="nav-btn"
                type="button"
                title="Bermain"
                @click="goToLastUnlockedLevel"
            >
                🎮
                <span class="tooltip">Bermain</span>
            </button>

            <RouterLink
                v-if="auth.isAuthenticated"
                to="/challenge"
                class="nav-btn"
                title="Challenge"
            >
                ⚔️
                <span class="tooltip">Challenge</span>
            </RouterLink>

            <RouterLink to="/leaderboard" class="nav-btn" title="Leaderboard">
                🏆
                <span class="tooltip">Papan Peringkat</span>
            </RouterLink>

            <RouterLink
                v-if="auth.isAuthenticated"
                to="/realtime"
                class="nav-btn"
                title="Realtime"
            >
                💬
                <span class="tooltip">Realtime</span>
            </RouterLink>

            <RouterLink
                v-if="auth.isAuthenticated"
                to="/settings"
                class="nav-btn"
                title="Settings"
            >
                ⚙️
                <span class="tooltip">Settings</span>
            </RouterLink>

            <div class="sidebar-bottom">
                <RouterLink
                    v-if="auth.isAuthenticated"
                    to="/profile"
                    class="nav-btn"
                    title="Profil"
                >
                    👤
                    <span class="tooltip">Profil</span>
                </RouterLink>

                <RouterLink
                    v-if="!auth.isAuthenticated"
                    to="/login"
                    class="nav-btn"
                    title="Login"
                >
                    🔓
                    <span class="tooltip">Login</span>
                </RouterLink>

                <button
                    v-if="auth.isAuthenticated"
                    class="nav-btn"
                    type="button"
                    title="Logout"
                    @click="handleLogout"
                >
                    🚪
                    <span class="tooltip">Keluar</span>
                </button>
            </div>
        </nav>

        <main class="main">
            <RouterView />
        </main>
    </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import { useThemeStore } from '@/stores/themeStore';
import { useAudioStore } from '@/stores/audioStore';
import { storyResumeService } from '@/services/storyResumeService';

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();
const theme = useThemeStore();
const audio = useAudioStore();

const isAuthPage = computed(() => {
    return ['login', 'register'].includes(route.name);
});

function handleSessionExpired() {
    auth.clearSession();
    if (!['login', 'register'].includes(route.name)) {
        router.push('/login');
    }
}

onMounted(async () => {
    theme.init();
    audio.init();
    window.addEventListener('laras:session-expired', handleSessionExpired);

    if (auth.token) {
        await auth.checkSession();
    }
});

onBeforeUnmount(() => {
    window.removeEventListener('laras:session-expired', handleSessionExpired);
});

async function goToLastUnlockedLevel() {
    if (!auth.isAuthenticated) {
        router.push('/login');
        return;
    }

    try {
        const path = await storyResumeService.getLastUnlockedLevelPath();
        router.push(path);
    } catch (error) {
        router.push('/story/levels/1');
    }
}

async function handleLogout() {
    await auth.logout();
    router.push('/login');
}
</script>