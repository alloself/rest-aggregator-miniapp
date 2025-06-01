import { ref, computed } from 'vue';
import { restaurantAPI } from '../api';
import { useApi } from './useApi';
import { formatPhoneNumber } from '../utils';
import type { Restaurant } from '../types';

export function useRestaurant() {
  const { loading, error, apiCall } = useApi();
  
  // State
  const restaurants = ref<Restaurant[]>([]);
  const currentRestaurant = ref<Restaurant | null>(null);

  // Computed
  const activeRestaurants = computed(() => 
    restaurants.value.filter(r => r.is_active)
  );

  // Actions
  async function fetchRestaurants(params?: any) {
    const response = await apiCall(() => restaurantAPI.getAll(params));
    if (response) {
      restaurants.value = response.data;
    }
    return response;
  }

  async function fetchRestaurant(id: string) {
    const response = await apiCall(() => restaurantAPI.getById(id));
    if (response) {
      currentRestaurant.value = response.data;
    }
    return response?.data;
  }

  async function fetchProfile() {
    const response = await apiCall(() => restaurantAPI.getProfile());
    if (response) {
      currentRestaurant.value = response.data;
    }
    return response?.data;
  }

  async function updateProfile(data: Partial<Restaurant>) {
    const response = await apiCall(() => restaurantAPI.updateProfile(data));
    if (response) {
      currentRestaurant.value = response.data;
    }
    return response?.data;
  }

  // Utility functions (using shared utilities)

  function getRestaurantImageUrl(restaurant: Restaurant): string {
    // Return default or actual image URL
    return (restaurant.settings as any)?.image_url || '/images/restaurant-default.jpg';
  }

  function isRestaurantOpen(restaurant: Restaurant): boolean {
    // Check if restaurant is open based on settings
    if (!restaurant.settings?.working_hours) return true;
    
    const now = new Date();
    const currentHour = now.getHours();
    const workingHours = restaurant.settings.working_hours as any;
    
    return currentHour >= workingHours.open && currentHour <= workingHours.close;
  }

  return {
    // State
    restaurants,
    currentRestaurant,
    loading,
    error,
    
    // Computed
    activeRestaurants,
    
    // Actions
    fetchRestaurants,
    fetchRestaurant,
    fetchProfile,
    updateProfile,
    
    // Utils
    formatPhoneNumber,
    getRestaurantImageUrl,
    isRestaurantOpen,
  };
} 