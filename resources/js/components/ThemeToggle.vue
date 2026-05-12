<template>
    <button
        type="button"
        class="theme-toggle"
        :aria-label="`Ganti ke ${theme.isLight ? 'dark theme' : 'light theme'}`"
        @click="theme.toggleTheme()"
    >
        <span>{{ theme.isLight ? '☀️' : '🌙' }}</span>
        <b>{{ theme.isLight ? 'Light' : 'Dark' }}</b>
    </button>
</template>

<script setup>
import { useThemeStore } from '@/stores/themeStore';

const theme = useThemeStore();
</script>
