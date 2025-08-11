import { defineStore } from 'pinia';
import { ref } from 'vue';
import { client } from '@site/ts/shared/api/axios';
import { News, PaginatedResponse } from '@/shared';

export const useNewsStore = defineStore('news', () => {
  const news = ref<News[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const getNewsData = async (slug: string) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await client.get<PaginatedResponse<News[]>>(`api/site/restaurants/${slug}/news`);
      news.value = response.data.data;
    } catch (e) {
      if (e.response?.status === 404) {
        error.value = 'Новости не найдены';
      } else if (e.response?.data?.message) {
        error.value = e.response.data.message;
      } else {
        error.value = 'Произошла ошибка при загрузке новостей';
      }

      console.error('Ошибка загрузки новостей:', e);
    } finally {
      loading.value = false;
    }
  };

  return { getNewsData, loading, error, news };
});
