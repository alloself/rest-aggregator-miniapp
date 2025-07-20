import type { ISmartFormField } from "@/shared/types";
import { computed } from "vue";
import BaseInput from "@/shared/components/BaseInput.vue";
import BaseTextarea from "@/shared/components/BaseTextarea.vue";
import { z } from "zod";

export const useRestaurantDetailFormFields = () => {
    const fields = computed<ISmartFormField[]>(() => [
        {
            component: BaseInput,
            key: "name",
            props: {
                type: "name",
                fluid: true,
                autocomplete: "restaurant_name",
                label: "Название",
                name: "name",
            },
            rule: z
                .string({
                    required_error: "Название обязательно",
                })
                .min(1, "Название обязательно"),
        },
        {
            component: BaseInput,
            key: "slug",
            props: {
                type: "slug",
                label: "Slug",
                fluid: true,
                autocomplete: "restaurant_slug",
                name: "slug",
            },
        },
        {
            component: BaseTextarea,
            key: "description",
            props: {
                type: "description",
                label: "Описание",
                fluid: true,
                autocomplete: "restaurant_description",
                name: "description",
            },
        },
    ]);

    return {
        fields,
    };
};
