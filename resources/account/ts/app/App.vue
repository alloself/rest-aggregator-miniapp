<template>
    <div id="app" class="min-h-screen bg-gray-50">
        <!-- Toast контейнер для уведомлений -->
        <Toast />
        
        <!-- Основной роутер контент -->
        <RouterView />
    </div>
</template>

<script setup lang="ts">
import { RouterView } from "vue-router";
import Toast from "primevue/toast";

// Настройка мета информации приложения
defineOptions({
    name: "AccountApp",
});
</script>

<style>
/* Глобальные стили приложения */
#app {
    font-family: var(--font-sans, system-ui, -apple-system, sans-serif);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Обеспечиваем правильное отображение toast уведомлений */
.p-toast {
    z-index: 1100;
}
</style>
