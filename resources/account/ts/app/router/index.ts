import { createRouter, createWebHistory } from "vue-router";
import type { RouteRecordRaw } from "vue-router";

const routes: RouteRecordRaw[] = [
    {
        path: "/account/login",
        name: "login",
        component: () => import("@account/ts/pages/Login.vue"),
        meta: {
            title: "Вход в систему",
        },
    },
    {
        path: "/account/dashboard",
        name: "dashboard",
        component: () => import("@account/ts/pages/Dashboard.vue"),
        meta: {
            title: "Главная",
            requiresAuth: true,
        },
    },
    {
        path: "/account",
        redirect: "/account/dashboard",
    },
    {
        path: "/:pathMatch(.*)*",
        name: "404",
        redirect: "/account/dashboard",
    },
];

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

// TODO: Add authentication guard
// router.beforeEach((to, from, next) => {
//     const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
//     const isAuthenticated = false; // Check from auth store
    
//     if (requiresAuth && !isAuthenticated) {
//         next('/account/login');
//     } else {
//         next();
//     }
// });

export default router;
