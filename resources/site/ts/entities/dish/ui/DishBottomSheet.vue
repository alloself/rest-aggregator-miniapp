<template>
  <div class="dish-bottom-sheet">
    <!-- Изображение блюда -->
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

    <!-- Информация о блюде -->
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
import { AppImage } from '../../../shared/ui';
import type { Dish } from '../../../../../shared/types/models';

interface DishBottomSheetProps {
  dish: Dish;
}

const props = defineProps<DishBottomSheetProps>();

/**
 * Форматирование цены
 */
function formatPrice(price: number): string {
  return price.toLocaleString('ru-RU');
}
</script>

<style>
/* Dish Bottom Sheet Component - PostCSS Nested */
.dish-bottom-sheet {
  display: flex;
  flex-direction: column;
  gap: 16px;
  padding: 13px;

  &__image-container {
    width: 100%;
    height: 240px;
    border-radius: 20px;
    overflow: hidden;
    position: relative;
  }

  &__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  &__image-placeholder {
    width: 100%;
    height: 100%;
    background-color: var(--surface-100);
    display: flex;
    align-items: center;
    justify-content: center;

    .pi {
      font-size: 3rem;
      color: var(--surface-400);
    }
  }

  &__content {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 0 10px;
  }

  &__title {
    font-family: Inter, sans-serif;
    font-weight: 500;
    font-size: 14px;
    line-height: 1em;
    letter-spacing: -0.03em;
    color: rgba(0, 0, 0, 0.9);
    margin: 0;
  }

  &__description {
    font-family: Inter, sans-serif;
    font-weight: 400;
    font-size: 12px;
    line-height: 1.2em;
    letter-spacing: -0.03em;
    color: rgba(0, 0, 0, 0.9);
    margin: 0;
  }
}
</style>
