import api from './api';

export const notificationApi = {
    async list() {
        const response = await api.get('/notifications');
        return response.data;
    },

    async markAsRead(id) {
        const response = await api.post(`/notifications/${id}/read`);
        return response.data.data;
    },

    async markAllAsRead() {
        const response = await api.post('/notifications/read-all');
        return response.data;
    },
};
