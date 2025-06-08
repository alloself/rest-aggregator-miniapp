<template>
  <div :class="imageContainerClasses">
    <Image
      v-bind="imageProps"
      :src="src"
      :alt="alt"
      :width="width"
      :height="height"
      :class="imageClasses"
      @load="handleLoad"
      @error="handleError"
    />
    
    <!-- Loading indicator -->
    <div v-if="isLoading" class="image-loading">
      <div class="image-loading__spinner"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import type { ImageProps } from 'primevue'

interface AppImageProps extends /* @vue-ignore */ ImageProps {
  src: string
  alt: string
  width?: string | number
  height?: string | number
  objectFit?: 'cover' | 'contain' | 'fill' | 'none' | 'scale-down'
  borderRadius?: 'none' | 'sm' | 'md' | 'lg' | 'xl' | 'full'
  loading?: 'lazy' | 'eager'
}

const props = withDefaults(defineProps<AppImageProps>(), {
  objectFit: 'cover',
  borderRadius: 'md',
  loading: 'lazy'
})

const emit = defineEmits<{
  load: [event: Event]
  error: [event: Event]
}>()

const isLoading = ref(true)

const imageProps = computed(() => {
  const { objectFit, borderRadius, ...primeProps } = props
  return primeProps
})

const imageContainerClasses = computed(() => {
  return [
    'app-image',
    `app-image--radius-${props.borderRadius}`,
    {
      'app-image--loading': isLoading.value
    }
  ]
})

const imageClasses = computed(() => {
  return [
    'app-image__img',
    `app-image__img--${props.objectFit}`
  ]
})

const handleLoad = (event: Event) => {
  isLoading.value = false
  emit('load', event)
}

const handleError = (event: Event) => {
  isLoading.value = false
  emit('error', event)
}
</script>

<style>
/* App Image Component - PostCSS Nested */
.app-image {
  position: relative;
  display: inline-block;
  overflow: hidden;
  background-color: var(--color-surface);

  /* Border radius variants */
  &--radius-none { border-radius: 0; }
  &--radius-sm { border-radius: var(--border-radius-sm); }
  &--radius-md { border-radius: var(--border-radius-md); }
  &--radius-lg { border-radius: var(--border-radius-lg); }
  &--radius-xl { border-radius: var(--border-radius-xl); }
  &--radius-full { border-radius: var(--border-radius-full); }

  &__img {
    width: 100%;
    height: 100%;
    transition: all var(--transition-base);

    /* Object fit variants */
    &--cover { object-fit: cover; }
    &--contain { object-fit: contain; }
    &--fill { object-fit: fill; }
    &--none { object-fit: none; }
    &--scale-down { object-fit: scale-down; }
  }
}

.image-loading {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--color-surface);

  &__spinner {
    width: 24px;
    height: 24px;
    border: 2px solid var(--color-gray-light);
    border-top: 2px solid var(--color-primary);
    border-radius: var(--border-radius-full);
    animation: spin 1s linear infinite;
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style> 