import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authAPI } from '../api/auth';
import { apiClient } from '../api/client';
import type { AuthUser, LoginRequest, AuthState, ApiError } from '../types/auth';

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<AuthUser | null>(null);
  const token = ref<string | null>(null);
  const isLoading = ref<boolean>(false);
  const error = ref<string | null>(null);

  // Computed
  const isAuthenticated = computed(() => !!user.value && !!token.value);
  const isAdmin = computed(() => user.value?.roles.includes('admin') ?? false);
  const isRestaurantOwner = computed(() => user.value?.roles.includes('restaurant_owner') ?? false);

  // Actions
  async function login(credentials: LoginRequest) {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await authAPI.login(credentials);
      
      // Сохраняем данные пользователя и токен
      user.value = response.user;
      token.value = response.token;
      
      // Устанавливаем токен в API client
      apiClient.setToken(response.token);
      
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
      // Пытаемся отправить запрос на сервер
      if (token.value) {
        await authAPI.logout();
      }
    } catch (err) {
      // Игнорируем ошибки при выходе
      console.warn('Logout error:', err);
    } finally {
      // Очищаем данные независимо от результата
      clearAuth();
      isLoading.value = false;
    }
  }

  async function fetchUser() {
    if (!token.value) return null;

    isLoading.value = true;
    error.value = null;

    try {
      const response = await authAPI.getUser();
      user.value = response.user;
      return response.user;
    } catch (err) {
      const apiError = err as ApiError;
      error.value = apiError.message;
      
      // Если токен недействителен, очищаем аутентификацию
      if (apiError.message.includes('Unauthenticated')) {
        clearAuth();
      }
      
      throw err;
    } finally {
      isLoading.value = false;
    }
  }

  function clearAuth() {
    user.value = null;
    token.value = null;
    error.value = null;
    apiClient.clearToken();
  }

  // Проверка роли пользователя
  function hasRole(role: string): boolean {
    return user.value?.roles.includes(role) ?? false;
  }

  // Проверка множественных ролей
  function hasAnyRole(roles: string[]): boolean {
    return roles.some(role => hasRole(role));
  }

  // Инициализация при запуске приложения
  function initAuth() {
    const savedToken = apiClient.getToken();
    
    if (savedToken) {
      token.value = savedToken;
      // Пытаемся получить данные пользователя
      fetchUser().catch(() => {
        // Если не удалось, очищаем токен
        clearAuth();
      });
    }
  }

  return {
    // State
    user,
    token,
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
    clearAuth,
    hasRole,
    hasAnyRole,
    initAuth,
  };
}); 