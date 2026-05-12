<template>
    <div class="character-card" :class="`state-${state}`">
        <div class="character-aura"></div>
        <div class="character-body">
            <div class="character-head">
                <span class="character-face">{{ face }}</span>
            </div>
            <div class="character-torso">
                <span class="character-scroll">ᮜᮛᮞ</span>
            </div>
        </div>
        <div class="character-shadow"></div>
        <div class="character-caption">
            <strong>{{ title }}</strong>
            <span>{{ caption }}</span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    state: {
        type: String,
        default: 'idle',
        validator: (value) => ['idle', 'typing', 'combo', 'mistake', 'win', 'lose'].includes(value),
    },
});

const face = computed(() => ({
    idle: '🙂',
    typing: '✍️',
    combo: '🔥',
    mistake: '😮',
    win: '🏆',
    lose: '😵',
}[props.state] ?? '🙂'));

const title = computed(() => ({
    idle: 'Juru Aksara Siap',
    typing: 'Sedang Menulis',
    combo: 'Combo Bagus',
    mistake: 'Ada Salah Ketik',
    win: 'Level Selesai',
    lose: 'Coba Lagi',
}[props.state] ?? 'Juru Aksara'));

const caption = computed(() => ({
    idle: 'Mulai level untuk mengaktifkan karakter.',
    typing: 'Jaga ritme dan akurasi.',
    combo: 'Pertahankan ketikan bersih.',
    mistake: 'Perbaiki tempo, jangan panik.',
    win: 'Progress berhasil tersimpan.',
    lose: 'Ulangi level untuk membuka progress.',
}[props.state] ?? ''));
</script>
