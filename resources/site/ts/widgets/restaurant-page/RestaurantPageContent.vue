<template>
  <div class="restaurant-page">
    <div v-if="loading" class="restaurant-page__loading">Загрузка...</div>

    <div v-else-if="error" class="restaurant-page__error">
      {{ error }}
    </div>

    <div v-else-if="restaurant" class="restaurant-page__content">
      <h1 class="restaurant-page__title">{{ restaurant.name }}</h1>
      <p v-if="restaurant.description" class="restaurant-page__description" v-html="restaurant.description"></p>
      <!-- Добавьте здесь дополнительную информацию о ресторане -->
    </div>

    <div v-else class="restaurant-page__not-found">Ресторан не найден</div>
  </div>
</template>

<script setup lang="ts">
import { onBeforeMount } from 'vue';
import { storeToRefs } from 'pinia';
import { useRestaurantStore } from '@site/ts/stores/restaurant';

const { slug } = defineProps<{ slug: string }>();

const store = useRestaurantStore();
const { restaurant, loading, error } = storeToRefs(store);

onBeforeMount(async () => {
  await store.getRestaurantData(slug);
});
</script>

<style scoped>
.restaurant-page {
  padding: 20px;
}

.restaurant-page__loading,
.restaurant-page__error,
.restaurant-page__not-found {
  text-align: center;
  padding: 40px 20px;
}

.restaurant-page__error {
  color: #dc3545;
}

.restaurant-page__title {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.restaurant-page__description {
  font-size: 1.1rem;
  line-height: 1.6;
  color: #666;
}
</style>
