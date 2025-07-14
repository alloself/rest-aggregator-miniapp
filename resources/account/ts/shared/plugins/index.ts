import type { App } from "vue";
import { createPinia } from "pinia";
import { setupPrimeVue } from "./primevue";
import router from "@account/ts/app/router";

export function setupPlugins(app: App): void {
    app.use(router);
    const pinia = createPinia();
    app.use(pinia);
    setupPrimeVue(app);
}