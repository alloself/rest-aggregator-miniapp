import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { apiClient } from '../api/client';
import type { AuthUser, LoginRequest, ProfileUpdateRequest } from '../types/auth';
import type { ApiError } from '../types/api';

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<AuthUser | null>(null);
  const isLoading = ref<boolean>(false);
  const error = ref<string | null>(null);

  // Computed
  const isAuthenticated = computed(() => !!user.value);
  const isAdmin = computed(() => user.value?.roles.includes('admin') ?? false);
  const isRestaurantOwner = computed(() => user.value?.roles.includes('restaurant_owner') ?? false);

  // Actions
  async function login(credentials: LoginRequest) {
    console.log('Auth store: starting login...');
    isLoading.value = true;
    error.value = null;

    try {
      // API client автоматически получает CSRF cookie и логинит
      console.log('Auth store: calling API login...');
      await apiClient.login(credentials);
      
      // Получаем данные пользователя после успешного логина
      console.log('Auth store: fetching user data...');
      const userData = await apiClient.user<AuthUser>();
      user.value = userData;
      
      console.log('Auth store: login successful', userData);
      return { user: userData, message: 'Login successful' };
    } catch (err) {
      console.error('Auth store: login error', err);
      const apiError = err as ApiError;
      error.value = apiError.message;
      throw err;
    } finally {
      console.log('Auth store: setting isLoading to false');
      isLoading.value = false;
    }
  }

  async function logout() {
    isLoading.value = true;
    
    try {
      // Отправляем запрос на выход (очистит session)
      await apiClient.logout();
    } catch (err) {
      // Игнорируем ошибки при выходе
    } finally {
      // Очищаем пользователя
      clearAuth();
      isLoading.value = false;
    }
  }

  async function fetchUser() {
    console.log('Auth store: fetching user...');
    error.value = null;

    try {
      const userData = await apiClient.user<AuthUser>();
      user.value = userData;
      console.log('Auth store: user fetched successfully', userData);
      return userData;
    } catch (err) {
      const apiError = err as ApiError;
      error.value = apiError.message;
      
      // Если не аутентифицирован, очищаем данные пользователя
      if (apiError.message.includes('Unauthenticated')) {
        console.log('Auth store: user not authenticated, clearing auth');
        clearAuth();
      }
      
      throw err;
    }
  }

  async function updateProfile(data: ProfileUpdateRequest) {
    isLoading.value = true;
    error.value = null;

    try {
      // Пока используем patch запрос для обновления профиля
      const response = await apiClient.patch<AuthUser>('/api/user/profile', data);
      user.value = response;
      return response;
    } catch (err) {
      const apiError = err as ApiError;
      error.value = apiError.message;
      throw err;
    } finally {
      isLoading.value = false;
    }

  }

  function clearAuth() {
    user.value = null;
    error.value = null;
    isLoading.value = false;
  }

  // Проверка роли пользователя
  function hasRole(role: string): boolean {
    return user.value?.roles.includes(role) ?? false;
  }

  // Проверка множественных ролей
  function hasAnyRole(roles: string[]): boolean {
    return roles.some(role => hasRole(role));
  }

  // Проверка доступа к ресторану
  function canAccessRestaurant(restaurantId?: string): boolean {
    if (isAdmin.value) return true;
    if (!isRestaurantOwner.value) return false;
    
    return user.value?.restaurant_id === restaurantId;
  }

  return {
    // State
    user,
    isLoading,
    error,
    
    // Computed
    isAuthenticated,
    isAdmin,
    isRestaurantOwner,
    
    // Actions
    login,
    logout,
    fetchUser,
    updateProfile,
    clearAuth,
    hasRole,
    hasAnyRole,
    canAccessRestaurant,
  };
}); 