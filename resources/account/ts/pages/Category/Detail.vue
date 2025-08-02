<template>
    <CategoryDetail :id="id" />
</template>

<script setup lang="ts">
import { useRoute } from "vue-router";
import { CategoryDetail } from "../../entities/category";
import { computed } from "vue";

const route = useRoute();

const id = computed(() => route.params.id?.toString() || undefined);
</script>
