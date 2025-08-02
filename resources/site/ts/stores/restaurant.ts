import { defineStore } from 'pinia';
import { ref } from 'vue';

import { Restaurant } from '@/shared';
import { client } from '../shared/api/axios';

interface RestaurantApiResponse {
  data: Restaurant;
}

export const useRestaurantStore = defineStore('restaurant', () => {
  const restaurant = ref<Restaurant | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const getRestaurantData = async (slug: string) => {
    loading.value = true;
    error.value = null;
    restaurant.value = null; // Сбрасываем предыдущие данные

    try {
      const response = await client.get<RestaurantApiResponse>(`api/site/restaurants/${slug}`);
      restaurant.value = response.data.data;
    } catch (e: any) {
      restaurant.value = null;

      if (e.response?.status === 404) {
        error.value = 'Ресторан не найден';
      } else if (e.response?.data?.message) {
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
