<template>
    <RouterView v-if="isAuthPage" />

    <div v-else class="shell">
        <nav class="sidebar">
            <RouterLink to="/" class="sidebar-logo">
                <Sword :size="24" /><br>
                LARAS
            </RouterLink>

            <RouterLink to="/" class="nav-btn" title="Home">
                <Home :size="20" />
                <span class="tooltip">Home</span>
            </RouterLink>

            <RouterLink to="/chapters" class="nav-btn" title="Pilih Bab">
                <BookOpen :size="20" />
                <span class="tooltip">Pilih Bab</span>
            </RouterLink>

            <RouterLink to="/story/levels/1" class="nav-btn" title="Bermain">
                <Gamepad2 :size="20" />
                <span class="tooltip">Bermain</span>
            </RouterLink>

            <RouterLink to="/leaderboard" class="nav-btn" title="Leaderboard">
                <Trophy :size="20" />
                <span class="tooltip">Papan Peringkat</span>
            </RouterLink>

            <RouterLink
                v-if="auth.isAuthenticated"
                to="/realtime"
                class="nav-btn"
                title="Realtime"
            >
                <MessageCircle :size="20" />
                <span class="tooltip">Realtime</span>
            </RouterLink>

            <div class="sidebar-bottom">
                <RouterLink
                    v-if="auth.isAuthenticated"
                    to="/profile"
                    class="nav-btn"
                    title="Profil"
                >
                    <User :size="20" />
                    <span class="tooltip">Profil</span>
                </RouterLink>

                <button
                    class="nav-btn"
                    type="button"
                    :title="isMuted ? 'Unmute' : 'Mute'"
                    @click="toggleMute"
                >
                    <Volume2 v-if="!isMuted" :size="20" />
                    <VolumeX v-else :size="20" />
                    <span class="tooltip">{{ isMuted ? 'Unmute' : 'Mute' }}</span>
                </button>

                <RouterLink
                    v-if="!auth.isAuthenticated"
                    to="/login"
                    class="nav-btn"
                    title="Login"
                >
                    <Unlock :size="20" />
                    <span class="tooltip">Login</span>
                </RouterLink>

                <button
                    v-if="auth.isAuthenticated"
                    class="nav-btn"
                    type="button"
                    title="Logout"
                    @click="handleLogout"
                >
                    <LogOut :size="20" />
                    <span class="tooltip">Keluar</span>
                </button>
            </div>
        </nav>

        <main class="main">
            <RouterView :key="route.fullPath" />
            
            <footer class="app-footer">
                <div class="footer-content">
                    <div class="footer-logo">
                        <Sword :size="20" />
                        <span>LARAS</span>
                    </div>
                    <p class="footer-text">Ladang Aksara Siliwangi &copy; 2026</p>
                    <p class="footer-sub">Diciptakan untuk melestarikan dan meningkatkan kecepatan mengetik</p>
                </div>
            </footer>
        </main>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import { 
    Home, BookOpen, Gamepad2, Trophy, MessageCircle, 
    User, Unlock, LogOut, Sword, Volume2, VolumeX 
} from 'lucide-vue-next';
import { soundService } from '@/services/soundService';

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();

const isMuted = ref(soundService.isMuted);

onMounted(() => {
    const startAudio = async () => {
        const success = await soundService.playBGM();
        if (success) {
            // Remove listeners once audio has started successfully
            ['click', 'keydown', 'touchstart'].forEach(event => {
                document.removeEventListener(event, startAudio);
            });
        }
    };

    ['click', 'keydown', 'touchstart'].forEach(event => {
        document.addEventListener(event, startAudio);
    });
});

function toggleMute() {
    isMuted.value = soundService.toggleMute();
}

const isAuthPage = computed(() => {
    return ['login', 'register'].includes(route.name);
});

async function handleLogout() {
    await auth.logout();
    router.push('/login');
}
</script>

<style scoped>
.app-footer {
    margin-top: auto;
    padding: 40px 20px;
    text-align: center;
    border-top: 1px solid var(--border);
    background: linear-gradient(to top, rgba(0,0,0,0.5), transparent);
}

.footer-content {
    max-width: 600px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.footer-logo {
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: var(--font-head);
    color: var(--gold);
    font-size: 18px;
    margin-bottom: 8px;
}

.footer-text {
    font-size: 14px;
    color: var(--text);
}

.footer-sub {
    font-size: 12px;
    color: var(--muted);
}
</style>