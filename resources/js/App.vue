<template>
    <div class="app-shell">
        <header class="topbar">
            <RouterLink to="/" class="brand">
                <span class="brand-mark">ᮜ</span>

                <span class="brand-text">
                    <strong>LARAS</strong>
                    <small>Ladang Aksara Siliwangi</small>
                </span>
            </RouterLink>

            <nav class="nav-menu">
                <RouterLink to="/">Home</RouterLink>
                <RouterLink to="/chapters">Chapter</RouterLink>
                <RouterLink to="/leaderboard">Leaderboard</RouterLink>
                <RouterLink v-if="auth.isAuthenticated" to="/profile">Profile</RouterLink>
                <RouterLink v-if="!auth.isAuthenticated" to="/login">Login</RouterLink>
                <RouterLink v-if="!auth.isAuthenticated" to="/register">Register</RouterLink>

                <button
                    v-if="auth.isAuthenticated"
                    class="nav-button"
                    type="button"
                    @click="handleLogout"
                >
                    Logout
                </button>
            </nav>
        </header>

        <main class="main-content">
            <RouterView />
        </main>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const router = useRouter();
const auth = useAuthStore();

async function handleLogout() {
    await auth.logout();
    router.push('/login');
}
</script>