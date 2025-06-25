import { createRouter, createWebHistory } from "vue-router";
import type { RouteRecordRaw } from "vue-router";
import LoginPage from "../../pages/Login.vue";

const routes: RouteRecordRaw[] = [
    {
        path: "/account/login",
        name: "login",
        component: LoginPage,
        meta: {
            requiresAuth: false,
            title: "Вход в систему",
        },
    },
];

// Создание роутера
const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { top: 0 };
        }
    },
});

export default router;
