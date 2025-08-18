<template>
  <div class="restaurant-page">
    <RestaurantPageContent :slug="slug" />
  </div>
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router';
import RestaurantPageContent from '../widgets/restaurant-page/RestaurantPageContent.vue';
import { computed } from 'vue';

const route = useRoute();
const slug = computed(() => route.params.slug.toString());
</script>

<style>
.restaurant-page {
  width: 100%;
  margin: 0 auto;
  background-color: rgba(255, 254, 251, 0.97);
  min-height: 100vh;
  overflow-x: hidden;
}
</style>
