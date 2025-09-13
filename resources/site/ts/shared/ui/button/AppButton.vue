<template>
  <Button :class="buttonClasses" :severity="variant" @click="handleClick">
    <template #icon v-if="$slots.icon">
      <slot name="icon" />
    </template>
    <slot />
  </Button>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Button from 'primevue/button';
import type { ButtonProps } from 'primevue';
import { useHapticFeedback } from '../../lib/composables';

interface AppButtonProps extends /* @vue-ignore */ ButtonProps {
  variant?: 'primary' | 'secondary' | 'help';
  size?: 'small' | 'medium' | 'large';
  fullWidth?: boolean;
}

const { variant = 'primary', size = 'medium', fullWidth = false } = defineProps<AppButtonProps>();

const emit = defineEmits<{
  click: [event: Event];
}>();

const buttonClasses = computed(() => {
  const sizeClass = `app-button--size-${size}`;
  const widthClass = fullWidth ? 'app-button--full' : '';
  return [sizeClass, widthClass].filter(Boolean).join(' ');
});

const { trigger } = useHapticFeedback();

const handleClick = (event: Event) => {
  trigger();
  emit('click', event);
};
</script>
