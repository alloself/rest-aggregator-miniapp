<template>
  <section v-if="chefRecommendations.length > 0" class="chef-recommendations__section">
    <h2 class="chef-recommendations__title">Шеф рекомендует</h2>

    <div class="chef-recommendations__items">

      <div
        v-for="dish in chefRecommendations"
        :key="dish.id"
        class="chef-recommendations__item"
        @click="handleDishClick(dish)"
      >
        <AppImage
          :src="dish.images?.[0]?.url || ''"
          :alt="dish.name"
          width="120"
          height="120"
          object-fit="cover"
          border-radius="lg"
          class="chef-recommendations__image chef-recommendations__image--regular"
        />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { toRefs } from 'vue';
import { AppImage } from '../../shared/ui';
import { useBottomSheet } from '../../shared';
import DishBottomSheet from '../../features/dish-bottom-sheet/DishBottomSheet.vue';
import type { DishResourceData } from '@/shared/types/resources';

interface Props {
  chefRecommendations: DishResourceData[];
}

const props = defineProps<Props>();
const { chefRecommendations } = toRefs(props);

const { open } = useBottomSheet();

const handleDishClick = (dish: DishResourceData) => {
  open(DishBottomSheet, { dish }, { bottomGap: true });
};
</script>
