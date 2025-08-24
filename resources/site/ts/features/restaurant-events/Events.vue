<template>
  <InfiniteScroll
    :loading="loading"
    :error="error"
    :has-more="hasMore"
    :is-loading-more="isLoadingMore"
    :on-load-more="loadMore"
  >
    <EventCard v-for="item in events" :key="item.id" :item="item" />
  </InfiniteScroll>
</template>

<script setup lang="ts">
import { useEventsStore } from '../../entities/event';
import { storeToRefs } from 'pinia';
import { onBeforeMount } from 'vue';
import EventCard from './EventCard.vue';
import InfiniteScroll from '../../shared/ui/InfiniteScroll.vue';

const { slug } = defineProps<{ slug: string }>();

const store = useEventsStore();
const { events, loading, error, hasMore, isLoadingMore } = storeToRefs(store);
const { getEventsData, loadMore } = store;

onBeforeMount(async () => {
  await getEventsData(slug);
});
</script>

<style scoped>
</style>
