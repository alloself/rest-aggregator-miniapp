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
import { computed } from 'vue'
import type { CardProps } from 'primevue'

interface AppCardProps extends /* @vue-ignore */ CardProps {
  variant?: 'default' | 'elevated'
  padding?: 'none' | 'small' | 'medium' | 'large'
}

const props = withDefaults(defineProps<AppCardProps>(), {
  variant: 'default',
  padding: 'medium'
})

const cardClasses = computed(() => {
  return [
    'card',
    {
      'card--elevated': props.variant === 'elevated',
      [`card--padding-${props.padding}`]: props.padding !== 'medium'
    }
  ]
})
</script>

<style>
/* App Card Component - PostCSS Nested */
.card {
  &--padding-none {
    .card__header,
    .card__body,
    .card__footer {
      padding: 0;
    }
  }

  &--padding-small {
    .card__header,
    .card__body,
    .card__footer {
      padding: var(--spacing-sm);
    }
  }

  &--padding-large {
    .card__header,
    .card__body,
    .card__footer {
      padding: var(--spacing-2xl);
    }
  }
}
</style> 