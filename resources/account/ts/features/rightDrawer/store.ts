import { defineStore } from "pinia";
import { Component, ref, shallowRef } from "vue";

export const useRightDrawerStore = defineStore("rightDrawer", () => {
  const visible = ref(false);

  const componentsStack = shallowRef<{ component: Component; props: Record<string, unknown> }[]>([]);

  const pushComponent = (component: Component, props: Record<string, unknown> = {}) => {
    componentsStack.value.push({ component, props });
    visible.value = true;
  };

  return {
    visible,
    componentsStack,
    pushComponent,
  };
});
