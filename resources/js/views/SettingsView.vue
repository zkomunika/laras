<template>
    <section>
        <div class="topbar">
            <span class="topbar-title">⚙️ Settings LARAS</span>
            <div class="topbar-right">
                <ThemeToggle />
                <span class="tag-pill">Polish Tahap 5</span>
            </div>
        </div>

        <div class="pad settings-page">
            <div class="settings-hero card">
                <div>
                    <div class="sec-head">Pengaturan Pengalaman Bermain</div>
                    <h2>Atur tema, audio, dan gerak animasi.</h2>
                    <p>
                        Semua pengaturan disimpan di browser. Tidak perlu migration atau perubahan database.
                    </p>
                </div>
                <CharacterAvatar state="combo" />
            </div>

            <div class="settings-grid">
                <div class="card settings-card">
                    <div class="sec-head">🎨 Tema Visual</div>

                    <div class="theme-choice-grid">
                        <button
                            type="button"
                            class="theme-choice dark-choice"
                            :class="{ active: theme.theme === 'dark' }"
                            @click="theme.setTheme('dark')"
                        >
                            <span>🌙</span>
                            <b>Dark Theme</b>
                            <small>Legenda, malam, emas, dan nuansa kerajaan.</small>
                        </button>

                        <button
                            type="button"
                            class="theme-choice light-choice"
                            :class="{ active: theme.theme === 'light' }"
                            @click="theme.setTheme('light')"
                        >
                            <span>☀️</span>
                            <b>Light Theme</b>
                            <small>Kertas naskah, ladang pagi, dan emas hangat.</small>
                        </button>
                    </div>

                    <div class="setting-row motion-row">
                        <div>
                            <b>Kurangi Animasi</b>
                            <span>Cocok untuk perangkat lambat atau pengguna yang ingin tampilan lebih tenang.</span>
                        </div>
                        <button
                            type="button"
                            class="mini-toggle"
                            :class="{ active: theme.reduceMotion }"
                            @click="theme.setReduceMotion(!theme.reduceMotion)"
                        >
                            {{ theme.reduceMotion ? 'ON' : 'OFF' }}
                        </button>
                    </div>
                </div>

                <AudioSettings />
            </div>
        </div>
    </section>
</template>

<script setup>
import ThemeToggle from '@/components/ThemeToggle.vue';
import AudioSettings from '@/components/AudioSettings.vue';
import CharacterAvatar from '@/components/CharacterAvatar.vue';
import { useThemeStore } from '@/stores/themeStore';

const theme = useThemeStore();
</script>
