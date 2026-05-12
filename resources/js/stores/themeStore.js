import { defineStore } from 'pinia';

const THEME_STORAGE_KEY = 'laras.theme';
const MOTION_STORAGE_KEY = 'laras.motion';

export const useThemeStore = defineStore('theme', {
    state: () => ({
        theme: 'dark',
        reduceMotion: false,
        initialized: false,
    }),

    getters: {
        isLight: (state) => state.theme === 'light',
        themeLabel: (state) => state.theme === 'light' ? 'Light Theme' : 'Dark Theme',
    },

    actions: {
        init() {
            if (typeof window === 'undefined') {
                return;
            }

            const savedTheme = localStorage.getItem(THEME_STORAGE_KEY);
            const savedMotion = localStorage.getItem(MOTION_STORAGE_KEY);

            if (savedTheme === 'light' || savedTheme === 'dark') {
                this.theme = savedTheme;
            }

            this.reduceMotion = savedMotion === 'reduced';
            this.initialized = true;
            this.applyTheme();
        },

        setTheme(theme) {
            this.theme = theme === 'light' ? 'light' : 'dark';
            this.applyTheme();
        },

        toggleTheme() {
            this.setTheme(this.theme === 'dark' ? 'light' : 'dark');
        },

        setReduceMotion(value) {
            this.reduceMotion = Boolean(value);
            this.applyTheme();
        },

        applyTheme() {
            if (typeof document === 'undefined') {
                return;
            }

            document.body.dataset.theme = this.theme;
            document.body.classList.toggle('motion-reduced', this.reduceMotion);
            localStorage.setItem(THEME_STORAGE_KEY, this.theme);
            localStorage.setItem(MOTION_STORAGE_KEY, this.reduceMotion ? 'reduced' : 'full');
        },
    },
});
