import type { ISmartFormField } from "@/shared/types";
import { computed } from "vue";
import InputText from "primevue/inputtext";
import { z } from "zod";

export const useCategoryDetailFormFields = () => {
    const fields = computed<ISmartFormField[]>(() => [
        {
            component: InputText,
            key: "name",
            props: {
                type: "name",
                placeholder: "Название",
                fluid: true,
                autocomplete: "category_name",
                name: "name",
            },
            rule: z
                .string({
                    required_error: "Название обязательно",
                })
                .min(1, "Название обязательно"),
        },
    ]);

    return {
        fields,
    };
};
