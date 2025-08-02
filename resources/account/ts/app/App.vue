<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <RouterView />
    <BaseLayoutDrawer v-model:visible="visible" v-model:componentsStack="componentsStack" />
  </div>
</template>

<script setup lang="ts">
import { RouterView } from "vue-router";
import { useRightDrawerStore } from "../features/rightDrawer/store";
import { storeToRefs } from "pinia";
import BaseLayoutDrawer from "@/shared/widgets/BaseLayoutDrawer.vue";

const rightDrawerStore = useRightDrawerStore();

const { visible, componentsStack } = storeToRefs(rightDrawerStore);
</script>


