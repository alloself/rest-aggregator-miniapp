<template>
  <InfiniteScroll
    :loading="loading"
    :error="error"
    :has-more="hasMore"
    :is-loading-more="isLoadingMore"
    :on-load-more="loadMore"
  >
    <EventCard v-for="item in events" :key="item.id" :item="item" @click="handleEventClick" />
  </InfiniteScroll>
</template>

<script setup lang="ts">
import { useEventsStore } from '../../entities/event';
import { storeToRefs } from 'pinia';
import { onBeforeMount } from 'vue';
import { useRouter } from 'vue-router';
import type { Event } from '@/shared';
import EventCard from './EventCard.vue';
import InfiniteScroll from '../../shared/ui/InfiniteScroll.vue';

const { slug } = defineProps<{ slug: string }>();

const router = useRouter();

const store = useEventsStore();
const { events, loading, error, hasMore, isLoadingMore } = storeToRefs(store);
const { getEventsData, loadMore } = store;

const handleEventClick = (event: Event) => {
  router.push({
    name: 'event',
    params: {
      slug: slug,
      eventSlug: event.slug,
    },
  });
};

onBeforeMount(async () => {
  await getEventsData(slug);
});
</script>
