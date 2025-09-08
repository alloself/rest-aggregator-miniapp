<template>
  <div class="news-bottom-sheet">
    <div v-if="loading" class="news-bottom-sheet__loading">Загрузка...</div>
    <div v-else-if="error" class="news-bottom-sheet__error">{{ error }}</div>
    <div v-else-if="news" class="news-bottom-sheet__content">
      <HeroCarousel
        :images="carouselImages"
        :height="'287px'"
        :autoplay="false"
        :showPagination="(news.images?.length || 0) > 1"
        :paginationBottom="'12px'"
      />

      <h1 class="news-bottom-sheet__title">{{ news.title }}</h1>
      <div class="news-bottom-sheet__description" v-html="sanitizedDescription"></div>
    </div>
    <div v-else class="news-bottom-sheet__not-found">Новость не найдена</div>
  </div>
</template>

<script setup lang="ts">
import { onBeforeMount, computed, ref } from 'vue';
import { client } from '../../shared/api/axios';
import type { News } from '@/shared/types/models';
import HeroCarousel from '@shared/ui/HeroCarousel.vue';
import { sanitizeHtml } from '../../shared/lib';

type Props = {
  slug: string;
  newsSlug: News['slug'];
};

const { slug, newsSlug } = defineProps<Props>();

const news = ref<News | null>(null);
const loading = ref(false);
const error = ref<string | null>(null);

const carouselImages = computed(() => {
  const imgs = news.value?.images ?? [];
  return imgs.filter((i) => Boolean(i.url)).map((i) => ({ url: i.url, alt: i.name || news.value?.title }));
});

const sanitizedDescription = computed(() => sanitizeHtml(news.value?.description ?? ''));

onBeforeMount(async () => {
  loading.value = true;
  error.value = null;
  news.value = null;
  try {
    const { data } = await client.get<News>(`api/site/restaurants/${slug}/news/${newsSlug}`);
    news.value = data;
  } catch (e: unknown) {
    error.value = 'Не удалось загрузить новость';
    console.error(e);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.news-bottom-sheet { 
  /* фон соответствует карточкам, чтобы скругления смотрелись цельно */
  background: var(--color-bg-primary);
}

.news-bottom-sheet__content {
  /* отступ сверху и по бокам */
  padding: 16px 16px 16px;
}

.news-bottom-sheet__title { 
  margin: 0 0 16px 0; 
  font-weight: 600; 
  font-size: 18px; 
}

.news-bottom-sheet__description { 
  /* внутренние отступы уже задаются контейнером */
  padding: 0; 
}
</style>


