// Примеры использования lodash утилит в Restaurant Aggregator Platform
import { arrayUtils, objectUtils, stringUtils, restaurantUtils } from './index'
import type { Restaurant, Dish, Event, Category } from '../types/models'

// Пример данных (используем Partial для упрощения примеров)
const sampleDishes: Partial<Dish>[] = [
  {
    id: '1',
    menu_id: '1',
    name: 'Том Ям',
    description: 'Острый тайский суп',
    price: 450,
    image_path: null,
    is_available: true,
    allergens: null,
    sort_order: 1,
    likes_count: 15,
  },
  {
    id: '2',
    menu_id: '1',
    name: 'Паста Карбонара',
    description: 'Классическая итальянская паста',
    price: 380,
    image_path: null,
    is_available: true,
    allergens: null,
    sort_order: 2,
    likes_count: 23,
  },
]

const sampleEvents: Partial<Event>[] = [
  {
    id: '1',
    restaurant_id: '1',
    title: 'Джазовый вечер',
    description: 'Живая музыка каждую пятницу',
    start_date: new Date('2024-02-16T19:00:00') as any,
    end_date: new Date('2024-02-16T23:00:00') as any,
    max_participants: 50,
    price: 1000,
    status: 'published',
    likes_count: 8,
    is_active: false,
  },
]

// Примеры использования arrayUtils
export const arrayExamples = {
  // Группировка блюд по цене
  groupDishesByPriceRange: () => {
    const priceRanges = sampleDishes.map(dish => {
      if ((dish.price || 0) < 300) return 'budget'
      if ((dish.price || 0) < 500) return 'medium'
      return 'premium'
    })
    
    return arrayUtils.groupBy(
      sampleDishes.map((dish, index) => ({ ...dish, priceRange: priceRanges[index] })),
      'priceRange'
    )
  },

  // Сортировка по популярности
  sortByPopularity: () => {
    return arrayUtils.orderBy(sampleDishes, ['likes_count'], ['desc'])
  },

  // Уникальные категории
  getUniqueCategories: (dishes: Dish[]) => {
    const allCategories = dishes.flatMap(dish => dish.categories || [])
    return arrayUtils.uniq(allCategories.map(cat => cat?.name))
  },

  // Разбивка на страницы
  paginateDishes: (pageSize: number = 10) => {
    return arrayUtils.chunk(sampleDishes, pageSize)
  },
}

// Примеры использования objectUtils
export const objectExamples = {
  // Клонирование ресторана для редактирования
  cloneRestaurantForEdit: (restaurant: Restaurant) => {
    return objectUtils.cloneDeep(restaurant)
  },

  // Выбор только публичных полей
  getPublicRestaurantData: (restaurant: Restaurant) => {
    return objectUtils.pick(restaurant, [
      'id', 'name', 'description', 'address', 'phone'
    ])
  },

  // Удаление чувствительных данных
  sanitizeRestaurantData: (restaurant: Restaurant) => {
    return objectUtils.omit(restaurant, [
      'telegram_bot_token', 'settings', 'created_by_admin_id'
    ])
  },

  // Слияние настроек
  mergeRestaurantSettings: (restaurant: Restaurant, newSettings: Record<string, any>) => {
    return objectUtils.merge(restaurant, { settings: newSettings })
  },
}

// Примеры использования stringUtils
export const stringExamples = {
  // Генерация slug для блюда
  generateDishSlug: (dishName: string) => {
    return stringUtils.kebabCase(dishName) // "Том Ям" → "том-ям"
  },

  // Форматирование названий для API
  formatApiProperty: (property: string) => {
    return stringUtils.camelCase(property) // "restaurant_name" → "restaurantName"
  },

  // Форматирование для компонентов
  formatComponentName: (name: string) => {
    return stringUtils.pascalCase(name) // "dish-card" → "DishCard"
  },

  // Обрезка описаний
  truncateDescription: (description: string) => {
    return stringUtils.truncate(description, 100)
  },
}

// Примеры использования restaurantUtils
export const restaurantExamples = {
  // Группировка блюд по категориям
  organizeMenuByCategories: () => {
    return restaurantUtils.groupDishesByCategory(sampleDishes)
  },

  // Сортировка событий по дате
  getUpcomingEvents: () => {
    return restaurantUtils.sortEventsByDate(sampleEvents)
  },

  // Фильтрация доступных блюд
  getAvailableMenu: () => {
    return restaurantUtils.getAvailableDishes(sampleDishes)
  },

  // Расчет средней цены
  getAveragePrice: () => {
    return restaurantUtils.calculateAveragePrice(sampleDishes)
  },
}

// Комплексный пример: подготовка данных для dashboard
export const dashboardDataPreparation = {
  prepareRestaurantDashboard: (restaurant: Restaurant, dishes: Dish[], events: Event[]) => {
    // Группировка блюд по категориям
    const dishesByCategory = restaurantUtils.groupDishesByCategory(dishes)
    
    // Топ популярных блюд
    const popularDishes = arrayUtils.orderBy(dishes, ['likes_count'], ['desc']).slice(0, 5)
    
    // Предстоящие события
    const upcomingEvents = restaurantUtils.sortEventsByDate(
      events.filter(event => new Date(event.start_date) > new Date())
    )
    
    // Статистика
    const stats = {
      totalDishes: dishes.length,
      availableDishes: restaurantUtils.getAvailableDishes(dishes).length,
      averagePrice: restaurantUtils.calculateAveragePrice(dishes),
      totalEvents: events.length,
      totalLikes: dishes.reduce((sum, dish) => sum + dish.likes_count, 0),
    }
    
    // Публичные данные ресторана
    const publicData = objectUtils.pick(restaurant, [
      'id', 'name', 'description', 'address', 'phone'
    ])
    
    return {
      restaurant: publicData,
      dishesByCategory,
      popularDishes,
      upcomingEvents,
      stats,
    }
  },
} 