import api from './api';

export const gameApi = {
    async start(levelId) {
        const response = await api.post('/game/start', {
            level_id: levelId,
        });

        return response.data.data;
    },

    async submit(attemptId, typedText) {
        const response = await api.post('/game/submit', {
            attempt_id: attemptId,
            typed_text: typedText,
        });

        return response.data.data;
    },
};