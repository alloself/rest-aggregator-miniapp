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

    <div v-if="isLoading" class="image-loading">
      <div class="image-loading__spinner"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import type { ImageProps } from 'primevue';

interface AppImageProps extends /* @vue-ignore */ ImageProps {
  src: string;
  alt: string;
  width?: string | number;
  height?: string | number;
  objectFit?: 'cover' | 'contain' | 'fill' | 'none' | 'scale-down';
  borderRadius?: 'none' | 'sm' | 'md' | 'lg' | 'xl' | 'full';
  loading?: 'lazy' | 'eager';
}

const props = withDefaults(defineProps<AppImageProps>(), {
  objectFit: 'cover',
  borderRadius: 'md',
  loading: 'lazy',
});

const emit = defineEmits<{
  load: [event: Event];
  error: [event: Event];
}>();

const isLoading = ref(true);

const imageProps = computed(() => {
  const { objectFit, borderRadius, ...primeProps } = props;
  return primeProps;
});

const imageContainerClasses = computed(() => {
  return [
    'app-image',
    `app-image--radius-${props.borderRadius}`,
    {
      'app-image--loading': isLoading.value,
    },
  ];
});

const imageClasses = computed(() => {
  return ['app-image__img', `app-image__img--${props.objectFit}`];
});

const handleLoad = (event: Event) => {
  isLoading.value = false;
  emit('load', event);
};

const handleError = (event: Event) => {
  isLoading.value = false;
  emit('error', event);
};
</script>
