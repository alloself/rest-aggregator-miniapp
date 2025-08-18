import { defineStore } from 'pinia';
import { News } from '@/shared';
import { usePagination } from '@/shared/helpers/pagination';

export const useNewsStore = defineStore('news', () => {
  const {
    items: news,
    loading,
    error,
    isLoadingMore,
    hasMore,
    getData: getNewsData,
    loadMore,
  } = usePagination<News>('news');

  return { getNewsData, loadMore, loading, isLoadingMore, error, news, hasMore };
});
