<template>
  <FloatLabel variant="on">
    <InputNumber id="on_label" class="w-full" v-model="value" v-bind="$attrs" />
    <label for="on_label">{{ label }}</label>
  </FloatLabel>
</template>

<script setup lang="ts">
import InputNumber from 'primevue/inputnumber';
import FloatLabel from 'primevue/floatlabel';

const value = defineModel<number>('modelValue', {
  default: 0,
});

const { label } = defineProps<{
  label: string;
}>();
</script>
