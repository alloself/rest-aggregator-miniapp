<template>
  <div class="flex items-center gap-2">
    <Checkbox v-model="value" binary v-bind="$attrs" />
    <label class="text-sm" @click="toggle">{{ label }}</label>
  </div>
</template>

<script setup lang="ts">
import Checkbox from 'primevue/checkbox';

const value = defineModel<boolean>('modelValue', { default: false });

const { label = '' } = defineProps<{
  label?: string;
}>();

function toggle() {
  value.value = !value.value;
}
</script>


