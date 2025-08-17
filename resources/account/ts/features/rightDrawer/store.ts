import { defineStore } from "pinia";
import { Component, ref, markRaw } from "vue";

export const useRightDrawerStore = defineStore("rightDrawer", () => {
  const visible = ref(false);

  const componentsStack = ref<{ component: Component; props: Record<string, unknown> }[]>([]);

  const pushComponent = (component: Component, props: Record<string, unknown> = {}) => {
    componentsStack.value.push({ component: markRaw(component), props });
    visible.value = true;
  };

  return {
    visible,
    componentsStack,
    pushComponent,
  };
});
