/**
 * CategoryFilter - Универсальный компонент фильтрации по категориям
 * 
 * Особенности:
 * - Автоматическое добавление опции "Все" (includeAllOption)
 * - Настраиваемый текст и иконка для опции "Все"
 * - Подсчет общего количества элементов для опции "Все"
 * - Поддержка счетчиков для каждой категории
 * - Touch-friendly дизайн для Telegram Mini App
 */

<template>
  <div class="category-filter">
    <button
      v-for="category in categoriesWithAll"
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
  includeAllOption?: boolean
  allOptionLabel?: string
  allOptionIcon?: string
}

const props = withDefaults(defineProps<CategoryFilterProps>(), {
  showCounts: false,
  includeAllOption: true,
  allOptionLabel: 'Все',
  allOptionIcon: 'pi-apps'
})

const emit = defineEmits<{
  categoryChange: [categoryId: string, category: Category]
}>()

const categoriesWithAll = computed(() => {
  const allCategories = [...props.categories]
  
  if (props.includeAllOption) {
    const totalCount = props.showCounts 
      ? props.categories.reduce((sum, cat) => sum + (cat.count || 0), 0)
      : undefined

    const allCategory: Category = {
      id: 'all',
      label: props.allOptionLabel,
      icon: props.allOptionIcon,
      count: totalCount
    }
    
    allCategories.unshift(allCategory)
  }
  
  return allCategories
})

const selectCategory = (categoryId: string) => {
  const category = categoriesWithAll.value.find(cat => cat.id === categoryId)
  if (category && !category.disabled) {
    emit('categoryChange', categoryId, category)
  }
}
</script>

<style>
/* Category Filter Component - Standard CSS */
.category-filter {
  display: flex;
  gap: 6px;
  padding: 0 15px;
  margin-bottom: 24px;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE/Edge */
}

.category-filter::-webkit-scrollbar {
  display: none; /* Chrome/Safari */
}

/* Scrolling indicators - опционально */
.category-filter::before,
.category-filter::after {
  content: '';
  position: absolute;
  top: 0;
  bottom: 0;
  width: 20px;
  pointer-events: none;
  z-index: 1;
}

.category-filter::before {
  left: 0;
  background: linear-gradient(to right, #ffffff, transparent);
}

.category-filter::after {
  right: 0;
  background: linear-gradient(to left, #ffffff, transparent);
}

.category-filter__item {
  flex: 0 0 auto;
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 10px 16px;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  font-size: 15px;
  font-weight: 500;
  color: #000000;
  background-color: transparent;
  border: 1px solid rgba(0, 0, 0, 0.3);
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.25s ease;
  white-space: nowrap;
  min-height: 39px;
  user-select: none;
  letter-spacing: -0.05em;
}

.category-filter__item:hover:not(:disabled) {
  background-color: rgba(244, 243, 243, 0.5);
}

.category-filter__item:active {
  transform: translateY(0);
}

.category-filter__item--active {
  background-color: #F4F3F3;
  border: none;
  color: #000000;
}

.category-filter__item--active:hover {
  background-color: #F4F3F3;
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
.category-filter__item .pi {
  font-size: 15px;
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