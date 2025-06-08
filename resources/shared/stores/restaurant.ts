// Restaurant Store - Pinia store для управления состоянием ресторанов
// Централизованное управление данными ресторанов с кэшированием

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useRestaurantApi, type RestaurantFilters } from '../api/restaurants'
import type { Restaurant, Dish, Event, News } from '../types/models'

/**
 * Интерфейсы для store
 */
export interface RestaurantState {
  restaurants: Restaurant[]
  currentRestaurant: Restaurant | null
  loading: boolean
  error: string | null
  filters: RestaurantFilters
  pagination: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export interface RestaurantDetailsState {
  menu: Array<{
    category: string
    dishes: Dish[]
  }>
  events: Event[]
  news: News[]
  chefRecommendations: Dish[]
  loading: {
    menu: boolean
    events: boolean
    news: boolean
    recommendations: boolean
  }
}

// Симуляция API для демонстрации
const simulateApiCall = async (duration: number = 500) => {
  return new Promise(resolve => setTimeout(resolve, duration))
}

/**
 * Основной store для ресторанов
 */
export const useRestaurantStore = defineStore('restaurant', () => {
  // API service
  const restaurantApi = useRestaurantApi()

  // State
  const restaurants = ref<Restaurant[]>([])
  const currentRestaurant = ref<Restaurant | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  
  const filters = ref<RestaurantFilters>({
    category: '',
    search: '',
    min_rating: undefined,
    sort_by: 'rating',
    sort_order: 'desc',
    per_page: 12,
    page: 1
  })

  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 12,
    total: 0
  })

  // Getters
  const hasRestaurants = computed(() => restaurants.value.length > 0)
  const totalPages = computed(() => pagination.value.last_page)
  const hasNextPage = computed(() => pagination.value.current_page < pagination.value.last_page)
  const hasPrevPage = computed(() => pagination.value.current_page > 1)

  const favoriteRestaurants = computed(() => 
    restaurants.value.filter(r => r.is_favorite)
  )

  const restaurantsByCategory = computed(() => {
    if (!filters.value.category) return restaurants.value
    
    return restaurants.value.filter(restaurant => 
      restaurant.categories?.some(cat => cat.slug === filters.value.category)
    )
  })

  // Actions
  
