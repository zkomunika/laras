import { defineStore } from 'pinia';
import { soundService } from '@/services/soundService.js';

soundService.playBGM();

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
        musicEnabled: false,
        sfxEnabled: true,
        musicVolume: 0.18,
        sfxVolume: 0.32,
        context: null,
        musicNodes: null,
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

        ensureContext() {
            if (typeof window === 'undefined') {
                return null;
            }

            const AudioContext = window.AudioContext || window.webkitAudioContext;

            if (!AudioContext) {
                return null;
            }

            if (!this.context) {
                this.context = new AudioContext();
            }

            if (this.context.state === 'suspended') {
                this.context.resume().catch(() => {});
            }

            return this.context;
        },

        setMusicEnabled(value) {
            this.musicEnabled = Boolean(value);
            this.persist();

            if (this.musicEnabled) {
                this.startMusic();
            } else {
                this.stopMusic();
            }
        },

        setSfxEnabled(value) {
            this.sfxEnabled = Boolean(value);
            this.persist();
        },

        setMusicVolume(value) {
            this.musicVolume = Math.min(1, Math.max(0, Number(value)));
            this.persist();

            if (this.musicNodes?.gain) {
                this.musicNodes.gain.gain.setTargetAtTime(this.musicVolume * 0.08, this.context.currentTime, 0.05);
            }
        },

        setSfxVolume(value) {
            this.sfxVolume = Math.min(1, Math.max(0, Number(value)));
            this.persist();
        },

        startMusic() {
            if (!this.musicEnabled || this.musicNodes) {
                return;
            }

            const ctx = this.ensureContext();

            if (!ctx) {
                return;
            }

            const gain = ctx.createGain();
            const lowDrone = ctx.createOscillator();
            const highTone = ctx.createOscillator();

            lowDrone.type = 'sine';
            highTone.type = 'triangle';
            lowDrone.frequency.value = 146.83;
            highTone.frequency.value = 220;
            gain.gain.value = this.musicVolume * 0.07;

            lowDrone.connect(gain);
            highTone.connect(gain);
            gain.connect(ctx.destination);

            lowDrone.start();
            highTone.start();

            this.musicNodes = { gain, lowDrone, highTone };
        },

        stopMusic() {
            if (!this.musicNodes) {
                return;
            }

            const { gain, lowDrone, highTone } = this.musicNodes;
            const ctx = this.context;

            try {
                gain.gain.setTargetAtTime(0, ctx.currentTime, 0.03);
                lowDrone.stop(ctx.currentTime + 0.08);
                highTone.stop(ctx.currentTime + 0.08);
            } catch (error) {
                // Browser may throw if an oscillator already stopped. Safe to ignore.
            }

            this.musicNodes = null;
        },

        playSfx(type = 'correct') {
            if (!this.sfxEnabled) {
                return;
            }

            const ctx = this.ensureContext();

            if (!ctx) {
                return;
            }

            const presets = {
                correct: { frequency: 640, duration: 0.045, wave: 'sine' },
                wrong: { frequency: 140, duration: 0.09, wave: 'sawtooth' },
                combo: { frequency: 880, duration: 0.08, wave: 'triangle' },
                win: { frequency: 1046, duration: 0.16, wave: 'triangle' },
                lose: { frequency: 110, duration: 0.18, wave: 'sine' },
                click: { frequency: 420, duration: 0.04, wave: 'square' },
            };

            const preset = presets[type] ?? presets.correct;
            const oscillator = ctx.createOscillator();
            const gain = ctx.createGain();
            const now = ctx.currentTime;

            oscillator.type = preset.wave;
            oscillator.frequency.value = preset.frequency;
            gain.gain.value = this.sfxVolume * 0.16;
            gain.gain.exponentialRampToValueAtTime(0.0001, now + preset.duration);

            oscillator.connect(gain);
            gain.connect(ctx.destination);
            oscillator.start(now);
            oscillator.stop(now + preset.duration);
        },
    },
});
