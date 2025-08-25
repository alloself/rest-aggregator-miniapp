<template>
  <BottomSheetProvider>
    <div class="site-app container mx-auto">
      <router-view />
    </div>
  </BottomSheetProvider>
</template>

<script setup lang="ts">
import { BottomSheetProvider } from '../shared/ui'
</script>

<style>
.site-app {
  min-height: 100vh;
  width: 100%;
  margin: 0 auto;
}
</style>
