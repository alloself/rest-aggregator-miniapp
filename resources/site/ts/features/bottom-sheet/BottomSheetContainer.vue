<template>
  <BottomSheet
    v-for="sheet in sheets"
    :key="sheet.id"
    :show-handle="sheet.options?.showHandle"
    :closable-by-backdrop="sheet.options?.closableByBackdrop"
    :closable-by-swipe="sheet.options?.closableBySwipe"
    :height="sheet.options?.height"
    :custom-class="sheet.options?.class"
    :z-index="sheet.options?.zIndex"
    @close="close(sheet.id)"
  >
    <component :is="sheet.component" v-bind="sheet.props" @close="close(sheet.id)" />
  </BottomSheet>
</template>

<script setup lang="ts">
import BottomSheet from '../../shared/ui/BottomSheet.vue';
import { useBottomSheet } from '../../shared';

const { sheets, close } = useBottomSheet();
</script>
