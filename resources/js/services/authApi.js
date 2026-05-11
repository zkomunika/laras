import api from './api';

export const authApi = {
    async register(payload) {
        const response = await api.post('/auth/register', payload);
        return response.data.data;
    },

    async login(payload) {
        const response = await api.post('/auth/login', payload);
        return response.data.data;
    },

    async me() {
        const response = await api.get('/me');
        return response.data.data;
    },

    async logout() {
        await api.post('/auth/logout');
    },
};