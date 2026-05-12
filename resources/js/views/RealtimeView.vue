<template>
    <section>
        <div class="topbar">
            <span class="topbar-title"><MessageCircle :size="18" style="vertical-align:text-bottom" /> Ruang Aksara Live</span>

            <div class="topbar-right">
                <span class="tag-pill teal">
                    <span class="live-dot"></span>
                    Online
                </span>
                <span class="tag-pill">{{ onlineUsers.length }} user</span>
            </div>
        </div>

        <div class="pad">
            <!-- ARENA BALAP -->
            <div class="card race-arena">
                <div v-if="loadingLevel" class="mini-empty">
                    Memuat arena...
                </div>
                
                <template v-else-if="level">
                    <div class="sec-head">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <Sword :size="18" />
                            Arena Balap Realtime — Level {{ level.level_number }}
                        </div>
                    </div>

                    <p class="page-description" style="margin-top:0; margin-bottom: 20px;">
                        Ketik teks di bawah ini secepat mungkin! Progress Anda akan terlihat langsung oleh semua orang.
                    </p>

                    <!-- STATS DASHBOARD (Matching StoryGameView) -->
                    <div class="game-dashboard" style="margin-bottom: 24px;">
                        <div class="dash-card" :class="{'active-dash': status === 'playing'}">
                            <Activity :size="20" class="dash-icon" />
                            <div class="dash-info">
                                <span class="dash-label">Status</span>
                                <span class="dash-value">{{ statusLabel }}</span>
                            </div>
                        </div>
                        
                        <div class="dash-card">
                            <Target :size="20" class="dash-icon" />
                            <div class="dash-info">
                                <span class="dash-label">Target WPM</span>
                                <span class="dash-value">{{ level.target_wpm }}</span>
                            </div>
                        </div>

                        <div class="dash-card" :class="{'warning-dash': remainingSeconds < 10 && status === 'playing'}">
                            <Timer :size="20" class="dash-icon" />
                            <div class="dash-info">
                                <span class="dash-label">Sisa Waktu</span>
                                <span class="dash-value">{{ remainingSeconds }}s</span>
                            </div>
                        </div>

                        <div class="dash-card">
                            <Zap :size="20" class="dash-icon" :color="wpm >= level.target_wpm ? '#10b981' : 'currentColor'" />
                            <div class="dash-info">
                                <span class="dash-label">WPM</span>
                                <span class="dash-value">{{ wpm }}</span>
                            </div>
                        </div>

                        <div class="dash-card">
                            <BarChart2 :size="20" class="dash-icon" :color="accuracy >= level.min_accuracy ? '#10b981' : '#ef4444'" />
                            <div class="dash-info">
                                <span class="dash-label">Akurasi</span>
                                <span class="dash-value">{{ accuracy }}%</span>
                            </div>
                        </div>

                        <div class="dash-card">
                            <AlertCircle :size="20" class="dash-icon" :color="mistakes > 0 ? '#ef4444' : 'currentColor'" />
                            <div class="dash-info">
                                <span class="dash-label">Keliru</span>
                                <span class="dash-value">{{ mistakes }}</span>
                            </div>
                        </div>

                        <div class="dash-card highlight-dash">
                            <Trophy :size="20" class="dash-icon" color="#fbbf24" />
                            <div class="dash-info">
                                <span class="dash-label">Skor</span>
                                <span class="dash-value" style="color: #fbbf24;">{{ score }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="target-text-box race-box">
                        <span
                            v-for="(character, index) in targetCharacters"
                            :key="index"
                            class="target-char"
                            :class="getCharacterClass(index)"
                        >{{ character }}</span>
                    </div>

                    <div class="typing-area">
                        <textarea
                            class="typing-input race-input"
                            :value="typedText"
                            :maxlength="targetText.length"
                            :disabled="inputDisabled"
                            placeholder="Mulai ketik untuk balapan realtime..."
                            @input="handleTypingInput"
                            @paste.prevent
                        ></textarea>
                        
                        <div v-if="status !== 'idle'" class="my-progress-container" style="margin-top: 12px;">
                            <div class="progress-label">Progress Anda: {{ Math.round((typedText.length / targetText.length) * 100) }}%</div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" :style="{ width: (typedText.length / targetText.length) * 100 + '%' }"></div>
                            </div>
                        </div>
                    </div>

                    <!-- RESULT PANEL (Matching StoryGameView) -->
                    <div
                        v-if="status === 'finished' || status === 'failed'"
                        class="result-panel"
                        style="margin-top: 24px; text-align: center; background: var(--bg3); padding: 30px; border-radius: 16px; border: 1px solid var(--border);"
                    >
                        <p class="eyebrow">Hasil Balapan</p>
                        <h2 v-if="status === 'finished'" style="font-size: 24px; margin-bottom: 20px;">Hebat! Balapan selesai.</h2>
                        <h2 v-else style="font-size: 24px; margin-bottom: 20px; color: var(--red);">Waktu habis!</h2>

                        <div class="stars-row" style="display:flex; justify-content:center; gap:12px; margin-bottom: 24px;">
                            <span
                                v-for="star in 3"
                                :key="star"
                                :class="{ active: star <= stars }"
                                :style="{ color: star <= stars ? '#fbbf24' : 'var(--muted)' }"
                            >
                                <Star :size="32" :fill="star <= stars ? 'currentColor' : 'none'" />
                            </span>
                        </div>

                        <div class="game-actions" style="justify-content: center;">
                            <button v-if="level.next_level_id" type="button" class="btn btn-primary" @click="loadLevel(level.next_level_id)">
                                Lanjut Level Berikutnya
                            </button>
                            <button type="button" class="btn btn-secondary" @click="resetGame">
                                Ulangi Arena
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="realtime-layout">
                <!-- SIDEBAR ONLINE -->
                <aside class="online-panel">
                    <div class="sec-head">Pejuang Online</div>

                    <div v-if="onlineUsers.length === 0" class="mini-empty">
                        <p>Mencari pejuang lain...</p>
                    </div>

                    <div
                        v-for="user in onlineUsers"
                        :key="user.id"
                        class="online-user-card"
                    >
                        <UserAvatar :user="user" :size="36" />
                        <div class="online-user-info">
                            <div class="user-meta">
                                <span class="user-name">{{ user.name }}</span>
                                <span v-if="user.wpm > 0" class="user-wpm">{{ user.wpm }} WPM</span>
                            </div>
                            <div class="user-progress-track">
                                <div class="user-progress-bar" :style="{ width: (user.progress || 0) + '%' }"></div>
                            </div>
                            <span class="user-status-text">
                                {{ user.progress === 100 ? 'Selesai!' : (user.progress > 0 ? 'Sedang mengetik...' : 'Menunggu...') }}
                            </span>
                        </div>
                    </div>
                </aside>

                <!-- CHAT PANEL -->
                <div class="chat-panel">
                    <div ref="chatBox" class="chat-box-area">
                        <div v-if="messages.length === 0" class="mini-empty">
                            Belum ada pesan. Kirim pesan pertama untuk memulai percakapan.
                        </div>

                        <div
                            v-for="message in messages"
                            :key="message.id"
                            class="message-wrapper"
                            :class="{ 'message-own': message.user?.id === auth.user?.id }"
                        >
                            <div class="message-meta">
                                <UserAvatar :user="message.user" :size="20" />
                                <span>{{ message.user?.name }}</span>
                            </div>
                            <div class="message-bubble">
                                <p>{{ message.message }}</p>
                                <time>{{ message.created_at }}</time>
                            </div>
                        </div>
                    </div>

                    <form class="chat-input-form" @submit.prevent="sendMessage">
                        <input
                            v-model="form.message"
                            type="text"
                            maxlength="500"
                            placeholder="Tulis pesan..."
                            required
                        >
                        <button class="btn btn-primary" type="submit" :disabled="sending">
                            <span v-if="!sending">Kirim</span>
                            <span v-else>...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { nextTick, onBeforeUnmount, onMounted, reactive, ref } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { useTypingGame } from '@/composables/useTypingGame';
import { levelApi } from '@/services/levelApi';
import { realtimeApi } from '@/services/realtimeApi';
import { soundService } from '@/services/soundService';
import { 
    MessageCircle, Sword, Activity, Target, Timer, 
    Zap, BarChart2, AlertCircle, Trophy, Star 
} from 'lucide-vue-next';
import UserAvatar from '@/components/UserAvatar.vue';

const auth = useAuthStore();

const {
    targetText,
    targetCharacters,
    typedText,
    status,
    statusLabel,
    remainingSeconds,
    mistakes,
    accuracy,
    wpm,
    score,
    stars,
    inputDisabled,
    setupGame,
    resetGame,
    updateTypedText,
    getCharacterClass,
} = useTypingGame();

const onlineUsers = ref([]);
const messages = ref([]);
const chatBox = ref(null);
const sending = ref(false);
const loadingLevel = ref(true);
const level = ref(null);

const form = reactive({
    message: '',
});

let channel = null;
let updateTimeout = null;

async function loadLevel(id = 1) {
    soundService.play('click');
    loadingLevel.value = true;
    try {
        level.value = await levelApi.detail(id);
        setupGame(level.value);
    } catch (err) {
        console.error("Gagal memuat level arena.");
    } finally {
        loadingLevel.value = false;
    }
}

function handleTypingInput(event) {
    if (status.value === 'idle') {
        soundService.play('start');
    }

    updateTypedText(event.target.value);
    
    // Sync to local online list
    const me = onlineUsers.value.find(u => u.id === auth.user?.id);
    const progress = Math.min(100, Math.round((typedText.value.length / targetText.value.length) * 100));
    
    if (me) {
        me.progress = progress;
        me.wpm = wpm.value;
    }

    // Broadcast to others
    clearTimeout(updateTimeout);
    updateTimeout = setTimeout(() => {
        realtimeApi.updateProgress(progress, wpm.value).catch(() => {});
    }, 200);

    if (progress === 100) {
        soundService.play('finish');
    }
}

onMounted(async () => {
    await loadLevel();
    await fetchMessages();

    if (!window.Echo) return;

    channel = window.Echo.join('chat')
        .here((users) => {
            onlineUsers.value = users;
        })
        .joining((user) => {
            if (!onlineUsers.value.find(u => u.id === user.id)) {
                onlineUsers.value.push(user);
            }
        })
        .leaving((user) => {
            onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id);
        })
        .listen('MessageSent', (e) => {
            messages.value.push(e.message);
            scrollToBottom();
        })
        .listen('PlayerProgress', (e) => {
            const u = onlineUsers.value.find(user => user.id === e.userId);
            if (u) {
                u.progress = e.progress;
                u.wpm = e.wpm;
            }
        });
});

