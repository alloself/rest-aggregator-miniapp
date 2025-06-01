import { apiClient } from './client';
import type { Menu, Dish, Category } from '../types/models';
import type { PaginatedResponse, QueryParams } from '../types/api';

export class MenuAPI {
  // Public endpoints
  async getMenu(restaurantId: string): Promise<{ data: Menu[] }> {
    return apiClient.get<{ data: Menu[] }>(`restaurants/${restaurantId}/menu`);
  }

  async getDishes(menuId: string, params?: QueryParams): Promise<PaginatedResponse<Dish>> {
    return apiClient.get<PaginatedResponse<Dish>>(`menu/${menuId}/dishes`, { params });
  }

  async getDish(dishId: string): Promise<{ data: Dish }> {
    return apiClient.get<{ data: Dish }>(`dishes/${dishId}`);
  }

  // Restaurant owner endpoints
  async getOwnMenu(): Promise<{ data: Menu[] }> {
    return apiClient.get<{ data: Menu[] }>('restaurant/menu');
  }

  async createMenu(data: Partial<Menu>): Promise<{ data: Menu }> {
    return apiClient.post<{ data: Menu }>('restaurant/menu', data);
  }

  async updateMenu(id: string, data: Partial<Menu>): Promise<{ data: Menu }> {
    return apiClient.patch<{ data: Menu }>(`restaurant/menu/${id}`, data);
  }

  async deleteMenu(id: string): Promise<{ message: string }> {
    return apiClient.delete<{ message: string }>(`restaurant/menu/${id}`);
  }

  // Dish management
  async getOwnDishes(menuId: string, params?: QueryParams): Promise<PaginatedResponse<Dish>> {
    return apiClient.get<PaginatedResponse<Dish>>(`restaurant/menu/${menuId}/dishes`, { params });
  }

  async createDish(menuId: string, data: Partial<Dish>): Promise<{ data: Dish }> {
    return apiClient.post<{ data: Dish }>(`restaurant/menu/${menuId}/dishes`, data);
  }

  async updateDish(dishId: string, data: Partial<Dish>): Promise<{ data: Dish }> {
    return apiClient.patch<{ data: Dish }>(`restaurant/dishes/${dishId}`, data);
  }

  async deleteDish(dishId: string): Promise<{ message: string }> {
    return apiClient.delete<{ message: string }>(`restaurant/dishes/${dishId}`);
  }

  async reorderDishes(menuId: string, dishIds: string[]): Promise<{ message: string }> {
    return apiClient.patch<{ message: string }>(`restaurant/menu/${menuId}/reorder-dishes`, {
      dish_ids: dishIds,
    });
  }

  // Category management
  async getCategories(): Promise<{ data: Category[] }> {
    return apiClient.get<{ data: Category[] }>('categories');
  }

  async assignDishToCategories(dishId: string, categoryIds: string[]): Promise<{ data: Dish }> {
    return apiClient.patch<{ data: Dish }>(`restaurant/dishes/${dishId}/categories`, {
      category_ids: categoryIds,
    });
  }

  // Like functionality (public)
  async toggleLike(dishId: string, telegramUserId: string): Promise<{ data: { liked: boolean, likes_count: number } }> {
    return apiClient.post<{ data: { liked: boolean, likes_count: number } }>(`dishes/${dishId}/toggle-like`, {
      telegram_user_id: telegramUserId,
    });
  }
}

export const menuAPI = new MenuAPI(); 