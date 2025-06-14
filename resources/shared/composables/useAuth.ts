import { useAuthStore } from '../stores/auth';
import type { LoginRequest } from '../types/auth';

export function useAuth() {
  const authStore = useAuthStore();

  return {
    // State
    user: authStore.user,
    isLoading: authStore.isLoading,
    error: authStore.error,
    
    // Computed
    isAuthenticated: authStore.isAuthenticated,
    isAdmin: authStore.isAdmin,
    isRestaurantOwner: authStore.isRestaurantOwner,
    
    // Actions
    login: (credentials: LoginRequest) => authStore.login(credentials),
    logout: () => authStore.logout(),
    fetchUser: () => authStore.fetchUser(),
    clearAuth: () => authStore.clearAuth(),
    hasRole: (role: string) => authStore.hasRole(role),
    hasAnyRole: (roles: string[]) => authStore.hasAnyRole(roles),
  };
} 