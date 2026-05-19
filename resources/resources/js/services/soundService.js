const SOUND_SOURCES = {
    bgm: 'https://raw.githubusercontent.com/mdn/webaudio-examples/main/audio-basics/outfoxing.mp3',
    click: 'https://raw.githubusercontent.com/IonDen/ion.sound/master/sounds/button_tiny.mp3',
    start: 'https://raw.githubusercontent.com/IonDen/ion.sound/master/sounds/bell_ring.mp3',
    correct: 'https://raw.githubusercontent.com/IonDen/ion.sound/master/sounds/tap.mp3',
    wrong: 'https://raw.githubusercontent.com/IonDen/ion.sound/master/sounds/cd_tray.mp3',
    finish: 'https://raw.githubusercontent.com/IonDen/ion.sound/master/sounds/glass.mp3',
    levelUp: 'https://raw.githubusercontent.com/IonDen/ion.sound/master/sounds/magic_chime.mp3',
};

const BGM_PRESETS = {
    story: {
        source: SOUND_SOURCES.bgm,
        volumeMultiplier: 1,
        playbackRate: 0.92,
    },
    challenge: {
        source: SOUND_SOURCES.bgm,
        volumeMultiplier: 1.25,
        playbackRate: 1.08,
    },
};

const SFX_ALIASES = {
    combo: 'levelUp',
    win: 'finish',
    lose: 'wrong',
    success: 'finish',
    error: 'wrong',
};

const SFX_PRESETS = {
    click: { volumeMultiplier: 0.85 },
    start: { volumeMultiplier: 1 },
    correct: { volumeMultiplier: 0.7 },
    wrong: { volumeMultiplier: 1 },
    finish: { volumeMultiplier: 1.1 },
    levelUp: { volumeMultiplier: 1.15 },
};

class SoundService {
    constructor() {
        this.musicTracks = Object.entries(BGM_PRESETS).reduce((tracks, [key, preset]) => {
            tracks[key] = this.createAudio(preset.source, {
                loop: true,
                preload: 'auto',
            });
            return tracks;
        }, {});

        this.sounds = {
            click: this.createAudio(SOUND_SOURCES.click),
            start: this.createAudio(SOUND_SOURCES.start),
            correct: this.createAudio(SOUND_SOURCES.correct),
            wrong: this.createAudio(SOUND_SOURCES.wrong),
            finish: this.createAudio(SOUND_SOURCES.finish),
            levelUp: this.createAudio(SOUND_SOURCES.levelUp),
        };

        const legacyMuted = this.readLegacyMutedValue();

        this.musicEnabled = !legacyMuted;
        this.sfxEnabled = !legacyMuted;
        this.musicVolume = 0.18;
        this.sfxVolume = 0.32;
        this.currentBgmKey = null;
        this.musicPlayToken = 0;
    }

    createAudio(source, options = {}) {
        const audio = new Audio(source);
        audio.loop = Boolean(options.loop);
        audio.preload = options.preload || 'auto';
        return audio;
    }

    readLegacyMutedValue() {
        if (typeof localStorage === 'undefined') {
            return false;
        }

        try {
            return JSON.parse(localStorage.getItem('laras_muted') || 'false');
        } catch (error) {
            return false;
        }
    }

    syncFromSettings(settings = {}) {
        if (settings.musicEnabled !== undefined) {
            this.musicEnabled = Boolean(settings.musicEnabled);
        }

        if (settings.sfxEnabled !== undefined) {
            this.sfxEnabled = Boolean(settings.sfxEnabled);
        }

        if (settings.musicVolume !== undefined) {
            this.setMusicVolume(settings.musicVolume);
        }

        if (settings.sfxVolume !== undefined) {
            this.setSfxVolume(settings.sfxVolume);
        }

        this.applyMuteState();
    }

    setMusicVolume(value) {
        this.musicVolume = this.normalizeVolume(value, 0.18);
        this.updateCurrentMusicVolume();
    }

    setSfxVolume(value) {
        this.sfxVolume = this.normalizeVolume(value, 0.32);
    }

    setMusicEnabled(value) {
        this.musicEnabled = Boolean(value);
        this.applyMuteState();

        if (!this.musicEnabled) {
            this.stopBGM({ reset: false });
        }
    }

