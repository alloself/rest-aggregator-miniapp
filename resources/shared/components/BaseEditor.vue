<template>
  <div>
    <h3>{{ title }}</h3>
    <Editor v-bind="$attrs" />
  </div>
</template>

<script setup lang="ts">
import Editor from "primevue/editor";

const { title = "" } = defineProps<{
  title?: string;
}>();
</script>
