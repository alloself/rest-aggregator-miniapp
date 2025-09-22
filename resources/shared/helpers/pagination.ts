import { ref, Ref } from 'vue';
import { client } from '@site/ts/shared/api/axios';
import { isAxiosError } from 'axios';
import { PaginatedResponse } from '@/shared';

export interface PaginationState<T> {
  items: Ref<T[]>;
  loading: Ref<boolean>;
  error: Ref<string | null>;
  isLoadingMore: Ref<boolean>;
  page: Ref<number>;
  perPage: Ref<number>;
  hasMore: Ref<boolean>;
  savedSlug: Ref<string>;
}

export interface PaginationConfig {
  perPage?: number;
}

export function usePagination<T>(
  endpoint: string,
  config: PaginationConfig = {},
): PaginationState<T> & {
  getData: (slug: string) => Promise<void>;
  loadMore: () => Promise<void>;
  reset: () => void;
} {
  const { perPage: defaultPerPage = 5 } = config;

  const items = ref<T[]>([]) as any;
  const loading = ref(false);
  const error = ref<string | null>(null);
  const isLoadingMore = ref(false);
  const page = ref(1);
  const perPage = ref(defaultPerPage);
  const hasMore = ref(true);
  const savedSlug = ref<string>('');

  const getData = async (slug: string) => {
    loading.value = true;
    error.value = null;
    page.value = 1;
    hasMore.value = true;
    savedSlug.value = slug;

    try {
      const response = await client.get<PaginatedResponse<T>>(`api/site/restaurants/${slug}/${endpoint}`, {
        params: { page: page.value, per_page: perPage.value },
      });
      items.value = response.data.data;
      const meta = response.data.meta;
      hasMore.value = meta.current_page < meta.last_page;
    } catch (e: unknown) {
      if (isAxiosError(e)) {
        if (e.response?.status === 404) {
          error.value = 'Ресурс не найден';
        } else if (e.response?.data && typeof e.response.data === 'object' && 'message' in e.response.data) {
          error.value = String((e.response.data as { message?: string }).message || 'Ошибка запроса');
        } else {
          error.value = 'Произошла ошибка при загрузке ресурса';
        }
      } else {
        error.value = 'Произошла непредвиденная ошибка';
      }

      console.error(`Ошибка загрузки ${endpoint}:`, e);
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
      const response = await client.get<PaginatedResponse<T>>(`api/site/restaurants/${savedSlug.value}/${endpoint}`, {
        params: { page: nextPage, per_page: perPage.value },
      });
      items.value = [...items.value, ...response.data.data];
      const meta = response.data.meta;
      page.value = meta.current_page;
      hasMore.value = meta.current_page < meta.last_page;
    } catch (e: unknown) {
      if (isAxiosError(e)) {
        if (e.response?.data && typeof e.response.data === 'object' && 'message' in e.response.data) {
          error.value = String((e.response.data as { message?: string }).message || 'Ошибка запроса');
        } else {
          error.value = 'Не удалось загрузить ещё ресурсы';
        }
      } else {
        error.value = 'Произошла непредвиденная ошибка';
      }
      console.error(`Ошибка дозагрузки ${endpoint}:`, e);
    } finally {
      isLoadingMore.value = false;
    }
  };

  const reset = () => {
    items.value = [];
    loading.value = false;
    error.value = null;
    isLoadingMore.value = false;
    page.value = 1;
    hasMore.value = true;
    savedSlug.value = '';
  };

  return {
    items,
    loading,
    error,
    isLoadingMore,
    page,
    perPage,
    hasMore,
    savedSlug,
    getData,
    loadMore,
    reset,
  };
}
