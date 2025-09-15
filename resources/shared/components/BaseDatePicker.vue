<template>
  <FloatLabel variant="on">
    <DatePicker
      id="on_label"
      class="w-full"
      :model-value="value"
      @update:modelValue="onUpdate"
      v-bind="$attrs"
    />
    <label for="on_label">{{ label }}</label>
  </FloatLabel>
  
</template>

<script setup lang="ts">
import DatePicker from 'primevue/datepicker';
import FloatLabel from 'primevue/floatlabel';

const value = defineModel<Date | null>('modelValue', {
  default: null,
});

const { label } = defineProps<{
  label: string;
}>();

/**
 * Нормализует событие DatePicker (которое может отдавать Date | Date[] | null)
 * к одиночному значению Date | null для совместимости со SmartForm.
 */
function onUpdate(newValue: unknown) {
  let normalized: Date | null = null;
  if (Array.isArray(newValue)) {
    normalized = newValue[0] instanceof Date ? newValue[0] : null;
  } else if (newValue instanceof Date || newValue === null) {
    normalized = newValue;
  }
  value.value = normalized;
}
</script>


