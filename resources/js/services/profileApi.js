import api from './api';

export const profileApi = {
    async show() {
        const response = await api.get('/profile');
        return response.data.data;
    },
};