onBeforeUnmount(() => {
    if (channel) {
        window.Echo.leave('chat');
    }
});

async function fetchMessages() {
    try {
        const lastId = messages.value.length
            ? messages.value[messages.value.length - 1].id
            : 0;

        const newMessages = await realtimeApi.messages(lastId);

        if (newMessages.length > 0) {
            messages.value.push(...newMessages);
            await scrollToBottom();
        }
    } catch (error) {
        console.error(error);
    }
}

async function sendMessage() {
    if (!form.message.trim() || sending.value) return;

    sending.value = true;
    try {
        await realtimeApi.sendMessage(form.message.trim());
        soundService.play('correct');
        form.message = '';
    } catch (error) {
        console.error(error);
    } finally {
        sending.value = false;
    }
}

async function scrollToBottom() {
    await nextTick();
    if (chatBox.value) {
        chatBox.value.scrollTop = chatBox.value.scrollHeight;
    }
}
</script>

<style scoped>
.realtime-layout {
    display: flex;
    gap: 20px;
    height: 600px;
    margin-top: 20px;
}

/* SIDEBAR ONLINE */
.online-panel {
    width: 300px;
    background: var(--bg2);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    overflow-y: auto;
}

.online-user-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: var(--bg3);
    border: 1px solid var(--border);
    border-radius: 12px;
    transition: transform 0.2s;
}

