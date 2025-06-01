import { apiClient } from './client';
import type { 
  LoginRequest, 
  LoginResponse, 
  AuthUser, 
  ProfileUpdateRequest,
  PasswordResetRequest,
  PasswordResetResponse,
  TwoFactorChallengeRequest
} from '../types/auth';

export class AuthAPI {
  // Получение CSRF cookie (необходимо для SPA)
  async getCsrfCookie(): Promise<void> {
    return apiClient.get('sanctum/csrf-cookie');
  }

  // Логин пользователя (session-based)
  async login(credentials: LoginRequest): Promise<LoginResponse> {
    return apiClient.post<LoginResponse>('login', credentials);
  }

  // Получение информации о текущем пользователе
  async getUser(): Promise<{ user: AuthUser }> {
    return apiClient.get<{ user: AuthUser }>('user');
  }

  // Выход из системы (очистка session)
  async logout(): Promise<{ message: string }> {
    return apiClient.post<{ message: string }>('logout');
  }

  // Обновление профиля
  async updateProfile(data: ProfileUpdateRequest): Promise<{ user: AuthUser; message: string }> {
    return apiClient.patch<{ user: AuthUser; message: string }>('user/profile-information', data);
  }

  // Обновление пароля
  async updatePassword(data: { current_password: string; password: string; password_confirmation: string }): Promise<{ message: string }> {
    return apiClient.put<{ message: string }>('user/password', data);
  }

  // Запрос сброса пароля
  async forgotPassword(data: PasswordResetRequest): Promise<PasswordResetResponse> {
    return apiClient.post<PasswordResetResponse>('forgot-password', data);
  }

  // Сброс пароля
  async resetPassword(data: { token: string; email: string; password: string; password_confirmation: string }): Promise<{ message: string }> {
    return apiClient.post<{ message: string }>('reset-password', data);
  }

  // Подтверждение email
  async verifyEmail(id: string, hash: string, signature: string, expires: string): Promise<{ message: string }> {
    return apiClient.post<{ message: string }>(`email/verify/${id}/${hash}`, {}, {
      params: { signature, expires }
    });
  }

  // Повторная отправка email подтверждения
  async resendVerification(): Promise<{ message: string }> {
    return apiClient.post<{ message: string }>('email/verification-notification');
  }

  // Two-Factor Authentication
  async enableTwoFactor(): Promise<{ qr_code: string; secret_key: string; recovery_codes: string[] }> {
    return apiClient.post<{ qr_code: string; secret_key: string; recovery_codes: string[] }>('user/two-factor-authentication');
  }

  async confirmTwoFactor(data: { code: string }): Promise<{ message: string }> {
    return apiClient.post<{ message: string }>('user/confirmed-two-factor-authentication', data);
  }

  async disableTwoFactor(): Promise<{ message: string }> {
    return apiClient.delete<{ message: string }>('user/two-factor-authentication');
  }

  async generateRecoveryCodes(): Promise<{ recovery_codes: string[] }> {
    return apiClient.post<{ recovery_codes: string[] }>('user/two-factor-recovery-codes');
  }

  // Two-Factor Challenge (при входе)
  async twoFactorChallenge(data: TwoFactorChallengeRequest): Promise<LoginResponse> {
    return apiClient.post<LoginResponse>('two-factor-challenge', data);
  }
}

// Создаем экземпляр Auth API
export const authAPI = new AuthAPI(); 