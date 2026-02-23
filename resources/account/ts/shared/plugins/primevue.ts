import type { App } from "vue";
import PrimeVue from "primevue/config";
import { definePreset } from "@primeuix/themes";
import Lara from "@primeuix/themes/lara";

const AccountPreset = definePreset(Lara, {
    semantic: {
        primary: {
            50: "{blue.50}",
            100: "{blue.100}",
            200: "{blue.200}",
            300: "{blue.300}",
            400: "{blue.400}",
            500: "{blue.500}",
            600: "{blue.600}",
            700: "{blue.700}",
            800: "{blue.800}",
            900: "{blue.900}",
            950: "{blue.950}",
        },
    },
});

export function setupPrimeVue(app: App): void {
    app.use(PrimeVue, {
        theme: {
            preset: AccountPreset,
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
