import { createApp } from "vue";

import App from "./app/App.vue";
import router from "./app/router";
import { setupPlugins } from "./shared/plugins";
import { apiClient } from "@shared/api/client";

async function initAccountApp() {
    try {
        const app = createApp(App);

        app.use(router);

        setupPlugins(app);

        await apiClient.init();

        app.mount("#account-app");
    } catch (error) {
        console.error("Account App: initialization failed", error);
    }
}

initAccountApp();
