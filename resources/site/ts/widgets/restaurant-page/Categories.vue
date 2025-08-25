<template>
  <section class="categories">
    <CategoryFilter :categories="categories" :active-category="activeCategory" @category-change="onCategoryChange" />

    <Component :is="components[activeCategory]" :slug="slug" />
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import CategoryFilter from '../../features/category-filter/CategoryFilter.vue';
import News from '../../features/restaurant-news/News.vue';
import Events from '../../features/restaurant-events/Events.vue';
import Offers from '../../features/restaurant-offers/Offers.vue';
import { ECategory } from '../../shared';

const { slug } = defineProps<{ slug: string }>();

const categories = [
  { id: ECategory.News, label: 'Новости' },
  { id: ECategory.Events, label: 'События' },
  // { id: ECategory.Offers, label: 'Предложения' },
];

const activeCategory = ref(categories[0].id);
const onCategoryChange = (categoryId: ECategory) => {
  activeCategory.value = categoryId;
};

const components = {
  [ECategory.News]: News,
  [ECategory.Events]: Events,
  [ECategory.Offers]: Offers,
};
</script>

<style scoped>
.categories {
  display: flex;
  flex-direction: column;
}
</style>
