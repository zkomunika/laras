<template>
    <section class="auth-page">
        <div class="auth-card">
            <p class="eyebrow">Buat Akun</p>
            <h1>Register</h1>

            <form class="auth-form" @submit.prevent="submitRegister">
                <label>
                    Nama
                    <input v-model="form.name" type="text" required>
                </label>

                <label>
                    Email
                    <input v-model="form.email" type="email" required>
                </label>

                <label>
                    Password
                    <input v-model="form.password" type="password" minlength="6" required>
                </label>

                <p v-if="error" class="form-error">{{ error }}</p>

                <button class="btn btn-primary" type="submit" :disabled="loading">
                    {{ loading ? 'Memproses...' : 'Register' }}
                </button>
            </form>

            <p class="auth-switch">
                Sudah punya akun?
                <RouterLink to="/login">Login</RouterLink>
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
    name: '',
    email: '',
    password: '',
});

async function submitRegister() {
    loading.value = true;
    error.value = '';

    try {
        await auth.register(form);
        router.push('/chapters');
    } catch (err) {
        error.value = err.response?.data?.message || 'Register gagal.';
    } finally {
        loading.value = false;
    }
}
</script>