.online-user-info {
    flex: 1;
    min-width: 0;
}

.user-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}

.user-name {
    font-weight: 600;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-wpm {
    font-size: 11px;
    color: var(--teal);
    font-weight: bold;
    font-family: var(--font-mono);
}

.user-progress-track {
    height: 4px;
    background: var(--bg);
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 4px;
}

.user-progress-bar {
    height: 100%;
    background: var(--teal);
    transition: width 0.3s ease;
}

.user-status-text {
    font-size: 10px;
    color: var(--muted);
}

/* CHAT PANEL */
.chat-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.chat-box-area {
    flex: 1;
    background: var(--bg2);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.message-wrapper {
    display: flex;
    flex-direction: column;
    gap: 4px;
    max-width: 75%;
}

.message-own {
    align-self: flex-end;
    align-items: flex-end;
}

.message-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--muted);
}

.message-own .message-meta {
    flex-direction: row-reverse;
}

.message-bubble {
    background: var(--bg3);
    border: 1px solid var(--border);
    padding: 10px 14px;
    border-radius: 14px;
    border-top-left-radius: 2px;
    position: relative;
    word-break: break-word;
    width: fit-content;
    min-width: 50px;
}

.message-own .message-bubble {
    background: var(--teal);
    border-color: var(--teal);
    color: #000;
    border-top-left-radius: 14px;
    border-top-right-radius: 2px;
}

