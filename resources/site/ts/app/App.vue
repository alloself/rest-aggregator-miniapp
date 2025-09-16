<template>
  <div class="site-app container mx-auto">
    <router-view />
    <BottomSheetContainer />
  </div>
</template>

<script setup lang="ts">
import { BottomSheetContainer } from '@site/ts/features/bottom-sheet';
import { watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useRestaurantStore } from '@site/ts/entities/restaurant';
import { useYandexMetrica } from '@shared/composables';

const { metricaCode, initMetrica, removeMetricaScript } = useYandexMetrica();
const restaurantStore = useRestaurantStore();
const { restaurant } = storeToRefs(restaurantStore);

watch(
  () => restaurant.value?.yandex_metrica_code ?? null,
  (code) => {
    if (!code) {
      removeMetricaScript();
      return;
    }
    if (metricaCode.value === code) return;
    initMetrica(code);
  },
  { immediate: true },
);
</script>

<style>
.site-app {
  min-height: 100vh;
  width: 100%;
  margin: 0 auto;
}
</style>
