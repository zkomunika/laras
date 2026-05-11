import { createRouter, createWebHistory } from 'vue-router';

import HomeView from '@/views/HomeView.vue';
import ChaptersView from '@/views/ChaptersView.vue';
import ChapterDetailView from '@/views/ChapterDetailView.vue';
import StoryGameView from '@/views/StoryGameView.vue';
import LeaderboardView from '@/views/LeaderboardView.vue';
import ProfileView from '@/views/ProfileView.vue';
import NotFoundView from '@/views/NotFoundView.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: HomeView,
    },
    {
        path: '/chapters',
        name: 'chapters',
        component: ChaptersView,
    },
    {
        path: '/chapters/:id',
        name: 'chapter-detail',
        component: ChapterDetailView,
        props: true,
    },
    {
        path: '/story/levels/:id',
        name: 'story-game',
        component: StoryGameView,
        props: true,
    },
    {
        path: '/leaderboard',
        name: 'leaderboard',
        component: LeaderboardView,
    },
    {
        path: '/profile',
        name: 'profile',
        component: ProfileView,
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: NotFoundView,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;