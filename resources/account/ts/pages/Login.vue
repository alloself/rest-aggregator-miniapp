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

                    <Button
                        label="Войти"
                        @click="handleSubmit"
                        :loading="isLoading"
                        :disabled="isLoading"
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
import { useFormSubmit } from "@/shared/composables/useFormSubmit";
import { useLoginFormFields } from "../entities/user/forms/login";
import { useAuthStore } from "@/shared";

const router = useRouter();
const isLoading = ref(false);
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
        });
        router.push("/account/dashboard");
    } catch (error: any) {
        if (error?.response?.data?.errors) {
            formContext.value?.setErrors(error.response.data.errors);
        }
    } finally {
        isLoading.value = false;
    }
};
const { handler: handleSubmit } = useFormSubmit(submitLogin, formContext);
</script>

<style scoped>
:deep(.p-password-toggle-mask-icon) {
    cursor: pointer;
}
</style>
