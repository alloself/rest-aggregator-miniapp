<template>
  <div class="site-app container mx-auto">
    <router-view />
    <BottomSheetContainer />
  </div>
</template>

<script setup lang="ts">
import { BottomSheetContainer } from '@site/ts/features/bottom-sheet';
</script>

<style>
.site-app {
  min-height: 100vh;
  width: 100%;
  margin: 0 auto;
}
</style>
