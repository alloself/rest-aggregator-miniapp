<template>
  <div class="site-app container mx-auto">
    <router-view />
    <DirectLinkBanner />
    <PersonalDataConsentPopup />
    <BottomSheetContainer />
  </div>
</template>

<script setup lang="ts">
import { BottomSheetContainer } from '@site/ts/features/bottom-sheet';
import { DirectLinkBanner } from '@site/ts/features/direct-link-banner';
import { PersonalDataConsentPopup } from '@site/ts/features/personal-data-consent';
</script>

<style>
.site-app {
  min-height: 100vh;
  width: 100%;
  margin: 0 auto;
}
</style>
