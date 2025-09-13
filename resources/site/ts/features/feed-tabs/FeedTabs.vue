<template>
  <div class="feed-tabs">
    <button
      v-for="category in categories"
      :key="category.id"
      :class="['feed-tabs__item', { 'feed-tabs__item--active': category.id === activeCategory }]"
      @click="selectCategory(category.id)"
      :aria-label="`Фильтр ${category.label}`"
    >
      {{ category.label }}
    </button>
  </div>
</template>

<script setup lang="ts">
import { ECategory } from '../../shared';
import { useHapticFeedback } from '../../shared/lib/composables';

interface Category {
  id: ECategory;
  label: string;
}

interface TabsProps {
  categories: Category[];
  activeCategory: string;
}

const props = defineProps<TabsProps>();

const emit = defineEmits<{
  categoryChange: [categoryId: ECategory, category: Category];
}>();

const { trigger } = useHapticFeedback();

const selectCategory = (categoryId: ECategory) => {
  const category = props.categories.find((cat) => cat.id === categoryId);
  if (category) {
    trigger();
    emit('categoryChange', categoryId, category);
  }
};
</script>
