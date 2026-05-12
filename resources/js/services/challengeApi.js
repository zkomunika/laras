import api from '@/services/api';

function unwrap(response) {
    return response.data?.data ?? response.data;
}

export const challengeApi = {
    async rooms() {
        const response = await api.get('/challenge/rooms');
        return unwrap(response);
    },

    async createRoom(payload) {
        const response = await api.post('/challenge/rooms', payload);
        return unwrap(response);
    },

    async room(id) {
        const response = await api.get(`/challenge/rooms/${id}`);
        return unwrap(response);
    },

    async join(id) {
        const response = await api.post(`/challenge/rooms/${id}/join`);
        return unwrap(response);
    },

    async joinCode(id, code) {
        const response = await api.post(`/challenge/rooms/${id}/join-code`, { code });
        return unwrap(response);
    },

    async leave(id) {
        const response = await api.post(`/challenge/rooms/${id}/leave`);
        return unwrap(response);
    },

    async start(id) {
        const response = await api.post(`/challenge/rooms/${id}/start`);
        return unwrap(response);
    },

    async progress(id, payload) {
        const response = await api.post(`/challenge/rooms/${id}/progress`, payload);
        return unwrap(response);
    },

    async submit(id, payload) {
        const response = await api.post(`/challenge/rooms/${id}/submit`, payload);
        return unwrap(response);
    },

    async results(id) {
        const response = await api.get(`/challenge/rooms/${id}/results`);
        return unwrap(response);
    },
};
