import api from './api';

export const realtimeApi = {
    async heartbeat() {
        await api.post('/realtime/heartbeat');
    },

    async onlineUsers() {
        const response = await api.get('/realtime/online-users');
        return response.data.data;
    },

    async messages(afterId = 0) {
        const response = await api.get('/realtime/messages', {
            params: {
                after_id: afterId,
            },
        });

        return response.data.data;
    },

    async sendMessage(message) {
        const response = await api.post('/realtime/messages', {
            message,
        });

        return response.data.data;
    },

    async updateProgress(progress, wpm) {
        await api.post('/realtime/progress', {
            progress,
            wpm
        });
    }
};