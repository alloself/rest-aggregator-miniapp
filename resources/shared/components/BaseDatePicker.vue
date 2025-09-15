<template>
  <FloatLabel variant="on">
    <DatePicker
      id="on_label"
      class="w-full"
      :model-value="resolvedValue"
      @update:modelValue="onUpdate"
      :appendTo="'body'"
      v-bind="$attrs"
    />
    <label for="on_label">{{ label }}</label>
  </FloatLabel>
  
</template>

<script setup lang="ts">
import DatePicker from 'primevue/datepicker';
import FloatLabel from 'primevue/floatlabel';
import { computed, watch } from 'vue';

const value = defineModel<string | Date | null>('modelValue', { default: null });

const { label } = defineProps<{
  label: string;
}>();

function onUpdate(newValue: unknown) {
  let normalized: Date | null = null;
  if (Array.isArray(newValue)) {
    normalized = newValue[0] instanceof Date ? newValue[0] : null;
  } else if (newValue instanceof Date || newValue === null) {
    normalized = newValue;
  }
  value.value = normalized;
}

function toDate(val: unknown): Date | null {
  if (val === null || val === undefined) return null;
  if (val instanceof Date) return val;
  if (typeof val === 'string') {
    const parsed = new Date(val);
    return Number.isNaN(parsed.getTime()) ? null : parsed;
  }
  return null;
}

const resolvedValue = computed<Date | null>(() => toDate(value.value));

watch(
  () => value.value,
  (v) => {
    if (typeof v === 'string') {
      const d = toDate(v);
      if (d) value.value = d;
    }
  },
  { immediate: true },
);
</script>