    setSfxEnabled(value) {
        this.sfxEnabled = Boolean(value);
        this.applyMuteState();
    }

    normalizeVolume(value, fallback) {
        const number = Number(value);

        if (!Number.isFinite(number)) {
            return fallback;
        }

        return Math.min(1, Math.max(0, number));
    }

    clampVolume(value) {
        return Math.min(1, Math.max(0, Number(value) || 0));
    }

    updateCurrentMusicVolume() {
        if (!this.currentBgmKey) {
            return;
        }

        const bgm = this.musicTracks[this.currentBgmKey];
        const preset = BGM_PRESETS[this.currentBgmKey] ?? BGM_PRESETS.story;

        if (bgm) {
            bgm.volume = this.clampVolume(this.musicVolume * preset.volumeMultiplier);
        }
    }

    async playBGM(mode = 'story') {
        return this.playMusic(mode);
    }

    async playMusic(mode = 'story') {
        if (!this.musicEnabled) {
            return false;
        }

        const bgmKey = BGM_PRESETS[mode] ? mode : 'story';
        const bgm = this.musicTracks[bgmKey];
        const preset = BGM_PRESETS[bgmKey];

        if (!bgm) {
            return false;
        }

        if (this.currentBgmKey && this.currentBgmKey !== bgmKey) {
            this.stopBGM({ reset: false });
        }

        const playToken = ++this.musicPlayToken;
        this.currentBgmKey = bgmKey;
        bgm.muted = false;
        bgm.volume = this.clampVolume(this.musicVolume * preset.volumeMultiplier);
        bgm.playbackRate = preset.playbackRate;

        try {
            await bgm.play();

            const isStalePlayRequest = playToken !== this.musicPlayToken || this.currentBgmKey !== bgmKey || !this.musicEnabled;
            if (isStalePlayRequest) {
                bgm.pause();
                return false;
            }

            return true;
        } catch (error) {
            if (playToken === this.musicPlayToken) {
                console.warn('BGM play blocked or failed', error);
            }
            return false;
        }
    }

    stopBGM(options = {}) {
        const { reset = true } = options;
        this.musicPlayToken += 1;

        Object.values(this.musicTracks).forEach((bgm) => {
            bgm.pause();

            if (reset) {
                bgm.currentTime = 0;
            }
        });

        this.currentBgmKey = null;
    }

    play(soundName, options = {}) {
        if (!this.sfxEnabled) {
            return;
        }

        const resolvedSoundName = SFX_ALIASES[soundName] ?? soundName;
        const baseSound = this.sounds[resolvedSoundName];

        if (!baseSound) {
            console.warn(`SFX "${soundName}" tidak ditemukan di soundService.js`);
            return;
        }

        const preset = SFX_PRESETS[resolvedSoundName] ?? { volumeMultiplier: 1 };
        const sound = baseSound.cloneNode(true);
        const volumeMultiplier = options.volumeMultiplier ?? preset.volumeMultiplier;

        sound.muted = false;
        sound.volume = this.clampVolume(this.sfxVolume * volumeMultiplier);
        sound.playbackRate = options.playbackRate ?? 1;
        sound.play().catch((error) => console.log('SFX play blocked', error));
    }


    preview(soundName = 'levelUp', options = {}) {
        const previousSfxEnabled = this.sfxEnabled;
        this.sfxEnabled = true;
        this.applyMuteState();
        this.play(soundName, options);
        this.sfxEnabled = previousSfxEnabled;
        this.applyMuteState();
    }

    toggleMute() {
        const muted = this.musicEnabled || this.sfxEnabled;

        this.musicEnabled = !muted;
        this.sfxEnabled = !muted;
        if (typeof localStorage !== 'undefined') {
            localStorage.setItem('laras_muted', JSON.stringify(muted));
        }
        this.applyMuteState();

        if (muted) {
            this.stopBGM({ reset: false });
        }

        return muted;
    }

    applyMute() {
        this.applyMuteState();
    }

    applyMuteState() {
        Object.values(this.musicTracks).forEach((bgm) => {
            bgm.muted = !this.musicEnabled;
        });

        Object.values(this.sounds).forEach((sound) => {
            sound.muted = !this.sfxEnabled;
        });
    }
}

export const soundService = new SoundService();
