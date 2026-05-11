import api from './api';

export const leaderboardApi = {
    async list() {
        const response = await api.get('/leaderboard');
        return response.data.data;
    },
};