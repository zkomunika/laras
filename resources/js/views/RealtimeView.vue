<template>
    <section>
        <div class="topbar">
            <span class="topbar-title">💬 Ruang Aksara Live</span>

            <div class="topbar-right">
                <span class="tag-pill teal">
                    <span class="live-dot"></span>
                    Online
                </span>
                <span class="tag-pill">{{ onlineUsers.length }} user</span>
            </div>
        </div>

        <div class="pad">
            <div class="card">
                <div class="sec-head">Realtime Demo</div>

                <p class="page-description" style="margin-top:0;">
                    Halaman ini digunakan untuk demo UAS: dua akun dapat saling mengirim pesan
                    dan melihat status online tanpa reload halaman.
                </p>
            </div>

            <div class="realtime-layout">
                <aside class="online-panel">
                    <div class="sec-head">Online</div>

                    <div v-if="onlineUsers.length === 0" class="mini-empty">
                        Belum ada user online.
                    </div>

                    <div
                        v-for="user in onlineUsers"
                        :key="user.id"
                        class="online-user"
                    >
                        <span class="online-dot"></span>

                        <div>
                            <strong>{{ user.name }}</strong>
                            <small>{{ user.email }}</small>
                        </div>
                    </div>
                </aside>

                <div class="chat-panel">
                    <div ref="chatBox" class="chat-box">
                        <div v-if="messages.length === 0" class="mini-empty">
                            Belum ada pesan. Kirim pesan pertama.
                        </div>

                        <article
                            v-for="message in messages"
                            :key="message.id"
                            class="chat-message"
                            :class="{ own: message.user?.id === auth.user?.id }"
                        >
                            <div class="chat-bubble">
                                <strong>{{ message.user?.name }}</strong>
                                <p>{{ message.message }}</p>
                                <small>{{ message.created_at }}</small>
                            </div>
                        </article>
                    </div>

                    <form class="chat-form" @submit.prevent="sendMessage">
                        <input
                            v-model="form.message"
                            type="text"
                            maxlength="500"
                            placeholder="Tulis pesan realtime..."
                            required
                        >

                        <button class="btn btn-primary" type="submit" :disabled="sending">
                            {{ sending ? 'Mengirim...' : 'Kirim' }}
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
import { realtimeApi } from '@/services/realtimeApi';

const auth = useAuthStore();

const onlineUsers = ref([]);
const messages = ref([]);
const chatBox = ref(null);
const sending = ref(false);

const form = reactive({
    message: '',
});

let heartbeatTimer = null;
let pollTimer = null;

onMounted(async () => {
    await heartbeat();
    await refreshOnlineUsers();
    await fetchMessages();

    heartbeatTimer = setInterval(heartbeat, 5000);

    pollTimer = setInterval(async () => {
        await refreshOnlineUsers();
        await fetchMessages();
    }, 1500);
});

onBeforeUnmount(() => {
    clearInterval(heartbeatTimer);
    clearInterval(pollTimer);
});

async function heartbeat() {
    try {
        await realtimeApi.heartbeat();
    } catch (error) {
        console.error(error);
    }
}

async function refreshOnlineUsers() {
    try {
        onlineUsers.value = await realtimeApi.onlineUsers();
    } catch (error) {
        console.error(error);
    }
}

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
    if (!form.message.trim()) {
        return;
    }

    sending.value = true;

    try {
        const createdMessage = await realtimeApi.sendMessage(form.message.trim());
        messages.value.push(createdMessage);
        form.message = '';
        await scrollToBottom();
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