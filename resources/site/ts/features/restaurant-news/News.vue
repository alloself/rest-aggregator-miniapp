<template>
  <InfiniteScroll
    :loading="loading"
    :error="error"
    :has-more="hasMore"
    :is-loading-more="isLoadingMore"
    :on-load-more="newsStore.loadMore"
  >
    <NewsCard v-for="item in news" :key="item.id" :item="item" />
  </InfiniteScroll>
</template>

<script setup lang="ts">
import { useNewsStore } from '../../entities/news';
import { storeToRefs } from 'pinia';
import { onBeforeMount } from 'vue';
import NewsCard from './NewsCard.vue';
import InfiniteScroll from '../../shared/ui/InfiniteScroll.vue';

const { slug } = defineProps<{ slug: string }>();

const newsStore = useNewsStore();
const { news, loading, error, hasMore, isLoadingMore } = storeToRefs(newsStore);

onBeforeMount(async () => {
  await newsStore.getNewsData(slug);
});
</script>
