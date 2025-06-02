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
    isLoading.value = true;
    error.value = null;

    try {
      // API client автоматически получает CSRF cookie и логинит
      await apiClient.login(credentials);
      
      // Получаем данные пользователя после успешного логина
      const userData = await apiClient.user<AuthUser>();
      user.value = userData;
      
      return { user: userData, message: 'Login successful' };
    } catch (err) {
      const apiError = err as ApiError;
      error.value = apiError.message;
      throw err;
    } finally {
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
      console.warn('Logout error:', err);
    } finally {
      // Очищаем пользователя
      clearAuth();
      isLoading.value = false;
    }
  }

  async function fetchUser() {
    isLoading.value = true;
    error.value = null;

    try {
      const userData = await apiClient.user<AuthUser>();
      user.value = userData;
      return userData;
    } catch (err) {
      const apiError = err as ApiError;
      error.value = apiError.message;
      
      // Если не аутентифицирован, очищаем данные пользователя
      if (apiError.message.includes('Unauthenticated')) {
        clearAuth();
      }
      
      throw err;
    } finally {
      isLoading.value = false;
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

  // Инициализация при запуске приложения
  async function initAuth() {
    try {
      // Пытаемся получить текущего пользователя из session
      await fetchUser();
    } catch {
      // Если session недействительна, пользователь не залогинен
      clearAuth();
    }

    // Слушаем события потери аутентификации от API client
    window.addEventListener('auth:unauthorized', () => {
      clearAuth();
    });
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
    initAuth,
  };
}); 