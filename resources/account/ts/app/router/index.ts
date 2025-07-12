import { createRouter, createWebHistory } from "vue-router";
import type { RouteRecordRaw } from "vue-router";

const routes: RouteRecordRaw[] = [
    {
        path: "/login",
        name: "login",
        component: () => import("@account/ts/pages/Login.vue"),
        meta: {
            title: "Вход в систему",
        },
    },
    {
        path: "/",
        name: "App",
        component: () => import("@account/ts/widgets/Layout/ui/Layout.vue"),
        meta: {
            title: "Главная",
        },
        children: [
            {
                path: "/dashboard",
                alias: "/",
                name: "dashboard",
                component: () => import("@account/ts/pages/Dashboard.vue"),
            },
            {
                path: `/category`,
                name: `category`,
                meta: {
                    title: "Категории",
                },
                component: () => import("@account/ts/pages/Category/List.vue"),
            },
            {
                path: `/category/create`,
                name: `categoryCreate`,
                meta: {
                    title: "Создание категории",
                },
                component: () =>
                    import("@account/ts/pages/Category/Detail.vue"),
            },
            {
                path: `/category/:id`,
                name: `categoryDetail`,
                meta: {
                    title: "Категория",
                },
                component: () =>
                    import("@account/ts/pages/Category/Detail.vue"),
            },
        ],
    },
    {
        path: "/:pathMatch(.*)*",
        name: "404",
        redirect: "/dashboard",
    },
];

const router = createRouter({
    history: createWebHistory("/account"),
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
