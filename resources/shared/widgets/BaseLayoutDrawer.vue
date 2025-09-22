<template>
  <Drawer
    v-model:visible="visible"
    position="right"
    blockScroll
    :showCloseIcon="false"
    :pt="{
      header: { style: { display: 'none' } },
      content: { style: { padding: '0' } },
      root: { class: 'base-layout-drawer' },
    }"
  >
    <keep-alive>
      <component
        class="base-layout-drawer-content"
        :key="`${currentComponent.props.id}-${currentComponent.component.name}`"
        v-if="componentsStack.length > 0"
        :is="currentComponent.component"
        v-bind="currentComponent.props"
        modal
        @save="handleSave"
        @delete="handleDelete"
        @close="closeComponent"
      />
    </keep-alive>
  </Drawer>
</template>

<script setup lang="ts">
import Drawer from 'primevue/drawer';
import { type Component, computed } from 'vue';

const visible = defineModel<boolean>('visible', {
  default: false,
});

const componentsStack = defineModel<{ component: Component; props: Record<string, unknown> }[]>('componentsStack', {
  default: () => [],
});

const currentComponent = computed(() => componentsStack.value[componentsStack.value.length - 1]);

const closeComponent = () => {
  componentsStack.value.pop();
  if (componentsStack.value.length === 0) {
    visible.value = false;
  }
};

/**
 * Проверить, является ли значение функцией-колбэком нужной сигнатуры
 */
const isCallback = (fn: unknown): fn is (payload: unknown) => void => typeof fn === 'function';

/**
 * Получить внешний колбэк save/delete, если он передан в props текущего компонента
 */
const getCallback = (name: 'onSave' | 'onDelete'): ((payload: unknown) => void) | null => {
  const current = currentComponent.value;
  if (!current || !current.props) return null;
  const candidate = current.props[name];
  return isCallback(candidate) ? candidate : null;
};

const handleSave = (payload: unknown) => {
  const cb = getCallback('onSave');
  if (cb) cb(payload);
  closeComponent();
};

const handleDelete = (payload: unknown) => {
  const cb = getCallback('onDelete');
  if (cb) cb(payload);
  closeComponent();
};
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
