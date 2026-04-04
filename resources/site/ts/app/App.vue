<template>
  <div class="site-app container mx-auto">
    <router-view />
    <PersonalDataConsentPopup />
    <BottomSheetContainer />
  </div>
</template>

<script setup lang="ts">
import { BottomSheetContainer } from '@site/ts/features/bottom-sheet';
import { PersonalDataConsentPopup } from '@site/ts/features/personal-data-consent';
</script>

<style>
.site-app {
  min-height: 100vh;
  width: 100%;
  margin: 0 auto;
}
</style>
