import api from './api';

export const levelApi = {
    async list() {
        const response = await api.get('/levels');
        return response.data.data;
    },

    async detail(id) {
        const response = await api.get(`/levels/${id}`);
        return response.data.data;
    },
};