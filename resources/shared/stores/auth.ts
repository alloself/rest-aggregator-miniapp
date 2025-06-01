import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authAPI } from '../api/auth';
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
      // Сначала получаем CSRF cookie
      await authAPI.getCsrfCookie();
      
      // Затем логинимся
      const response = await authAPI.login(credentials);
      
      // Сохраняем пользователя (session хранится в httpOnly cookie)
      user.value = response.user;
      
      return response;
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
      await authAPI.logout();
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
      const response = await authAPI.getUser();
      user.value = response.user;
      return response.user;
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
      const response = await authAPI.updateProfile(data);
      user.value = response.user;
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