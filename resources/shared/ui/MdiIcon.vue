<template>
  <img 
    v-if="iconSrc"
    :src="iconSrc" 
    :alt="alt"
    :class="['mdi-icon', sizeClass, colorClass]"
    :style="customStyle"
  />
  <div v-else class="mdi-icon-placeholder" :class="sizeClass">
    <!-- Fallback if icon not found -->
    <span>?</span>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  /** Название иконки из MDI (без префикса mdi-) */
  name: string
  /** Размер иконки: xs(12px), sm(16px), md(20px), lg(24px), xl(32px) или custom */
  size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl' | 'custom'
  /** Кастомный размер в пикселях (используется только с size="custom") */
  customSize?: number
  /** Цвет иконки: black, white, primary, gray или custom */
  color?: 'black' | 'white' | 'primary' | 'gray' | 'custom'
  /** Alt текст для accessibility */
  alt?: string
  /** Кастомные CSS стили */
  style?: Record<string, string | number>
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
  color: 'black',
  alt: '',
  style: () => ({})
})

// Динамический импорт SVG иконок
const iconSrc = computed(() => {
  try {
    // Преобразуем название иконки в правильный формат файла
    const iconFileName = props.name.includes('.svg') 
      ? props.name 
      : `${props.name}.svg`
    
    // Возвращаем путь к SVG файлу
    return new URL(`../../../node_modules/@mdi/svg/svg/${iconFileName}`, import.meta.url).href
  } catch (error) {
    return null
  }
})

// CSS классы для размеров
const sizeClass = computed(() => {
  if (props.size === 'custom') return 'mdi-icon--custom'
  return `mdi-icon--${props.size}`
})

// CSS классы для цветов
const colorClass = computed(() => {
  if (props.color === 'custom') return 'mdi-icon--color-custom'
  return `mdi-icon--color-${props.color}`
})

// Кастомные стили
const customStyle = computed(() => {
  const style: Record<string, string | number> = { ...props.style }
  
  if (props.size === 'custom' && props.customSize) {
    style.width = `${props.customSize}px`
    style.height = `${props.customSize}px`
  }
  
  return style
})
</script>

<style>
/* MDI Icon Component - PostCSS Nested */
.mdi-icon {
  display: inline-block;
  object-fit: contain;
  flex-shrink: 0;
  vertical-align: middle;

  /* Size variants */
  &--xs {
    width: 12px;
    height: 12px;
  }

  &--sm {
    width: 16px;
    height: 16px;
  }

  &--md {
    width: 20px;
    height: 20px;
  }

  &--lg {
    width: 24px;
    height: 24px;
  }

  &--xl {
    width: 32px;
    height: 32px;
  }

  /* Color variants */
  &--color-black {
    filter: brightness(0);
  }

  &--color-white {
    filter: brightness(0) invert(1);
  }

  &--color-primary {
    filter: hue-rotate(210deg) saturate(1.5) brightness(0.8);
  }

  &--color-gray {
    filter: brightness(0) opacity(0.6);
  }
}

/* Fallback placeholder */
.mdi-icon-placeholder {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background-color: #f0f0f0;
  border-radius: 2px;
  color: #999;
  font-size: 10px;
  font-weight: bold;
}
</style> 