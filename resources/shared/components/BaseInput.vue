<template>
    <FloatLabel variant="on">
        <InputText id="on_label" v-model="value" v-bind="$attrs" />
        <label for="on_label">{{ label }}</label>
    </FloatLabel>
</template>

<script setup lang="ts">
import InputText from "primevue/inputtext";
import FloatLabel from "primevue/floatlabel";

const value = defineModel<string>("modelValue");

const { label } = defineProps<{
    label: string;
}>();
</script>
