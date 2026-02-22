import { defineStore } from 'pinia';
import { ref } from 'vue';
import { Event } from '@/shared';
import { usePagination } from '@/shared/helpers/pagination';
import { client } from '../../shared/api/axios';
import { isAxiosError } from 'axios';

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

export const useEventStore = defineStore('event', () => {
  const event = ref<Event | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const getEventData = async (slug: string, event_slug: string) => {
    loading.value = true;
    error.value = null;
    event.value = null;

    try {
      const response = await client.get<Event>(`/api/site/restaurants/${slug}/events/${event_slug}`);
      event.value = response.data;
    } catch (e: unknown) {
      event.value = null;

      if (isAxiosError(e) && e.response?.status === 404) {
        error.value = 'Событие не найдено';
      } else if (isAxiosError(e) && e.response?.data?.message) {
        error.value = e.response.data.message;
      } else {
        error.value = 'Произошла ошибка при загрузке события';
      }

      console.error('Ошибка загрузки события:', e);
    } finally {
      loading.value = false;
    }
  };

  return { event, loading, error, getEventData };
});
