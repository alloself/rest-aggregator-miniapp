// API Service для работы с ресторанами
// Строго типизированный сервис для взаимодействия с Restaurant API

import { apiClient } from './client'
import type { 
  Restaurant, 
  Dish, 
  Event, 
  News,
  Category 
} from '../types/models'

/**
 * Типы для API запросов и ответов
 */
export interface RestaurantFilters {
  category?: string
  search?: string
  min_rating?: number
  sort_by?: 'name' | 'rating' | 'created_at'
  sort_order?: 'asc' | 'desc'
  per_page?: number
  page?: number
}

export interface ApiResponse<T> {
  success: boolean
  data: T
  message?: string
}

export interface PaginatedResponse<T> extends ApiResponse<T> {
  pagination: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  filters?: Record<string, unknown>
}

export interface RestaurantListResponse {
  success: boolean
  data: Restaurant[]
  pagination: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  filters: RestaurantFilters
}

export interface MenuResponse {
  success: boolean
  data: {
    restaurant: Restaurant
    menu: Array<{
      category: string
      dishes: Dish[]
    }>
  }
}

export interface EventsResponse {
  success: boolean
  data: {
    restaurant: Restaurant
    events: Event[]
  }
  pagination: {
    current_page: number
    last_page: number
    total: number
  }
}

export interface NewsResponse {
  success: boolean
  data: {
    restaurant: Restaurant
    news: News[]
  }
  pagination: {
    current_page: number
    last_page: number
    total: number
  }
}

export interface ChefRecommendationsResponse {
  success: boolean
  data: {
    restaurant: Restaurant
    recommendations: Dish[]
  }
}

export interface LikeResponse {
  success: boolean
  message: string
  is_liked: boolean
  likes_count: number
}

export interface FavoriteResponse {
  success: boolean
  message: string
  is_favorite: boolean
}

/**
 * Основной сервис для работы с ресторанами
 */
export class RestaurantService {
  private readonly baseUrl = '/api/v1/restaurants'

  /**
   * Получить список ресторанов с фильтрацией
   */
  async getRestaurants(filters: RestaurantFilters = {}): Promise<RestaurantListResponse> {
    const params = new URLSearchParams()
    
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== null && value !== '') {
        params.append(key, String(value))
      }
    })

    const queryString = params.toString() ? `?${params.toString()}` : ''
    const response = await apiClient.get<RestaurantListResponse>(`${this.baseUrl}${queryString}`)
    
    return response.data
  }

  /**
   * Получить детальную информацию о ресторане по slug
   */
  async getRestaurant(slug: string): Promise<ApiResponse<Restaurant>> {
    const response = await apiClient.get<ApiResponse<Restaurant>>(`${this.baseUrl}/${slug}`)
    return response.data
  }

  /**
   * Получить меню ресторана по slug
   */
  async getMenu(slug: string, categorySlug?: string): Promise<MenuResponse> {
    const params = categorySlug ? `?category=${categorySlug}` : ''
    const response = await apiClient.get<MenuResponse>(`${this.baseUrl}/${slug}/menu${params}`)
    return response.data
  }

  /**
   * Получить события ресторана по slug
   */
  async getEvents(
    slug: string, 
    options: {
      type?: string
      date_from?: string
      date_to?: string
      page?: number
    } = {}
  ): Promise<EventsResponse> {
    const params = new URLSearchParams()
    
    Object.entries(options).forEach(([key, value]) => {
      if (value !== undefined && value !== null && value !== '') {
        params.append(key, String(value))
      }
    })

    const queryString = params.toString() ? `?${params.toString()}` : ''
    const response = await apiClient.get<EventsResponse>(`${this.baseUrl}/${slug}/events${queryString}`)
    
    return response.data
  }

  /**
   * Получить новости ресторана по slug
   */
  async getNews(slug: string, perPage: number = 6): Promise<NewsResponse> {
    const response = await apiClient.get<NewsResponse>(
      `${this.baseUrl}/${slug}/news?per_page=${perPage}`
    )
    return response.data
  }

  /**
   * Получить рекомендации шеф-повара по slug
   */
  async getChefRecommendations(slug: string): Promise<ChefRecommendationsResponse> {
    const response = await apiClient.get<ChefRecommendationsResponse>(
      `${this.baseUrl}/${slug}/chef-recommendations`
    )
    return response.data
  }

  /**
   * Добавить/убрать ресторан из избранного по slug
   */
  async toggleFavorite(slug: string): Promise<FavoriteResponse> {
    const response = await apiClient.post<FavoriteResponse>(`${this.baseUrl}/${slug}/favorite`)
    return response.data
  }

  /**
   * Поставить/убрать лайк блюду по slug ресторана
   */
  async toggleDishLike(restaurantSlug: string, dishId: string): Promise<LikeResponse> {
    const response = await apiClient.post<LikeResponse>(
      `${this.baseUrl}/${restaurantSlug}/dishes/${dishId}/like`
    )
    return response.data
  }
}

/**
 * Singleton instance
 */
export const restaurantService = new RestaurantService()

/**
 * Composable для использования в Vue компонентах
 */
export const useRestaurantApi = () => {
  return {
    getRestaurants: restaurantService.getRestaurants.bind(restaurantService),
    getRestaurant: restaurantService.getRestaurant.bind(restaurantService),
    getMenu: restaurantService.getMenu.bind(restaurantService),
    getEvents: restaurantService.getEvents.bind(restaurantService),
    getNews: restaurantService.getNews.bind(restaurantService),
    getChefRecommendations: restaurantService.getChefRecommendations.bind(restaurantService),
    toggleFavorite: restaurantService.toggleFavorite.bind(restaurantService),
    toggleDishLike: restaurantService.toggleDishLike.bind(restaurantService)
  }
}

/**
 * Helper функции для работы с данными
 */
export const restaurantHelpers = {
  /**
   * Форматировать рейтинг ресторана
   */
  formatRating(rating: number): string {
    return rating.toFixed(1)
  },

  /**
   * Проверить открыт ли ресторан сейчас
   */
  isOpenNow(restaurant: Restaurant): boolean {
    // Логика проверки рабочих часов
    // Пока возвращаем true, позже добавим реальную логику
    return true
  },

  /**
   * Получить основную категорию ресторана
   */
  getPrimaryCategory(restaurant: Restaurant): string {
    if (!restaurant.categories || restaurant.categories.length === 0) {
      return 'Ресторан'
    }
    return restaurant.categories[0].name
  },

  /**
   * Форматировать цену блюда
   */
  formatPrice(price: number): string {
    return new Intl.NumberFormat('ru-RU', {
      style: 'currency',
      currency: 'RUB',
      minimumFractionDigits: 0
    }).format(price)
  },

  /**
   * Получить URL изображения с fallback
   */
  getImageUrl(imageUrl: string | null, fallback: string = '/images/restaurant-placeholder.jpg'): string {
    if (!imageUrl) return fallback
    
    // Если это относительный путь, добавляем базовый URL
    if (imageUrl.startsWith('/')) {
      return imageUrl
    }
    
    return imageUrl
  },

  /**
   * Форматировать дату события
   */
  formatEventDate(date: string): string {
    return new Intl.DateTimeFormat('ru-RU', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }).format(new Date(date))
  }
} 