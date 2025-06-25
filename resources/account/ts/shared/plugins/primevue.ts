import type { App } from "vue";
import PrimeVue from "primevue/config";
import Aura from "@primeuix/themes/aura";
import ToastService from "primevue/toastservice";

import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Password from "primevue/password";
import Checkbox from "primevue/checkbox";
import Toast from "primevue/toast";
import FloatLabel from "primevue/floatlabel";
import Card from "primevue/card";

export function setupPrimeVue(app: App): void {
    app.use(PrimeVue, {
        theme: {
            preset: Aura,
        },
    });

    app.use(ToastService);

    app.component("Button", Button);
    app.component("InputText", InputText);
    app.component("Password", Password);
    app.component("Checkbox", Checkbox);
    app.component("Toast", Toast);
    app.component("FloatLabel", FloatLabel);
    app.component("Card", Card);
}
