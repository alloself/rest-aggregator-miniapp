<template>
    <div class="legal-page">
        <div class="legal-page__container">
            <RouterLink
                :to="backLink"
                class="legal-page__back"
            >
                <Icon name="mdi:arrow-left" size="20px" />
                {{ backLabel }}
            </RouterLink>
            <div class="legal-page__content">
                <h1 class="legal-page__title">
                    Политика конфиденциальности
                </h1>
                <div class="legal-page__text">
                    <p>
                        Текст политики конфиденциальности. Замените на
                        актуальный юридический текст документа.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useRoute } from "vue-router";
import { RouterLink } from "vue-router";
import Icon from "@shared/ui/Icon.vue";

const route = useRoute();

const restaurantSlug = computed(() => {
    const slug = route.query.restaurant;
    return typeof slug === "string" ? slug : null;
});

const backLink = computed(() => {
    if (restaurantSlug.value) {
        return { path: `/restaurant/${restaurantSlug.value}` };
    }
    return { path: "/" };
});

const backLabel = computed(() =>
    restaurantSlug.value ? "Назад в приложение" : "На главную",
);
</script>

<style scoped>
.legal-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    background: linear-gradient(to bottom right, #eff6ff, #eef2ff, #faf5ff);
    padding: 2rem 1rem;
}

.legal-page__container {
    width: 100%;
    max-width: 48rem;
}

.legal-page__back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #2563eb;
    margin-bottom: 1.5rem;
    text-decoration: none;
    font-weight: 500;
}

.legal-page__back:hover {
    color: #1d4ed8;
}

.legal-page__content {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
    padding: 1.5rem;
}

.legal-page__title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 1rem;
}

.legal-page__text {
    color: #374151;
    line-height: 1.625;
}

.legal-page__text p {
    margin: 0;
}
</style>
