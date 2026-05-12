import { defineStore } from 'pinia';
import { authApi } from '@/services/authApi';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('laras_user') || 'null'),
        token: localStorage.getItem('laras_token'),
        loading: false,
    }),

    getters: {
        isAuthenticated: (state) => Boolean(state.token && state.user),
    },

    actions: {
        setSession(data) {
            this.user = data.user;
            this.token = data.token;

            localStorage.setItem('laras_user', JSON.stringify(data.user));
            localStorage.setItem('laras_token', data.token);
        },

        clearSession() {
            this.user = null;
            this.token = null;

            localStorage.removeItem('laras_user');
            localStorage.removeItem('laras_token');
        },

        async register(payload) {
            const data = await authApi.register(payload);
            this.setSession(data);
        },

        async login(payload) {
            const data = await authApi.login(payload);
            this.setSession(data);
        },

        async checkSession() {
            if (!this.token) {
                this.clearSession();
                return false;
            }

            try {
                const user = await authApi.me();
                this.user = user;
                localStorage.setItem('laras_user', JSON.stringify(user));
                return true;
            } catch (error) {
                this.clearSession();
                return false;
            }
        },

        async logout() {
            try {
                if (this.token) {
                    await authApi.logout();
                }
            } finally {
                this.clearSession();
            }
        },
    },
});