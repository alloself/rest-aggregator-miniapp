<template>
  <section class="news">
    <div v-if="loading" class="news__state">Загрузка…</div>
    <div v-else-if="error" class="news__state">{{ error }}</div>
    <div v-else class="news__list">
      <NewsCard v-for="item in news" :key="item.id" :item="item" />
    </div>
  </section>
</template>

<script setup lang="ts">
import { useNewsStore } from '@site/ts/stores/news';
import { storeToRefs } from 'pinia';
import { onBeforeMount } from 'vue';
import NewsCard from './NewsCard.vue';

const { slug } = defineProps<{ slug: string }>();

const store = useNewsStore();
const { news, loading, error } = storeToRefs(store);

onBeforeMount(async () => {
  await store.getNewsData(slug);
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
</style>
