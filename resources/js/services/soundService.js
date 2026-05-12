class SoundService {
    constructor() {
        // Reliable MDN Demo Audio (Guaranteed no CORS/429 issues)
        this.bgm = new Audio('https://raw.githubusercontent.com/mdn/webaudio-examples/main/audio-basics/outfoxing.mp3');
        this.bgm.loop = true;
        this.bgm.volume = 0.2;
        this.bgm.preload = 'auto';

        this.sounds = {
            click: new Audio('https://raw.githubusercontent.com/IonDen/ion.sound/master/sounds/button_tiny.mp3'),
            start: new Audio('https://raw.githubusercontent.com/IonDen/ion.sound/master/sounds/bell_ring.mp3'),
            correct: new Audio('https://raw.githubusercontent.com/IonDen/ion.sound/master/sounds/tap.mp3'),
            wrong: new Audio('https://raw.githubusercontent.com/IonDen/ion.sound/master/sounds/cd_tray.mp3'),
            finish: new Audio('https://raw.githubusercontent.com/IonDen/ion.sound/master/sounds/glass.mp3'),
            levelUp: new Audio('https://raw.githubusercontent.com/IonDen/ion.sound/master/sounds/magic_chime.mp3')
        };

        this.isMuted = JSON.parse(localStorage.getItem('laras_muted') || 'false');
    }

    async playBGM() {
        if (!this.isMuted) {
            try {
                this.bgm.muted = false;
                await this.bgm.play();
                return true;
            } catch (e) {
                console.warn('BGM play blocked or failed', e);
                return false;
            }
        }
        return false;
    }

    stopBGM() {
        this.bgm.pause();
        this.bgm.currentTime = 0;
    }

    play(soundName) {
        if (!this.isMuted && this.sounds[soundName]) {
            const sound = this.sounds[soundName].cloneNode();
            sound.volume = 0.5;
            sound.play().catch(e => console.log('SFX play blocked', e));
        }
    }

    toggleMute() {
        this.isMuted = !this.isMuted;
        localStorage.setItem('laras_muted', JSON.stringify(this.isMuted));
        this.applyMute();
        return this.isMuted;
    }

    applyMute() {
        this.bgm.muted = this.isMuted;
        Object.values(this.sounds).forEach(s => {
            s.muted = this.isMuted;
        });
        if (this.isMuted) {
            this.bgm.pause();
        } else if (this.bgm.paused) {
            this.playBGM();
        }
    }
}

export const soundService = new SoundService();
