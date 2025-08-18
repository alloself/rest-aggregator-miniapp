<template>
  <div id="site-app" class="site-app">
    <!-- Site Application Container -->
    <div class="site-content">
      
      <!-- Router View for SPA Navigation -->
      <div class="container mx-auto">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Site App - главный компонент публичного сайта с Vue Router
</script>

<style>
/* Site App Component - PostCSS Nested */
.site-app {
  min-height: 100vh;
  width: 100%;
  margin: 0 auto;
}
</style> 