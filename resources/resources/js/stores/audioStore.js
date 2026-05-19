import { defineStore } from 'pinia';
import { soundService } from '@/services/soundService.js';

const AUDIO_STORAGE_KEY = 'laras.audio';

function safeParse(value, fallback) {
    try {
        return value ? JSON.parse(value) : fallback;
    } catch (error) {
        return fallback;
    }
}

export const useAudioStore = defineStore('audio', {
    state: () => ({
        musicEnabled: true,
        sfxEnabled: true,
        musicVolume: 0.18,
        sfxVolume: 0.32,
        currentMusicMode: 'story',
        initialized: false,
    }),

    getters: {
        musicPercent: (state) => Math.round(state.musicVolume * 100),
        sfxPercent: (state) => Math.round(state.sfxVolume * 100),
    },

    actions: {
        init() {
            if (typeof window === 'undefined') {
                return;
            }

            const saved = safeParse(localStorage.getItem(AUDIO_STORAGE_KEY), null);

            if (saved) {
                this.musicEnabled = Boolean(saved.musicEnabled);
                this.sfxEnabled = saved.sfxEnabled !== false;
                this.musicVolume = Number.isFinite(Number(saved.musicVolume)) ? Number(saved.musicVolume) : 0.18;
                this.sfxVolume = Number.isFinite(Number(saved.sfxVolume)) ? Number(saved.sfxVolume) : 0.32;
            }

            this.initialized = true;
            this.syncSoundService();
        },

        persist() {
            if (typeof window === 'undefined') {
                return;
            }

            localStorage.setItem(AUDIO_STORAGE_KEY, JSON.stringify({
                musicEnabled: this.musicEnabled,
                sfxEnabled: this.sfxEnabled,
                musicVolume: this.musicVolume,
                sfxVolume: this.sfxVolume,
            }));
        },

        syncSoundService() {
            soundService.syncFromSettings({
                musicEnabled: this.musicEnabled,
                sfxEnabled: this.sfxEnabled,
                musicVolume: this.musicVolume,
                sfxVolume: this.sfxVolume,
            });
        },

        setMusicEnabled(value) {
            this.musicEnabled = Boolean(value);
            this.persist();
            this.syncSoundService();

            if (this.musicEnabled) {
                this.startMusic(this.currentMusicMode);
            } else {
                this.stopMusic({ reset: false });
            }
        },

        setSfxEnabled(value) {
            this.sfxEnabled = Boolean(value);
            this.persist();
            this.syncSoundService();
        },

        setMusicVolume(value) {
            this.musicVolume = Math.min(1, Math.max(0, Number(value)));
            this.persist();
            this.syncSoundService();
        },

        setSfxVolume(value) {
            this.sfxVolume = Math.min(1, Math.max(0, Number(value)));
            this.persist();
            this.syncSoundService();
        },

        startMusic(mode = 'story') {
            this.currentMusicMode = mode;
            this.syncSoundService();

            if (!this.musicEnabled) {
                return;
            }

            soundService.playBGM(mode);
        },

        stopMusic(options = {}) {
            soundService.stopBGM(options);
        },

        playSfx(type = 'correct', options = {}) {
            this.syncSoundService();
            soundService.play(type, options);
        },

        testSfx(type = 'levelUp') {
            if (!this.initialized) {
                this.init();
            }

            this.syncSoundService();
            soundService.preview(type, { volumeMultiplier: 1.15 });
        },
    },
});
