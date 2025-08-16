<template>
  <div class="restaurant-page">
    <RestaurantPageContent :slug="route.params.slug.toString()" />
  </div>
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router';
import RestaurantPageContent from '../widgets/restaurant-page/RestaurantPageContent.vue';

const route = useRoute();
</script>

<style>
/* =============================================================================
   RESTAURANT PAGE - БЭМ METHODOLOGY STYLES (Enhanced Suspense)
   ============================================================================= */

.restaurant-page {
  width: 100%;
  margin: 0 auto;
  background-color: rgba(255, 254, 251, 0.97);
  min-height: 100vh;
  overflow-x: hidden; /* Предотвращаем выход изображений за край экрана */
}
</style>
