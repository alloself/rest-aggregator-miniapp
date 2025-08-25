// Главный файл экспорта всех TypeScript типов
// Автоматически генерируется командой generate:all-types

// Экспорт типов моделей
export * from './models'

// Экспорт типов API ресурсов
export * from './resources'

// Часто используемые типы для удобства
export type {
  RestaurantResourceData,
  CategoryResourceData,
  CategoryPivot,
  FileResourceData,
  DishResourceData,
  NewsResourceData,
  EventResourceData,
  UserResourceData,
  WorkingHours
} from './resources'
