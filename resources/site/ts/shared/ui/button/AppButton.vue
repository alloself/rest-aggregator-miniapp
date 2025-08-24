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

interface AppButtonProps extends /* @vue-ignore */ ButtonProps {
  variant?: 'primary' | 'secondary' | 'help';
  size?: 'small' | 'medium' | 'large';
  fullWidth?: boolean;
}

const props = withDefaults(defineProps<AppButtonProps>(), {
  variant: 'primary',
  size: 'medium',
  fullWidth: false,
});

const emit = defineEmits<{
  click: [event: Event];
}>();

const buttonClasses = computed(() => {
  const sizeClass = `app-button--size-${props.size}`;
  const widthClass = props.fullWidth ? 'app-button--full' : '';
  return [sizeClass, widthClass].filter(Boolean).join(' ');
});

const handleClick = (event: Event) => {
  emit('click', event);
};
</script>
