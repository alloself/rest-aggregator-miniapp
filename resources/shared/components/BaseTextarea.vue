<template>
    <FloatLabel variant="on">
        <Textarea  id="on_label" v-model="value" v-bind="$attrs" />
        <label for="on_label">{{ label }}</label>
    </FloatLabel>
</template>

<script setup lang="ts">
import Textarea from "primevue/textarea";
import FloatLabel from "primevue/floatlabel";

const value = defineModel<string>("modelValue");

const { label } = defineProps<{
    label: string;
}>();
</script>
