<template>
  <div class="base-color-input">
    <FloatLabel variant="on">
      <div class="base-color-input__row">
        <input
          type="color"
          class="base-color-input__picker"
          :value="normalizedValue"
          @input="handleColorPickerInput"
          :aria-label="label"
        />
        <InputText
          :id="inputId"
          class="base-color-input__text w-full"
          :model-value="displayValue"
          @update:model-value="handleTextInput"
          v-bind="$attrs"
        />
      </div>
      <label :for="inputId">{{ label }}</label>
    </FloatLabel>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import InputText from 'primevue/inputtext';
import FloatLabel from 'primevue/floatlabel';

const value = defineModel<string>('modelValue', {
  default: '',
});

const props = defineProps<{
  label: string;
  name?: string;
}>();

const inputId = computed(() => `color-${props.name ?? 'input'}`);

const HEX_REGEX = /^#?([a-fA-F0-9]{6})$/;

function normalizeHex(hex: string): string {
  const match = hex.match(HEX_REGEX);
  if (!match) return '#000000';
  return `#${match[1].toUpperCase()}`;
}

const normalizedValue = computed(() => {
  const val = value.value ?? '';
  if (val === '') return '#000000';
  const normalized = normalizeHex(val);
  return normalized === '#000000' ? val : normalized;
});

const displayValue = computed(() => value.value ?? '');

function handleColorPickerInput(event: Event): void {
  const target = event.target;
  if (target instanceof HTMLInputElement && target.value) {
    value.value = target.value;
  }
}

function handleTextInput(newValue: string | undefined): void {
  const trimmed = (newValue ?? '').trim();
  value.value = trimmed === '' ? '' : trimmed.startsWith('#') ? trimmed : `#${trimmed}`;
}
</script>

<style scoped>
.base-color-input__row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.base-color-input__picker {
  width: 40px;
  height: 40px;
  padding: 2px;
  border: 1px solid var(--p-input-border-color, #ced4da);
  border-radius: 6px;
  cursor: pointer;
  background: transparent;
}

.base-color-input__picker::-webkit-color-swatch-wrapper {
  padding: 2px;
}

.base-color-input__picker::-webkit-color-swatch {
  border: none;
  border-radius: 4px;
}

.base-color-input__text {
  flex: 1;
}
</style>
