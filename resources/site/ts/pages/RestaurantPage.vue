<template>
  <div class="restaurant-page">
    <!-- Enhanced Suspense - лучшее из обоих миров -->
    <EnhancedSuspenseWrapper 
      :slug="route.params.slug as string" 
      :min-duration="2500" 
    />
  </div>
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router'

// Local components  
import EnhancedSuspenseWrapper from '../components/EnhancedSuspenseWrapper.vue'

const route = useRoute()
</script>

<style>
/* =============================================================================
   RESTAURANT PAGE - БЭМ METHODOLOGY STYLES (Enhanced Suspense)
   ============================================================================= */

.restaurant-page {
  max-width: 440px;
  margin: 0 auto;
  background-color: rgba(255, 254, 251, 0.97);
  min-height: 100vh;
  overflow-x: hidden; /* Предотвращаем выход изображений за край экрана */
}
</style> 