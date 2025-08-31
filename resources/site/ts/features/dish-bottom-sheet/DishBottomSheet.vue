<template>
  <div class="dish-bottom-sheet">
    <div class="dish-bottom-sheet__image-container">
      <AppImage
        v-if="props.dish.images?.[0]"
        :src="props.dish.images[0].url"
        :alt="props.dish.name"
        class="dish-bottom-sheet__image"
      />
      <div v-else class="dish-bottom-sheet__image-placeholder">
        <i class="pi pi-image" />
      </div>
    </div>

    <div class="dish-bottom-sheet__content">
      <h2 class="dish-bottom-sheet__title">
        {{ props.dish.name }}
        <span v-if="props.dish.price"> — {{ formatPrice(props.dish.price) }}₽</span>
      </h2>

      <div v-if="props.dish.description" class="dish-bottom-sheet__description">
        {{ props.dish.description }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { AppImage } from '../../shared/ui';
import type { Dish } from '@shared/types/models';

interface DishBottomSheetProps {
  dish: Dish;
}

const props = defineProps<DishBottomSheetProps>();

function formatPrice(price: number): string {
  return price.toLocaleString('ru-RU');
}
</script>
