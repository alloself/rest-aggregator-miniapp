import axios, { AxiosInstance, AxiosResponse, AxiosError } from 'axios';
import type { ApiError } from '../types/api';

interface LoginCredentials {
  email: string;
  password: string;
}

class ApiClient {
  private instance: AxiosInstance;

  constructor() {
    this.instance = axios.create({
      baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      timeout: 10000,
      withCredentials: true, // Критично для session-based auth
    });

    this.setupInterceptors();
  }

  private setupInterceptors() {
    // Response interceptor - обработка ошибок
    this.instance.interceptors.response.use(
      (response: AxiosResponse) => response,
      (error: AxiosError<ApiError>) => {
        // 419 - CSRF Token Mismatch
        if (error.response?.status === 419) {
          // Обновляем CSRF токен и пытаемся снова
          return this.refreshCSRFAndRetry(error);
        }
        
        // 401 - Unauthorized (сессия истекла)
        if (error.response?.status === 401) {
          // Очищаем состояние аутентификации
          this.handleAuthenticationError();
        }
        
        return Promise.reject(this.formatError(error));
      }
    );
  }

  private async refreshCSRFAndRetry(error: AxiosError<ApiError>): Promise<any> {
    try {
      // Получаем новый CSRF токен
      await this.getCSRFCookie();
      
      // Повторяем исходный запрос
      if (error.config) {
        return this.instance.request(error.config);
      }
    } catch (csrfError) {
      console.error('Failed to refresh CSRF token:', csrfError);
    }
    
    return Promise.reject(this.formatError(error));
  }

  private handleAuthenticationError() {
    // Можно отправить событие для обновления состояния аутентификации
    window.dispatchEvent(new CustomEvent('auth:unauthorized'));
  }

  private formatError(error: AxiosError<ApiError>): ApiError {
    if (error.response?.data) {
      return error.response.data;
    }
    
    return {
      message: error.message || 'Произошла неизвестная ошибка',
    };
  }

  // Получение CSRF cookie (обязательно перед первым POST запросом)
  async getCSRFCookie(): Promise<void> {
    try {
      await this.instance.get('/sanctum/csrf-cookie');
    } catch (error) {
      console.error('Failed to get CSRF cookie:', error);
      throw error;
    }
  }

  // Методы для HTTP запросов
  async get<T>(url: string, config = {}) {
    const response = await this.instance.get<T>(url, config);
    return response.data;
  }

  async post<T>(url: string, data = {}, config = {}) {
    const response = await this.instance.post<T>(url, data, config);
    return response.data;
  }

  async put<T>(url: string, data = {}, config = {}) {
    const response = await this.instance.put<T>(url, data, config);
    return response.data;
  }

  async patch<T>(url: string, data = {}, config = {}) {
    const response = await this.instance.patch<T>(url, data, config);
    return response.data;
  }

  async delete<T>(url: string, config = {}) {
    const response = await this.instance.delete<T>(url, config);
    return response.data;
  }

  // Аутентификация через session cookies
  async login(credentials: LoginCredentials): Promise<void> {
    // Сначала получаем CSRF cookie
    await this.getCSRFCookie();
    
    // Затем логинимся (Laravel возвращает просто статус, не данные)
    await this.post<void>('/login', credentials);
  }

  async logout(): Promise<void> {
    await this.post<void>('/logout');
  }

  // Получение данных текущего пользователя
  async user<T = unknown>(): Promise<T> {
    return this.get<T>('/api/user');
  }
}

// Создаем единственный экземпляр API client
export const apiClient = new ApiClient(); 