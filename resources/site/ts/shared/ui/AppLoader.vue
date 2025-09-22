<template>
  <div class="app-loader" role="status" :aria-label="ariaLabel" aria-live="polite">
    <ProgressSpinner
      class="app-loader__spinner"
      :style="{ width: size, height: size }"
      strokeWidth="4"
      animationDuration="0.8s"
    />
  </div>
</template>

<script setup lang="ts">
import ProgressSpinner from 'primevue/progressspinner';

type Props = {
  size?: string;
  ariaLabel?: string;
};

const { size = '24px', ariaLabel = 'Загрузка' } = defineProps<Props>();
</script>

<style scoped>
.app-loader {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
}

:deep(.p-progressspinner) {
  color: var(--color-border-light);
}

:deep(.p-progressspinner-circle) {
  stroke: var(--color-border-light) !important;
  animation: p-progressspinner-dash 1.4s ease-in-out infinite !important;
}
</style>
