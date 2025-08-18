import { defineStore } from 'pinia';
import { Event } from '@/shared';
import { usePagination } from '@/shared/helpers/pagination';

export const useEventsStore = defineStore('events', () => {
  const {
    items: events,
    loading,
    error,
    isLoadingMore,
    hasMore,
    getData: getEventsData,
    loadMore,
  } = usePagination<Event>('events');

  return { getEventsData, loadMore, loading, isLoadingMore, error, events, hasMore };
});
