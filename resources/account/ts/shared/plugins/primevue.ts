import type { App } from "vue";
import PrimeVue from "primevue/config";
import Lara from "@primeuix/themes/lara";

export function setupPrimeVue(app: App): void {
    app.use(PrimeVue, {
        theme: {
            preset: Lara,
            options: {
                prefix: "p",
                darkModeSelector: ".p-dark",
                cssLayer: {
                    name: "primevue",
                    order: "theme, base, primevue",
                },
            },
        },
    });
}
