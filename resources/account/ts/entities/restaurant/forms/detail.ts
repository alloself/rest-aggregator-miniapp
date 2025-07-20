import type { ISmartFormField } from "@/shared/types";
import { computed } from "vue";
import InputText from "primevue/inputtext";
import { z } from "zod";

export const useRestaurantDetailFormFields = () => {
    const fields = computed<ISmartFormField[]>(() => [
        {
            component: InputText,
            key: "name",
            props: {
                type: "name",
                placeholder: "Название",
                fluid: true,
                autocomplete: "restaurant_name",
                name: "name",
            },
            rule: z
                .string({
                    required_error: "Название обязательно",
                })
                .min(1, "Название обязательно"),
        },
        {
            component: InputText,
            key: "slug",
            props: {
                type: "slug",
                placeholder: "Slug",
                fluid: true,
                autocomplete: "restaurant_slug",
                name: "slug",
            },
        },
    ]);

    return {
        fields,
    };
};
