import type { ISmartFormField } from "@/shared/types";
import { ref, computed } from "vue";
import InputText from "primevue/inputtext";
import Password from "primevue/password";
import { z } from "zod";

export const useLoginFormFields = () => {
    const showPassword = ref(false);

    const togglePasswordVisibility = () => {
        showPassword.value = !showPassword.value;
    };

    const fields = computed<ISmartFormField[]>(() => [
        {
            component: InputText,
            key: "email",
            props: {
                type: "email",
                placeholder: "example@email.com",
                fluid: true,
                autocomplete: "username",
                name: "email",
            },
            rule: z
                .string({
                    required_error: "Email обязателен",
                })
                .min(1, "Email обязателен")
                .email("Введите корректный email"),
        },
        {
            component: Password,
            key: "password",
            props: {
                placeholder: "Пароль",
                fluid: true,
                toggleMask: true,
                feedback: false,
                autocomplete: "current-password",
                name: "password",
            },
            rule: z
                .string({
                    required_error: "Пароль обязателен",
                })
                .min(8, "Пароль должен содержать минимум 8 символов"),
        },
    ]);

    return {
        fields,
        showPassword,
        togglePasswordVisibility,
    };
};
