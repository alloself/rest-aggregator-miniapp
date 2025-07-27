import type { ISmartFormField } from "@/shared/types";
import { computed } from "vue";
import BaseInput from "@/shared/components/BaseInput.vue";
import Editor from "primevue/editor";
import BaseWorkingHoursEditor from "@/shared/components/BaseWorkingHoursEditor.vue";
import { z } from "zod";
import BaseFileList from "@/shared/components/BaseFileList.vue";
import { client } from "@/account/ts/shared/api/axios";
import { FILE_BASE_URL } from "../../category/const";

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
            component: BaseInput,
            key: "average_receipt",
            props: {
                type: "average_receipt",
                label: "Средний чек",
                fluid: true,
                autocomplete: "restaurant_average_receipt",
                name: "average_receipt",
            },
        },
        {
            component: BaseInput,
            key: "address",
            props: {
                type: "address",
                label: "Адрес",
                fluid: true,
                autocomplete: "restaurant_address",
                name: "address",
            },
        },
        {
            component: Editor,
            key: "description",
            props: {
                type: "description",
                label: "Описание",
                fluid: true,
                autocomplete: "restaurant_description",
                name: "description",
            },
        },
        {
            component: BaseWorkingHoursEditor,
            key: "working_hours",
        },
        {
            component: BaseFileList,
            props: {
                type: "file",
                baseUrl: FILE_BASE_URL,
                client: client,
                title: "Файлы",
            },
            key: "files",
        },
        {
            component: BaseFileList,
            props: {
                type: "image",
                baseUrl: FILE_BASE_URL,
                client: client,
                title: "Изображения",
            },
            key: "images",
        },
    ]);

    return {
        fields,
    };
};
