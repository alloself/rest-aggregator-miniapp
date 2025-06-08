<template>
  <div class="category-filter">
    <button
      v-for="category in categories"
      :key="category.id"
      :class="[
        'category-filter__item',
        { 'category-filter__item--active': category.id === activeCategory }
      ]"
      @click="selectCategory(category.id)"
      :aria-label="`Фильтр ${category.label}`"
    >
      <i v-if="category.icon" :class="['pi', category.icon]"></i>
      {{ category.label }}
      <Badge
        v-if="category.count !== undefined"
        :value="category.count"
        severity="secondary"
        size="small"
      />
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Badge from 'primevue/badge'

interface Category {
  id: string
  label: string
  icon?: string
  count?: number
  disabled?: boolean
}

interface CategoryFilterProps {
  categories: Category[]
  activeCategory: string
  showCounts?: boolean
}

const props = withDefaults(defineProps<CategoryFilterProps>(), {
  showCounts: false
})

const emit = defineEmits<{
  categoryChange: [categoryId: string, category: Category]
}>()

const selectCategory = (categoryId: string) => {
  const category = props.categories.find(cat => cat.id === categoryId)
  if (category && !category.disabled) {
    emit('categoryChange', categoryId, category)
  }
}
</script>

<style>
/* Category Filter Component - PostCSS Nested */
.category-filter {
  display: flex;
  gap: var(--spacing-sm);
  padding: 0 var(--content-padding);
  margin-bottom: var(--spacing-xl);
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE/Edge */

  &::-webkit-scrollbar {
    display: none; /* Chrome/Safari */
  }

  /* Scrolling indicators - опционально */
  &::before,
  &::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 20px;
    pointer-events: none;
    z-index: 1;
  }

  &::before {
    left: 0;
    background: linear-gradient(to right, var(--color-white), transparent);
  }

  &::after {
    right: 0;
    background: linear-gradient(to left, var(--color-white), transparent);
  }

  &__item {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    padding: var(--spacing-md) var(--spacing-xl);
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-medium);
    color: var(--color-text-secondary);
    background-color: var(--color-surface);
    border-radius: var(--border-radius-xl);
    border: none;
    cursor: pointer;
    transition: all var(--transition-base);
    white-space: nowrap;
    min-height: var(--touch-target-min);
    user-select: none;
    animation: fadeInCategory 0.3s ease-out;
    
    &:hover:not(:disabled) {
      background-color: var(--color-gray-light);
      transform: translateY(-1px);
    }
    
    &:active {
      transform: translateY(0);
    }
    
    &--active {
      background-color: var(--color-accent);
      color: var(--color-text-primary);
      box-shadow: var(--shadow-sm);
      
      &:hover {
        background-color: var(--color-accent);
      }
    }
    
    &:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      
      &:hover {
        transform: none;
        background-color: var(--color-surface);
      }
    }

    /* Icon styling */
    .pi {
      font-size: var(--font-size-sm);
    }

    /* Badge styling внутри кнопки */
    :deep(.p-badge) {
      font-size: 10px;
      min-width: 16px;
      height: 16px;
      line-height: 1;
      padding: 0 4px;
    }
  }
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
    padding: 0 var(--spacing-md);
    gap: var(--spacing-xs);

    &__item {
      padding: var(--spacing-sm) var(--spacing-md);
      font-size: 12px;
    }
  }
}
</style> 