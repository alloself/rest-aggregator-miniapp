import { ref, reactive } from 'vue';
import { apiClient } from '../api/client';
import type { ApiError, PaginatedResponse, QueryParams } from '../types';

export function useApi() {
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Generic API call wrapper
  async function apiCall<T>(
    apiFunction: () => Promise<T>,
    options?: { showError?: boolean }
  ): Promise<T | null> {
    loading.value = true;
    error.value = null;

    try {
      const result = await apiFunction();
      return result;
    } catch (err) {
      const apiError = err as ApiError;
      error.value = apiError.message;
      
      if (options?.showError !== false) {
      }
      
      throw err;
    } finally {
      loading.value = false;
    }
  }

  // Paginated requests helper
  function usePagination<T>() {
    const data = ref<T[]>([]);
    const pagination = reactive({
      current_page: 1,
      last_page: 1,
      per_page: 15,
      total: 0,
      from: null,
      to: null,
    });

    async function fetchPage(
      endpoint: string,
      params: QueryParams = {}
    ): Promise<PaginatedResponse<T> | null> {
      const queryParams = {
        page: pagination.current_page,
        per_page: pagination.per_page,
        ...params,
      };

      return apiCall(async () => {
        const response = await apiClient.get<PaginatedResponse<T>>(endpoint, {
          params: queryParams,
        });

        // Update local data and pagination
        data.value = response.data;
        Object.assign(pagination, {
          current_page: response.current_page,
          last_page: response.last_page,
          per_page: response.per_page,
          total: response.total,
          from: response.from,
          to: response.to,
        });

        return response;
      });
    }

    function nextPage() {
      if (pagination.current_page < pagination.last_page) {
        pagination.current_page++;
        return true;
      }
      return false;
    }

    function prevPage() {
      if (pagination.current_page > 1) {
        pagination.current_page--;
        return true;
      }
      return false;
    }

    function goToPage(page: number) {
      if (page >= 1 && page <= pagination.last_page) {
        pagination.current_page = page;
        return true;
      }
      return false;
    }

    return {
      data,
      pagination,
      fetchPage,
      nextPage,
      prevPage,
      goToPage,
    };
  }

  // CRUD operations helper
  function useCrud<T>(baseEndpoint: string) {
    async function getAll(params?: QueryParams): Promise<T[] | null> {
      return apiCall(() => apiClient.get<T[]>(baseEndpoint, { params }));
    }

    async function getById(id: string): Promise<T | null> {
      return apiCall(() => apiClient.get<T>(`${baseEndpoint}/${id}`));
    }

    async function create(data: Partial<T>): Promise<T | null> {
      return apiCall(() => apiClient.post<T>(baseEndpoint, data));
    }

    async function update(id: string, data: Partial<T>): Promise<T | null> {
      return apiCall(() => apiClient.patch<T>(`${baseEndpoint}/${id}`, data));
    }

    async function remove(id: string): Promise<void | null> {
      return apiCall(() => apiClient.delete<void>(`${baseEndpoint}/${id}`));
    }

    return {
      getAll,
      getById,
      create,
      update,
      remove,
    };
  }

  return {
    loading,
    error,
    apiCall,
    usePagination,
    useCrud,
  };
} 