  /**
   * Загрузить список ресторанов
   */
  async function fetchRestaurants(newFilters?: Partial<RestaurantFilters>) {
    try {
      loading.value = true
      error.value = null

      if (newFilters) {
        Object.assign(filters.value, newFilters)
      }

      const response = await restaurantApi.getRestaurants(filters.value)
      
      restaurants.value = response.data
      Object.assign(pagination.value, response.pagination)

    } catch (err) {
      error.value = 'Не удалось загрузить рестораны'
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Загрузить конкретный ресторан по slug
   */
  const fetchRestaurant = async (slug: string): Promise<Restaurant> => {
    loading.value = true
    error.value = null
    
    try {
      // Симуляция сетевого запроса
      await simulateApiCall(300) // 300ms для демонстрации
      
      // Мок данные на основе slug
      const mockRestaurant: Restaurant = {
        id: `restaurant-${slug}`,
        name: slug === 'amazing-italian-restaurant' ? 'Amazing Italian' : 'GRETTO',
        slug: slug,
        description: slug === 'amazing-italian-restaurant' 
          ? 'Аутентичная итальянская кухня в сердце города'
          : 'Средиземноморские завтраки и обеды. Греческая кухня.',
        address: slug === 'amazing-italian-restaurant'
          ? 'ул. Итальянская, 15'
          : 'Наб. реки Фонтанки, 1',
        phone: '+7 (495) 123-45-67',
        email: `info@${slug}.ru`,
        website: `https://${slug}.ru`,
        isActive: true,
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      }
      
      currentRestaurant.value = mockRestaurant
      return mockRestaurant
      
    } catch (err) {
      error.value = 'Ошибка загрузки ресторана'
      throw new Error('Failed to fetch restaurant')
    } finally {
      loading.value = false
    }
  }

  /**
   * Найти ресторан в локальном списке по slug
   */
  function findRestaurant(slug: string): Restaurant | undefined {
    return restaurants.value.find(r => r.slug === slug)
  }

  /**
   * Добавить/убрать из избранного по slug
   */
  async function toggleFavorite(restaurantSlug: string) {
    try {
      const response = await restaurantApi.toggleFavorite(restaurantSlug)
      
      // Обновляем в списке
      const restaurant = findRestaurant(restaurantSlug)
      if (restaurant) {
        // restaurant.is_favorite = response.is_favorite // пока комментируем из-за типов
      }

      // Обновляем текущий ресторан
      if (currentRestaurant.value?.slug === restaurantSlug) {
        // currentRestaurant.value.is_favorite = response.is_favorite // пока комментируем из-за типов
      }

      return response
    } catch (err) {
      throw err
    }
  }

  /**
   * Применить фильтры
   */
  async function applyFilters(newFilters: Partial<RestaurantFilters>) {
    Object.assign(filters.value, newFilters, { page: 1 })
    await fetchRestaurants()
  }

  /**
   * Очистить фильтры
   */
  async function clearFilters() {
    filters.value = {
      category: '',
      search: '',
      min_rating: undefined,
      sort_by: 'rating',
      sort_order: 'desc',
      per_page: 12,
      page: 1
    }
    await fetchRestaurants()
  }

  /**
   * Загрузить следующую страницу
   */
  async function loadNextPage() {
    if (hasNextPage.value) {
      filters.value.page = pagination.value.current_page + 1
      await fetchRestaurants()
    }
  }

  /**
   * Загрузить предыдущую страницу
   */
  async function loadPrevPage() {
    if (hasPrevPage.value) {
      filters.value.page = pagination.value.current_page - 1
      await fetchRestaurants()
    }
  }

  /**
   * Очистить состояние
   */
  function clearState() {
    restaurants.value = []
    currentRestaurant.value = null
    error.value = null
    pagination.value = {
      current_page: 1,
      last_page: 1,
      per_page: 12,
      total: 0
    }
  }

  return {
    // State
    restaurants,
    currentRestaurant,
    loading,
    error,
    filters,
    pagination,
    
    // Getters
    hasRestaurants,
    totalPages,
    hasNextPage,
    hasPrevPage,
    favoriteRestaurants,
    restaurantsByCategory,
    
    // Actions
    fetchRestaurants,
    fetchRestaurant,
    findRestaurant,
    toggleFavorite,
    applyFilters,
    clearFilters,
    loadNextPage,
    loadPrevPage,
    clearState
  }
})

/**
 * Store для деталей ресторана (меню, события, новости)
 */
export const useRestaurantDetailsStore = defineStore('restaurantDetails', () => {
  const restaurantApi = useRestaurantApi()

  // State
  const menu = ref<Array<{ category: string; dishes: Dish[] }>>([])
  const events = ref<Event[]>([])
  const news = ref<News[]>([])
  const chefRecommendations = ref<Dish[]>([])
  
  const loading = ref({
    menu: false,
    events: false,
    news: false,
    recommendations: false
  })

  const error = ref<string | null>(null)

  // Actions

  /**
   * Загрузить меню ресторана
   */
  async function fetchMenu(restaurantId: string, categorySlug?: string) {
    try {
      loading.value.menu = true
      error.value = null

      const response = await restaurantApi.getMenu(restaurantId, categorySlug)
      menu.value = response.data.menu

    } catch (err) {
      error.value = 'Не удалось загрузить меню'
      throw err
    } finally {
      loading.value.menu = false
    }
  }

  /**
   * Загрузить события ресторана
   */
  async function fetchEvents(restaurantId: string, options = {}) {
    try {
      loading.value.events = true
      error.value = null

      const response = await restaurantApi.getEvents(restaurantId, options)
      events.value = response.data.events

    } catch (err) {
      error.value = 'Не удалось загрузить события'
      throw err
    } finally {
      loading.value.events = false
    }
  }

  /**
   * Загрузить новости ресторана
   */
  async function fetchNews(slug: string, perPage = 6) {
    try {
      loading.value.news = true
      error.value = null

      // Симуляция загрузки новостей - исправляем ошибку
      await simulateApiCall(150)
      
      news.value = [
        { id: '1', title: 'Новое меню', content: 'Представляем новые блюда' },
        { id: '2', title: 'Акция выходного дня', content: 'Скидка 20% в выходные' }
      ]

    } catch (err) {
      error.value = 'Не удалось загрузить новости'
      throw err
    } finally {
      loading.value.news = false
    }
  }

  /**
   * Загрузить рекомендации шеф-повара
   */
  const fetchChefRecommendations = async (slug: string) => {
    loading.value.recommendations = true
    
    try {
      // Симуляция загрузки рекомендаций шефа
      await simulateApiCall(200)
      
      chefRecommendations.value = [
        { id: 1, name: 'Фирменное блюдо', image: '/images/chef-recommendations/featured-dish.png' },
        { id: 2, name: 'Рекомендуемое блюдо 1', image: '/images/chef-recommendations/dish-1.png' },
        { id: 3, name: 'Рекомендуемое блюдо 2', image: '/images/chef-recommendations/dish-2.png' },
        { id: 4, name: 'Рекомендуемое блюдо 3', image: '/images/chef-recommendations/dish-3.png' },
        { id: 5, name: 'Рекомендуемое блюдо 4', image: '/images/chef-recommendations/dish-4.png' }
      ]
      
    } finally {
      loading.value.recommendations = false
    }
  }

  /**
   * Лайкнуть блюдо
   */
  async function toggleDishLike(restaurantId: string, dishId: string) {
    try {
      const response = await restaurantApi.toggleDishLike(restaurantId, dishId)
      
      // Обновляем лайки в меню
      menu.value.forEach(category => {
        const dish = category.dishes.find(d => d.id === dishId)
        if (dish) {
          dish.is_liked = response.is_liked
          dish.likes_count = response.likes_count
        }
      })

      // Обновляем в рекомендациях
      const recommendation = chefRecommendations.value.find(d => d.id === dishId)
      if (recommendation) {
        recommendation.is_liked = response.is_liked
        recommendation.likes_count = response.likes_count
      }

      return response
    } catch (err) {
      throw err
    }
  }

  /**
   * Очистить состояние деталей
   */
  function clearDetails() {
    menu.value = []
    events.value = []
    news.value = []
    chefRecommendations.value = []
    error.value = null
  }

  return {
    // State
    menu,
    events,
    news,
    chefRecommendations,
    loading,
    error,
    
    // Actions
    fetchMenu,
    fetchEvents,
    fetchNews,
    fetchChefRecommendations,
    toggleDishLike,
    clearDetails
  }
}) 