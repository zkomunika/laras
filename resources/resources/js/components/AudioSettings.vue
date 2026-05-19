<template>
    <div class="audio-settings-card">
        <div class="audio-settings-head">
            <div>
                <strong>Audio Interaktif</strong>
                <span>Musik dan efek suara untuk feedback gameplay.</span>
            </div>
        </div>

        <div class="setting-row">
            <div>
                <b>Backsound</b>
                <span>Nuansa rendah untuk mode cerita.</span>
            </div>
            <button
                type="button"
                class="mini-toggle"
                :class="{ active: audio.musicEnabled }"
                @click="toggleMusic"
            >
                {{ audio.musicEnabled ? 'ON' : 'OFF' }}
            </button>
        </div>

        <label class="range-row">
            <span>Volume Musik {{ audio.musicPercent }}%</span>
            <input
                type="range"
                min="0"
                max="1"
                step="0.01"
                :value="audio.musicVolume"
                @input="audio.setMusicVolume($event.target.value)"
            >
        </label>

        <div class="setting-row">
            <div>
                <b>Sound Effect</b>
                <span>Feedback saat benar, salah, menang, dan gagal.</span>
            </div>
            <button
                type="button"
                class="mini-toggle"
                :class="{ active: audio.sfxEnabled }"
                @click="toggleSfx"
            >
                {{ audio.sfxEnabled ? 'ON' : 'OFF' }}
            </button>
        </div>

        <label class="range-row">
            <span>Volume SFX {{ audio.sfxPercent }}%</span>
            <input
                type="range"
                min="0"
                max="1"
                step="0.01"
                :value="audio.sfxVolume"
                @input="audio.setSfxVolume($event.target.value)"
            >
        </label>

        <button type="button" class="btn btn-secondary audio-test-btn" @click="testSfx">
            Tes SFX
        </button>
    </div>
</template>

<script setup>
import { useAudioStore } from '@/stores/audioStore';

const audio = useAudioStore();

function toggleMusic() {
    audio.setMusicEnabled(!audio.musicEnabled);
}

function toggleSfx() {
    audio.setSfxEnabled(!audio.sfxEnabled);
    if (!audio.sfxEnabled) {
        return;
    }
    audio.playSfx('click');
}

function testSfx() {
    audio.testSfx('levelUp');
}
</script>
