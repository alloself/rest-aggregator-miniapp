<template>
  <InfiniteScroll
    :loading="loading"
    :error="error"
    :has-more="hasMore"
    :is-loading-more="isLoadingMore"
    :on-load-more="loadMore"
  >
    <NewsCard v-for="item in news" :key="item.id" :item="item" />
  </InfiniteScroll>
</template>

<script setup lang="ts">
import { useNewsStore } from '@site/ts/stores/news';
import { storeToRefs } from 'pinia';
import { onBeforeMount } from 'vue';
import NewsCard from './NewsCard.vue';
import InfiniteScroll from '@site/ts/components/InfiniteScroll.vue';

const { slug } = defineProps<{ slug: string }>();

const store = useNewsStore();
const { news, loading, error, hasMore, isLoadingMore } = storeToRefs(store);
const { getNewsData, loadMore } = store;

onBeforeMount(async () => {
  await store.getNewsData(slug);
});
</script>
