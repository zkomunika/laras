import { createRouter, createWebHistory } from "vue-router";

import HomeView from "@/views/HomeView.vue";
import ChaptersView from "@/views/ChaptersView.vue";
import ChapterDetailView from "@/views/ChapterDetailView.vue";
import StoryGameView from "@/views/StoryGameView.vue";
import LeaderboardView from "@/views/LeaderboardView.vue";
import ProfileView from "@/views/ProfileView.vue";
import NotFoundView from "@/views/NotFoundView.vue";
import LoginView from "@/views/LoginView.vue";
import RegisterView from "@/views/RegisterView.vue";
import { useAuthStore } from "@/stores/authStore";
import RealtimeView from "@/views/RealtimeView.vue";

const routes = [
    {
        path: "/",
        name: "home",
        component: HomeView,
    },
    {
        path: "/chapters",
        name: "chapters",
        meta: { requiresAuth: true },
        component: ChaptersView,
    },
    {
        path: "/chapters/:id",
        name: "chapter-detail",
        meta: { requiresAuth: true },
        component: ChapterDetailView,
        props: true,
    },
    {
        path: "/story/levels/:id",
        name: "story-game",
        meta: { requiresAuth: true },
        component: StoryGameView,
        props: true,
    },
    {
        path: "/leaderboard",
        name: "leaderboard",
        meta: { requiresAuth: true },
        component: LeaderboardView,
    },
    {
        path: "/profile",
        name: "profile",
        meta: { requiresAuth: true },
        component: ProfileView,
    },
    {
        path: "/:pathMatch(.*)*",
        name: "not-found",
        component: NotFoundView,
    },
    {
        path: "/login",
        name: "login",
        component: LoginView,
    },
    {
        path: "/register",
        name: "register",
        component: RegisterView,
    },
    {
        path: "/realtime",
        name: "realtime",
        component: RealtimeView,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    const auth = useAuthStore();

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return "/login";
    }

    if (
        (to.name === "login" || to.name === "register") &&
        auth.isAuthenticated
    ) {
        return "/chapters";
    }
});

export default router;
