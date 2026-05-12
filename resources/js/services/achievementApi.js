import api from './api';

export const achievementApi = {
    async list() {
        const response = await api.get('/achievements');
        return response.data.data;
    },
};
