<template>
  <section class="news">
    <div v-if="loading" class="news__state">Загрузка…</div>
    <div v-else-if="error" class="news__state">{{ error }}</div>
    <div v-else class="news__list">
      <NewsCard v-for="item in news" :key="item.id" :item="item" />
      <div v-if="hasMore" ref="sentinel" class="news__sentinel" aria-hidden="true" />
      <div v-if="isLoadingMore" class="news__state">Загрузка…</div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useNewsStore } from '@site/ts/stores/news';
import { storeToRefs } from 'pinia';
import { onBeforeMount, onMounted, onUnmounted, useTemplateRef, watch } from 'vue';
import NewsCard from './NewsCard.vue';

const { slug } = defineProps<{ slug: string }>();

const store = useNewsStore();
const { news, loading, error, hasMore, isLoadingMore } = storeToRefs(store);
const { loadMore } = store;

const sentinel = useTemplateRef<HTMLDivElement>('sentinel');

let observer: IntersectionObserver | null = null;

onBeforeMount(async () => {
  await store.getNewsData(slug);
});

onMounted(() => {
  observer = new IntersectionObserver(
    (entries) => {
      const [entry] = entries;
      if (!entry?.isIntersecting) return;
      if (loading.value || isLoadingMore.value || !hasMore.value) return;
      loadMore();
    },
    { root: null, rootMargin: '200px 0px', threshold: 0 },
  );

  if (sentinel.value) observer.observe(sentinel.value);
});

watch(
  sentinel,
  (el, prevEl) => {
    if (!observer) return;
    if (prevEl) observer.unobserve(prevEl);
    if (el && hasMore.value) observer.observe(el);
  },
  { flush: 'post' },
);

watch(hasMore, (val) => {
  if (!observer || !sentinel.value) return;
  if (val) observer.observe(sentinel.value);
  else observer.unobserve(sentinel.value);
});

onUnmounted(() => {
  if (observer) observer.disconnect();
  observer = null;
});
</script>

<style scoped>
.news {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.news__list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.news__state {
  color: rgba(0, 0, 0, 0.6);
  font-size: 14px;
}

.news__sentinel {
  height: 1px;
}
</style>