.message-bubble p {
    margin: 0;
    line-height: 1.5;
}

.message-bubble time {
    display: block;
    font-size: 9px;
    margin-top: 4px;
    opacity: 0.6;
    text-align: right;
}

.chat-input-form {
    display: flex;
    gap: 10px;
}

.chat-input-form input {
    flex: 1;
    background: var(--bg2);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 12px 16px;
    color: var(--text);
    outline: none;
}

.chat-input-form input:focus {
    border-color: var(--teal);
}

/* RACE ARENA - STYLING MATCHING STORYGAMEVIEW */
.game-dashboard {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.dash-card {
    flex: 1 1 calc(25% - 12px);
    min-width: 130px;
    background: var(--bg3);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.dash-info { display: flex; flex-direction: column; }
.dash-label { font-size: 10px; text-transform: uppercase; color: var(--muted); }
.dash-value { font-size: 16px; font-weight: 700; color: var(--text); }
.active-dash { border-color: var(--teal); background: rgba(45, 212, 191, 0.05); }
.warning-dash { border-color: #ef4444; animation: blink 1s infinite; }
.highlight-dash { background: rgba(251, 191, 36, 0.05); border-color: rgba(251, 191, 36, 0.3); }

.race-box {
    background: var(--bg3);
    border-left: 4px solid var(--gold);
    padding: 24px;
    font-size: 20px;
    line-height: 1.8;
}

.typing-input {
    width: 100%;
    min-height: 100px;
    background: var(--bg2);
    border: 2px solid var(--border);
    border-radius: 12px;
    padding: 16px;
    color: var(--text);
    font-family: var(--font-mono);
    font-size: 18px;
    outline: none;
}

.typing-input:focus { border-color: var(--gold); }

.target-char { border-radius: 4px; padding: 0 1px; }
.char-pending { color: var(--muted); }
.char-current { color: var(--gold2); background: rgba(200, 168, 75, 0.2); outline: 1px solid var(--gold); }
.char-correct { color: var(--green); background: rgba(74, 222, 128, 0.1); }
.char-wrong { color: var(--red); background: rgba(248, 113, 113, 0.1); }

.my-progress-container {
    padding: 12px;
    background: var(--bg3);
    border-radius: 10px;
}

.progress-bar-bg { height: 6px; background: var(--bg); border-radius: 3px; overflow: hidden; }
.progress-bar-fill { height: 100%; background: var(--teal); transition: width 0.3s ease; }

.mini-empty { text-align: center; color: var(--muted); padding: 40px 0; font-style: italic; }

@keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
</style>