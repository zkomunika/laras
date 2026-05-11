<template>
    <section class="auth-page">
        <div class="auth-card">
            <p class="eyebrow">Masuk Akun</p>
            <h1>Login LARAS</h1>

            <form class="auth-form" @submit.prevent="submitLogin">
                <label>
                    Email
                    <input v-model="form.email" type="email" required>
                </label>

                <label>
                    Password
                    <input v-model="form.password" type="password" required>
                </label>

                <p v-if="error" class="form-error">{{ error }}</p>

                <button class="btn btn-primary" type="submit" :disabled="loading">
                    {{ loading ? 'Memproses...' : 'Login' }}
                </button>
            </form>

            <p class="auth-switch">
                Belum punya akun?
                <RouterLink to="/register">Register</RouterLink>
            </p>
        </div>
    </section>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const router = useRouter();
const auth = useAuthStore();

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
        await auth.login(form);
        router.push('/chapters');
    } catch (err) {
        error.value = err.response?.data?.message || 'Login gagal.';
    } finally {
        loading.value = false;
    }
}
</script>