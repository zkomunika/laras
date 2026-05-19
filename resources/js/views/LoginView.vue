<template>
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-logo">
                <div style="font-size:36px;margin-bottom:8px;">⚔️</div>
                <h1>LARAS</h1>
                <p>Ladang Aksara Siliwangi</p>
                <p style="margin-top:4px;font-size:12px;">
                    Game Typing Interaktif · Universitas Siliwangi
                </p>
            </div>

            <form @submit.prevent="submitLogin">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="form-input"
                        placeholder="user@example.com"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        class="form-input"
                        placeholder="••••••••"
                        required
                    >
                </div>

                <p v-if="error" class="form-error">
                    {{ error }}
                </p>

                <button class="btn btn-primary btn-full" type="submit" :disabled="loading">
                    {{ loading ? 'Memproses...' : '🔓 Login' }}
                </button>
            </form>

            <div class="auth-switch">
                Belum punya akun?
                <RouterLink to="/register">Daftar di sini</RouterLink>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import { useAudioStore } from '@/stores/audioStore';

const router = useRouter();
const auth = useAuthStore();
const audio = useAudioStore();

const loading = ref(false);
const error = ref('');

const form = reactive({
    email: '',
    password: '',
});

async function submitLogin() {
    loading.value = true;
    error.value = '';

    try {
        audio.startMusic();
        await auth.login(form);
        audio.startMusic();
        router.push('/');
    } catch (err) {
        audio.stopMusic({ reset: true });
        error.value = err.response?.data?.message || 'Login gagal.';
    } finally {
        loading.value = false;
    }
}
</script>