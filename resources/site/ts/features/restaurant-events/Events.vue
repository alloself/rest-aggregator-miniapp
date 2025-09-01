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
import type { Event } from '@/shared';
import EventCard from './EventCard.vue';
import InfiniteScroll from '../../shared/ui/InfiniteScroll.vue';
import { useBottomSheet } from '../../shared/lib/composables/useBottomSheet';
import EventPage from '../../pages/EventPage.vue';

const { slug } = defineProps<{ slug: string }>();

const { open } = useBottomSheet();

const store = useEventsStore();
const { events, loading, error, hasMore, isLoadingMore } = storeToRefs(store);
const { getEventsData, loadMore } = store;

const handleEventClick = (event: Event) => {
  open(EventPage, { eventSlug: event.slug, slug }, { height: 90 });
};

onBeforeMount(async () => {
  await getEventsData(slug);
});
</script>
