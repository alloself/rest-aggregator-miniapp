<template>
  <slot />

  <BottomSheet
    v-for="sheet in state.sheets"
    :key="sheet.id"
    :visible="true"
    :show-handle="sheet.options?.showHandle"
    :closable-by-backdrop="sheet.options?.closableByBackdrop"
    :closable-by-swipe="sheet.options?.closableBySwipe"
    :height="sheet.options?.height"
    :custom-class="sheet.options?.class"
    :z-index="sheet.options?.zIndex"
    @close="() => close(sheet.id)"
  >
    <component :is="sheet.component" v-bind="sheet.props" @close="() => close(sheet.id)" />
  </BottomSheet>
</template>

<script setup lang="ts">
import { useBottomSheet } from '../lib/composables/useBottomSheet';
import { onBeforeUnmount } from 'vue';
import BottomSheet from './BottomSheet.vue';

const { state, close, closeAll } = useBottomSheet();

// Очищаем все bottom sheet'ы при размонтировании провайдера
onBeforeUnmount(() => {
  closeAll();
});
</script>
