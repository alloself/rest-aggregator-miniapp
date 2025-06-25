import type { App } from 'vue'
import { createPinia } from 'pinia'
import { setupPrimeVue } from './primevue'

// Основная функция для настройки всех плагинов
export function setupPlugins(app: App): void {
  // Pinia для state management
  const pinia = createPinia()
  app.use(pinia)
  
  // PrimeVue UI библиотека
  setupPrimeVue(app)
}

// Экспорт отдельных плагинов для случаев когда нужна гибкость
export { setupPrimeVue } from './primevue'
