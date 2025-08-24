import { defineStore } from 'pinia';
import { ref } from 'vue';

import { Restaurant } from '@/shared';
import { client } from '../../shared/api/axios';
import { isAxiosError } from 'axios';

export const useRestaurantStore = defineStore('restaurant', () => {
  const restaurant = ref<Restaurant | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const getRestaurantData = async (slug: string) => {
    loading.value = true;
    error.value = null;
    restaurant.value = null;

    try {
      const response = await client.get<Restaurant>(`api/site/restaurants/${slug}`);
      restaurant.value = response.data;
    } catch (e: unknown) {
      restaurant.value = null;

      if (isAxiosError(e) && e.response?.status === 404) {
        error.value = 'Ресторан не найден';
      } else if (isAxiosError(e) && e.response?.data?.message) {
        error.value = e.response.data.message;
      } else {
        error.value = 'Произошла ошибка при загрузке ресторана';
      }

      console.error('Ошибка загрузки ресторана:', e);
    } finally {
      loading.value = false;
    }
  };

  return { restaurant, loading, error, getRestaurantData };
});
