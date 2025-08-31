<template>
  <section v-if="chefRecommendations.length > 0" class="restaurant-card__chef-section">
    <h2 class="restaurant-card__chef-title">Шеф рекомендует</h2>

    <div class="restaurant-card__chef-items">
      <div v-if="chefRecommendations" class="restaurant-card__chef-featured">
        <div class="restaurant-card__chef-featured-image">
          <Icon name="chief" class="restaurant-card__chef-image" />
        </div>
      </div>

      <div
        v-for="dish in chefRecommendations"
        :key="dish.id"
        class="restaurant-card__chef-item"
        @click="handleDishClick(dish)"
      >
        <AppImage
          :src="dish.images?.[0]?.url || ''"
          :alt="dish.name"
          width="120"
          height="120"
          object-fit="cover"
          border-radius="lg"
          class="restaurant-card__chef-image restaurant-card__chef-image--regular"
        />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { toRefs } from 'vue';
import { Icon } from '@/shared';
import { AppImage } from '../../shared/ui';
import { useBottomSheet } from '../../shared';
import { DishBottomSheet } from '../../entities/dish/ui';
import type { DishResourceData } from '@/shared/types/resources';

interface Props {
  chefRecommendations: DishResourceData[];
}

const props = defineProps<Props>();
const { chefRecommendations } = toRefs(props);

const { open } = useBottomSheet();

/**
 * Открывает BottomSheet с информацией о блюде
 */
const handleDishClick = (dish: DishResourceData) => {
  open(DishBottomSheet, { dish });
};
</script>
