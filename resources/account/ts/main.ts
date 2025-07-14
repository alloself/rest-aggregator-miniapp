import { createApp } from "vue";

import App from "./app/App.vue";

import { setupPlugins } from "./shared/plugins";
import { useAuthStore } from "@/shared";
import { initClient } from "./shared/api/axios";
import router from "./app/router";
import { storeToRefs } from "pinia";

async function initAccountApp() {
    try {
        const app = createApp(App);

        setupPlugins(app);

        await initClient();

        const authStore = useAuthStore();

        const { user } = storeToRefs(authStore);

        await authStore.fetchUser();

        const currentPath = window.location.pathname;
        const loginUrl = router.resolve({ name: "login" }).href;
        const isLoginPage = currentPath === loginUrl;

        if (!user.value && !isLoginPage) {
            window.location.href = loginUrl;
            return;
        }

        app.mount("#account-app");
    } catch (error) {
        console.error("Account App: initialization failed", error);
    }
}

initAccountApp();
