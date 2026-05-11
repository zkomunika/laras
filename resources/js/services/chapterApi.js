import api from './api';

export const chapterApi = {
    async list() {
        const response = await api.get('/chapters');
        return response.data.data;
    },

    async detail(id) {
        const response = await api.get(`/chapters/${id}`);
        return response.data.data;
    },

    async levels(id) {
        const response = await api.get(`/chapters/${id}/levels`);
        return response.data.data;
    },
};