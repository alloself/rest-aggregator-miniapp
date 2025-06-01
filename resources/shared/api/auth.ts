import { apiClient } from './client';
import type { LoginRequest, LoginResponse, AuthUser } from '../types/auth';

export class AuthAPI {
  // Логин пользователя
  async login(credentials: LoginRequest): Promise<LoginResponse> {
    return apiClient.post<LoginResponse>('auth/login', credentials);
  }

  // Получение информации о текущем пользователе
  async getUser(): Promise<{ user: AuthUser }> {
    return apiClient.get<{ user: AuthUser }>('auth/user');
  }

  // Выход из системы
  async logout(): Promise<{ message: string }> {
    return apiClient.post<{ message: string }>('auth/logout');
  }

  // Выход со всех устройств
  async logoutAll(): Promise<{ message: string }> {
    return apiClient.post<{ message: string }>('auth/logout-all');
  }

  // Получение списка токенов
  async getTokens(): Promise<{ tokens: any[] }> {
    return apiClient.get<{ tokens: any[] }>('auth/tokens');
  }

  // Отзыв конкретного токена
  async revokeToken(tokenId: number): Promise<{ message: string }> {
    return apiClient.delete<{ message: string }>(`auth/tokens/${tokenId}`);
  }
}

// Создаем экземпляр Auth API
export const authAPI = new AuthAPI(); 