<template>
  <FloatLabel variant="on">
    <InputText 
      v-if="type === 'string'" 
      v-model="stringValue" 
      v-bind="$attrs" 
    />
    <InputNumber 
      v-else 
      v-model="numberValue" 
      v-bind="$attrs" 
    />
    <label>{{ label }}</label>
  </FloatLabel>
</template>

<script setup lang="ts">
import InputText from 'primevue/inputtext';
import FloatLabel from 'primevue/floatlabel';
import InputNumber from 'primevue/inputnumber';
import { computed } from 'vue';

const { label, type } = defineProps<{
  label: string;
  type: 'number' | 'string';
}>();

const value = defineModel<string | number | null>('modelValue');

const stringValue = computed({
  get: () => {
    if (type === 'string' && typeof value.value === 'string') {
      return value.value;
    }
    return '';
  },
  set: (newValue: string) => {
    value.value = newValue;
  }
});

const numberValue = computed({
  get: () => {
    if (type === 'number' && (typeof value.value === 'number' || value.value === null)) {
      return value.value;
    }
    return null;
  },
  set: (newValue: number | null) => {
    value.value = newValue;
  }
});
</script>