<template>
    <div id="app" class="min-h-screen bg-gray-50">
        <RouterView />
    </div>
</template>

<script setup lang="ts">
import { RouterView } from "vue-router";
</script>

<style>
.p-toast {
    z-index: 1100;
}
</style>
