import { defineStore } from 'pinia';
import { ref } from 'vue';
import { client } from '@site/ts/shared/api/axios';
import { isAxiosError } from 'axios';
import { News, PaginatedResponse } from '@/shared';

export const useNewsStore = defineStore('news', () => {
  const news = ref<News[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);
  const isLoadingMore = ref(false);

  // pagination state
  const page = ref(1);
  const perPage = ref(5);
  const hasMore = ref(true);
  const savedSlug = ref<string>('');

  const getNewsData = async (slug: string) => {
    loading.value = true;
    error.value = null;
    page.value = 1;
    hasMore.value = true;
    savedSlug.value = slug;

    try {
      const response = await client.get<PaginatedResponse<News>>(`api/site/restaurants/${slug}/news`, {
        params: { page: page.value, per_page: perPage.value },
      });
      news.value = response.data.data;
      const meta = response.data.meta;
      hasMore.value = meta.current_page < meta.last_page;
    } catch (e: unknown) {
      if (isAxiosError(e)) {
        if (e.response?.status === 404) {
          error.value = 'Новости не найдены';
        } else if (e.response?.data && typeof e.response.data === 'object' && 'message' in e.response.data) {
          error.value = String((e.response.data as { message?: string }).message || 'Ошибка запроса');
        } else {
          error.value = 'Произошла ошибка при загрузке новостей';
        }
      } else {
        error.value = 'Произошла непредвиденная ошибка';
      }

      console.error('Ошибка загрузки новостей:', e);
    } finally {
      loading.value = false;
    }
  };

  const loadMore = async () => {
    if (loading.value || isLoadingMore.value) return;
    if (!hasMore.value) return;
    if (!savedSlug.value) return;

    isLoadingMore.value = true;
    error.value = null;
    try {
      const nextPage = page.value + 1;
      const response = await client.get<PaginatedResponse<News>>(`api/site/restaurants/${savedSlug.value}/news`, {
        params: { page: nextPage, per_page: perPage.value },
      });
      news.value = [...news.value, ...response.data.data];
      const meta = response.data.meta;
      page.value = meta.current_page;
      hasMore.value = meta.current_page < meta.last_page;
    } catch (e: unknown) {
      if (isAxiosError(e)) {
        if (e.response?.data && typeof e.response.data === 'object' && 'message' in e.response.data) {
          error.value = String((e.response.data as { message?: string }).message || 'Ошибка запроса');
        } else {
          error.value = 'Не удалось загрузить ещё новости';
        }
      } else {
        error.value = 'Произошла непредвиденная ошибка';
      }
      console.error('Ошибка дозагрузки новостей:', e);
    } finally {
      isLoadingMore.value = false;
    }
  };

  return { getNewsData, loadMore, loading, isLoadingMore, error, news, hasMore };
});
