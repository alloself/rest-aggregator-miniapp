import { apiClient } from './client';
import type { Restaurant, Menu, Dish, Event, PaginatedResponse, QueryParams } from '../types';

export class RestaurantAPI {
  // Public endpoints (no auth required)
  async getAll(params?: QueryParams): Promise<PaginatedResponse<Restaurant>> {
    return apiClient.get<PaginatedResponse<Restaurant>>('restaurants', { params });
  }

  async getById(id: string): Promise<{ data: Restaurant }> {
    return apiClient.get<{ data: Restaurant }>(`restaurants/${id}`);
  }

  async getMenu(restaurantId: string): Promise<{ data: Menu[] }> {
    return apiClient.get<{ data: Menu[] }>(`restaurants/${restaurantId}/menu`);
  }

  async getEvents(restaurantId: string): Promise<{ data: Event[] }> {
    return apiClient.get<{ data: Event[] }>(`restaurants/${restaurantId}/events`);
  }

  // Restaurant owner endpoints (protected)
  async getProfile(): Promise<{ data: Restaurant }> {
    return apiClient.get<{ data: Restaurant }>('restaurant/profile');
  }

  async updateProfile(data: Partial<Restaurant>): Promise<{ data: Restaurant }> {
    return apiClient.patch<{ data: Restaurant }>('restaurant/profile', data);
  }

  // Admin endpoints (admin only)
  async create(data: Partial<Restaurant>): Promise<{ data: Restaurant }> {
    return apiClient.post<{ data: Restaurant }>('admin/restaurants', data);
  }

  async update(id: string, data: Partial<Restaurant>): Promise<{ data: Restaurant }> {
    return apiClient.patch<{ data: Restaurant }>(`admin/restaurants/${id}`, data);
  }

  async delete(id: string): Promise<{ message: string }> {
    return apiClient.delete<{ message: string }>(`admin/restaurants/${id}`);
  }

  async toggleActive(id: string): Promise<{ data: Restaurant }> {
    return apiClient.patch<{ data: Restaurant }>(`admin/restaurants/${id}/toggle-active`);
  }
}

export const restaurantAPI = new RestaurantAPI(); 