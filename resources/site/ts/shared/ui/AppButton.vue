<template>
  <Button 
    v-bind="buttonProps"
    :class="buttonClasses"
    @click="handleClick"
  >
    <template #icon v-if="$slots.icon">
      <slot name="icon" />
    </template>
    <slot />
  </Button>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { ButtonProps } from 'primevue'

// Расширяем PrimeVue ButtonProps нашими кастомными вариантами
interface AppButtonProps extends /* @vue-ignore */ ButtonProps {
  variant?: 'primary' | 'secondary' | 'ghost' | 'accent'
  size?: 'small' | 'medium' | 'large'
  fullWidth?: boolean
}

const props = withDefaults(defineProps<AppButtonProps>(), {
  variant: 'primary',
  size: 'medium',
  fullWidth: false
})

const emit = defineEmits<{
  click: [event: Event]
}>()

// Вычисляемые свойства для стилей
const buttonProps = computed(() => {
  const { variant, size, fullWidth, ...primeProps } = props
  return primeProps
})

const buttonClasses = computed(() => {
  const baseClasses = 'inline-flex items-center justify-center font-medium rounded-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed'
  
  const variantClasses = {
    primary: 'bg-black text-white hover:opacity-90 active:translate-y-0.5',
    secondary: 'bg-gray-100 text-black hover:bg-gray-200',
    ghost: 'bg-transparent text-black hover:bg-gray-100',
    accent: 'bg-green-100 text-black hover:bg-green-200'
  }
  
  const sizeClasses = {
    small: 'h-9 px-3 text-sm',
    medium: 'h-12 px-5 text-base',
    large: 'h-14 px-8 text-lg'
  }
  
  const widthClasses = props.fullWidth ? 'w-full' : ''
  
  return [
    baseClasses,
    variantClasses[props.variant],
    sizeClasses[props.size],
    widthClasses
  ].filter(Boolean).join(' ')
})

const handleClick = (event: Event) => {
  emit('click', event)
}
</script>

<style>
/* App Button Component - Uses Tailwind CSS for styling */
</style> 