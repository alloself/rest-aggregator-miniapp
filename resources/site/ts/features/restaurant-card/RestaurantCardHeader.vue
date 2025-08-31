<template>
  <div class="restaurant-card__header">
    <h1 class="restaurant-card__title">
      {{ restaurant?.name }}
    </h1>
    <p v-if="restaurant?.subtitle" class="restaurant-card__description">
      {{ restaurant.subtitle }}
    </p>
  </div>
</template>

<script setup lang="ts">
import type { RestaurantResourceData } from '@shared/types/resources';

interface Props {
  restaurant: RestaurantResourceData | null;
}

defineProps<Props>();
</script>
