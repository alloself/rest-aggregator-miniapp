import type { App } from "vue";
import PrimeVue from "primevue/config";
import Aura from "@primeuix/themes/aura";

export function setupPrimeVue(app: App): void {
    app.use(PrimeVue, {
        theme: {
            preset: Aura,
            options: {
                cssLayer: {
                    name: "primevue",
                    order: "theme, base, primevue",
                },
            },
        },
    });
} 