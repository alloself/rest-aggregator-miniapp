<template>
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 px-4 py-8"
    >
        <Card class="w-full max-w-md">
            <template #title>
                <h1 class="text-3xl font-bold text-gray-900 text-center">
                    Вход в аккаунт
                </h1>
            </template>
            <template #content>
                <div class="space-y-4">
                    <SmartForm :fields="fields" v-model:form="formContext" />

                    <div class="offer-checkbox flex items-center justify-center gap-2">
                        <Checkbox
                            v-model="offerAccepted"
                            :binary="true"
                            input-id="offer-accepted"
                            :disabled="isLoading"
                            class="flex-shrink-0"
                        />
                        <label
                            for="offer-accepted"
                            class="text-sm text-gray-700 cursor-pointer select-none"
                        >
                            Я согласен с условиями
                            <RouterLink
                                to="/legal/offer"
                                class="text-blue-600 hover:text-blue-800 underline"
                            >
                                оферты
                            </RouterLink>
                        </label>
                    </div>

                    <Button
                        label="Войти"
                        @click="handleSubmit"
                        :loading="isLoading"
                        :disabled="!offerAccepted || isLoading"
                        severity="info"
                        class="w-full"
                    />
                </div>
            </template>
        </Card>
    </div>
</template>

<script lang="ts" setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { type FormContext } from "vee-validate";
import SmartForm from "@/shared/components/SmartForm.vue";
import Button from "primevue/button";
import Card from "primevue/card";
import Checkbox from "primevue/checkbox";
import { useFormSubmit } from "@/shared/composables/useFormSubmit";
import { useLoginFormFields } from "../entities/user/forms/login";
import { useAuthStore } from "@/shared";

const router = useRouter();
const isLoading = ref(false);
const offerAccepted = ref(false);
const formContext = ref<FormContext>();

const { fields } = useLoginFormFields();
const { login } = useAuthStore();

const submitLogin = async () => {
    if (!formContext.value) return;

    const values = formContext.value.values;

    try {
        isLoading.value = true;
        await login({
            email: values.email,
            password: values.password,
            offer_accepted: offerAccepted.value,
        });
        router.push("/account/restaurant");
    } catch (error: unknown) {
        if (
            error &&
            typeof error === "object" &&
            "response" in error &&
            error.response &&
            typeof error.response === "object" &&
            "data" in error.response &&
            error.response.data &&
            typeof error.response.data === "object" &&
            "errors" in error.response.data
        ) {
            const errData = error.response.data as { errors: Record<string, string[]> };
            formContext.value?.setErrors(errData.errors);
        }
    } finally {
        isLoading.value = false;
    }
};
const { handler: handleSubmit } = useFormSubmit(submitLogin, formContext);
</script>

