<template>
  <Drawer
    v-model:visible="visible"
    position="right"
    blockScroll
    :showCloseIcon="false"
    :pt="{ 
      header: { style: { display: 'none' } }, 
      content: { style: { padding: '0' } },
      root: { class: 'base-layout-drawer' }
    }"
  >
    <keep-alive>
      <component class="base-layout-drawer-content" v-if="componentsStack.length > 0" :is="currentComponent.component" v-bind="currentComponent.props" />
    </keep-alive>
  </Drawer>
</template>

<script setup lang="ts">
import Drawer from "primevue/drawer";
import { type Component, computed } from "vue";

const visible = defineModel<boolean>("visible", {
  default: false,
});

const componentsStack = defineModel<{ component: Component; props: Record<string, unknown> }[]>("componentsStack", {
  default: () => [],
});

const currentComponent = computed(() => componentsStack.value[componentsStack.value.length - 1]);
</script>

<style>
/* Стили для десктопа - половина экрана */
@media (min-width: 769px) {
  .base-layout-drawer {
    width: 50% !important;
  }
}

/* Стили для мобильных устройств - весь экран */
@media (max-width: 768px) {
  .base-layout-drawer {
    width: 100% !important;
  }
}

.base-layout-drawer-content {
  height: 100%;
  width: 100%;
  display: flex;
  overflow: auto;
}
</style>
