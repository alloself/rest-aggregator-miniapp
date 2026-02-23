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
        path: "/legal/offer",
        name: "legal-offer",
        component: () => import("@account/ts/pages/Legal/Offer.vue"),
        meta: {
            title: "Оферта",
        },
    },
    {
        path: "/legal/personal-data",
        name: "legal-personal-data",
        component: () => import("@account/ts/pages/Legal/PersonalData.vue"),
        meta: {
            title: "Поручение на обработку персональных данных",
        },
    },
    {
        path: "/legal/privacy",
        name: "legal-privacy",
        component: () => import("@account/ts/pages/Legal/Privacy.vue"),
        meta: {
            title: "Политика конфиденциальности",
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
            ...createCRUDRoutes("restaurant", {
                prefix: "account",
                root: true,
            }),
        ],
    },
    {
        path: "/:pathMatch(.*)*",
        name: "404",
        redirect: "/restaurant",
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
