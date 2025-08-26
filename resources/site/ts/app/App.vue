<template>
  <div class="site-app container mx-auto">
    <router-view />
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
  </div>
</template>

<script setup lang="ts">
import { onBeforeUnmount } from 'vue';
import { useBottomSheet, BottomSheet } from '../shared';

const { state, close, closeAll } = useBottomSheet();

onBeforeUnmount(() => {
  closeAll();
});
</script>

<style>
.site-app {
  min-height: 100vh;
  width: 100%;
  margin: 0 auto;
}
</style>
