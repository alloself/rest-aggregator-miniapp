<template>
  <div class="category-filter">
    <button
      v-for="category in categories"
      :key="category.id"
      :class="['category-filter__item', { 'category-filter__item--active': category.id === activeCategory }]"
      @click="selectCategory(category.id)"
      :aria-label="`Фильтр ${category.label}`"
    >
      {{ category.label }}
    </button>
  </div>
</template>

<script setup lang="ts">
interface Category {
  id: string;
  label: string;
}

interface CategoryFilterProps {
  categories: Category[];
  activeCategory: string;
}

const props = defineProps<CategoryFilterProps>();

const emit = defineEmits<{
  categoryChange: [categoryId: string, category: Category];
}>();

const selectCategory = (categoryId: string) => {
  const category = props.categories.find((cat) => cat.id === categoryId);
  if (category) {
    emit('categoryChange', categoryId, category);
  }
};
</script>

<style>
/* Category Filter Component - Standard CSS */
.category-filter {
  display: flex;
  gap: 6px;
  padding: 0 15px;
  margin-bottom: 16px;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE/Edge */
}

.category-filter::-webkit-scrollbar {
  display: none; /* Chrome/Safari */
}

/* Scrolling indicators - опционально */
/* Убраны белые градиенты по бокам, создававшие эффект внутренней тени */

.category-filter__item {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 10px 16px;
  font-family:
    'Inter',
    -apple-system,
    BlinkMacSystemFont,
    sans-serif;
  font-size: 15px;
  font-weight: 500;
  color: #000000;
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.25s ease;
  white-space: nowrap;
  min-height: 39px;
  user-select: none;
  letter-spacing: -0.05em;
  background-color: #f4f3f3;
  border: 1px solid transparent;
}

.category-filter__item:hover:not(:disabled) {
  background-color: rgba(244, 243, 243, 0.9);
}

.category-filter__item:active {
  transform: translateY(0);
}

.category-filter__item--active {
  background-color: transparent;
  border: 1px solid rgba(0, 0, 0, 0.3);
}

.category-filter__item--active:hover {
  background-color: #f4f3f3;
}

.category-filter__item:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.category-filter__item:disabled:hover {
  transform: none;
  background-color: transparent;
}

/* Icon styling */
.category-filter__item .mdi-icon {
  flex-shrink: 0;
}

/* Badge styling внутри кнопки */
.category-filter__item :deep(.p-badge) {
  font-size: 10px;
  min-width: 16px;
  height: 16px;
  line-height: 1;
  padding: 0 4px;
}

/* Анимация для smooth transition */
@keyframes fadeInCategory {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive для маленьких экранов */
@media (max-width: 375px) {
  .category-filter {
    padding: 0 12px;
    gap: 4px;
  }

  .category-filter__item {
    padding: 8px 12px;
    font-size: 12px;
  }
}
</style>
