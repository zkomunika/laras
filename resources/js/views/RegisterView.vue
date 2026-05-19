<template>
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-logo">
                <div style="font-size:36px;margin-bottom:8px;">⚔️</div>
                <h1>LARAS</h1>
                <p>Ladang Aksara Siliwangi</p>
                <p style="margin-top:4px;font-size:12px;">
                    Buat akun untuk memulai perjalanan aksara.
                </p>
            </div>

            <form @submit.prevent="submitRegister">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="form-input"
                        placeholder="Nama kamu"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="form-input"
                        placeholder="nama@example.com"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        class="form-input"
                        placeholder="Minimal 6 karakter"
                        minlength="6"
                        required
                    >
                </div>

                <p v-if="error" class="form-error">
                    {{ error }}
                </p>

                <button class="btn btn-primary btn-full" type="submit" :disabled="loading">
                    {{ loading ? 'Memproses...' : '✨ Daftar' }}
                </button>
            </form>

            <div class="auth-switch">
                Sudah punya akun?
                <RouterLink to="/login">Masuk di sini</RouterLink>
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
    name: '',
    email: '',
    password: '',
});

async function submitRegister() {
    loading.value = true;
    error.value = '';

    try {
        audio.startMusic();
        await auth.register(form);
        audio.startMusic();
        router.push('/');
    } catch (err) {
        audio.stopMusic({ reset: true });
        error.value = err.response?.data?.message || 'Register gagal.';
    } finally {
        loading.value = false;
    }
}
</script>