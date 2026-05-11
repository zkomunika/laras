import api from './api';

export const progressApi = {
    async list() {
        const response = await api.get('/progress');
        return response.data.data;
    },
};