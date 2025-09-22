<template>
  <Card :class="cardClasses">
    <template #header v-if="$slots.header">
      <div class="card__header">
        <slot name="header" />
      </div>
    </template>

    <template #content>
      <div class="card__body">
        <slot />
      </div>
    </template>

    <template #footer v-if="$slots.footer">
      <div class="card__footer">
        <slot name="footer" />
      </div>
    </template>
  </Card>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { CardProps } from 'primevue';

interface AppCardProps extends /* @vue-ignore */ CardProps {
  variant?: 'default' | 'elevated';
  padding?: 'none' | 'small' | 'medium' | 'large';
}

const props = withDefaults(defineProps<AppCardProps>(), {
  variant: 'default',
  padding: 'medium',
});

const cardClasses = computed(() => {
  return [
    'card',
    {
      'card--elevated': props.variant === 'elevated',
      [`card--padding-${props.padding}`]: props.padding !== 'medium',
    },
  ];
});
</script>
