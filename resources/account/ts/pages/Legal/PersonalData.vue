<template>
    <div
        class="min-h-screen flex flex-col items-center bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 px-4 py-8"
    >
        <div class="w-full max-w-3xl">
            <RouterLink
                :to="backLink"
                class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 mb-6"
            >
                <Icon name="mdi:arrow-left" size="20px" />
                {{ backLabel }}
            </RouterLink>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">
                    Поручение на обработку персональных данных
                </h1>
                <div class="prose prose-gray max-w-none">
                    <p class="text-gray-700 leading-relaxed">
                        Текст поручения на обработку персональных данных.
                        Замените на актуальный юридический текст документа.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { RouterLink } from "vue-router";
import Icon from "@shared/ui/Icon.vue";
import { useAuthStore } from "@/shared";

const authStore = useAuthStore();
const backLink = computed(() =>
    authStore.user ? "/restaurant" : "/login",
);
const backLabel = computed(() =>
    authStore.user ? "Назад в личный кабинет" : "Назад к входу",
);
</script>
