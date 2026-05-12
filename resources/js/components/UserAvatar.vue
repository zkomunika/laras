<template>
    <div class="user-avatar" :style="{ backgroundColor: avatar.bg, color: avatar.color }" :title="avatar.name + ' - ' + userName">
        <component :is="avatar.icon" :size="size" />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { getUserAvatar } from '@/utils/avatar';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    size: {
        type: Number,
        default: 24,
    }
});

const avatar = computed(() => getUserAvatar(props.user?.email || props.user?.name || 'Guest'));
const userName = computed(() => props.user?.name || 'Guest');
</script>

<style scoped>
.user-avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    width: calc(v-bind('size') * 1.5px);
    height: calc(v-bind('size') * 1.5px);
    flex-shrink: 0;
}
</style>
