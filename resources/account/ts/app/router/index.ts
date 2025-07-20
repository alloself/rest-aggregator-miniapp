import { createCRUDRoutes } from "@/shared/helpers/createCRUDRoutes";
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
            ...createCRUDRoutes("category", {
                prefix: "account",
            }),
            ...createCRUDRoutes("restaurant", {
                prefix: "account",
            }),
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

export default router;
