import type { App } from "vue";
import PrimeVue from "primevue/config";
import Lara from "@primeuix/themes/lara";
import { definePreset } from "@primeuix/themes";
import Tooltip from "primevue/tooltip";
import ConfirmationService from "primevue/confirmationservice";
import ToastService from "primevue/toastservice";

// Создаем кастомную тему с синими цветами вместо зеленых
const BluePreset = definePreset(Lara, {
    semantic: {
        primary: {
            50: '{blue.50}',
            100: '{blue.100}',
            200: '{blue.200}',
            300: '{blue.300}',
            400: '{blue.400}',
            500: '{blue.500}',
            600: '{blue.600}',
            700: '{blue.700}',
            800: '{blue.800}',
            900: '{blue.900}',
            950: '{blue.950}'
        },
        colorScheme: {
            light: {
                formField: {
                    hoverBorderColor: '{blue.500}',
                    focusBorderColor: '{blue.500}'
                }
            },
            dark: {
                formField: {
                    hoverBorderColor: '{blue.400}',
                    focusBorderColor: '{blue.400}'
                }
            }
        }
    }
});

export function setupPrimeVue(app: App): void {
    app.use(PrimeVue, {
        theme: {
            preset: BluePreset,
            options: {
                prefix: 'p',
                darkModeSelector: ".p-dark",
            },
        },
    });
    
    // Регистрируем сервисы PrimeVue
    app.use(ConfirmationService);
    app.use(ToastService);
    
    // Регистрируем директивы PrimeVue
    app.directive('tooltip', Tooltip);
}
