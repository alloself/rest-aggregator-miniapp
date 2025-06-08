import { ref, computed } from 'vue'

interface LoaderState {
  isVisible: boolean
  restaurantName: string
  cuisineType: string
  progress: number
  showStartTime: number | null
}

// Минимальное время показа лоадера в миллисекундах
const MIN_LOADER_DURATION = 250

export const useRestaurantLoader = () => {
  // Создаем локальное состояние для каждого экземпляра
  const loaderState = ref<LoaderState>({
    isVisible: false, // Изначально скрыт, будет показан при вызове initializeLoader
    restaurantName: 'GRETTO',
    cuisineType: 'Средиземноморская кухня',
    progress: 0,
    showStartTime: null // Будет установлено при инициализации
  })

  // Показать лоадер
  const showLoader = (restaurantName?: string, cuisineType?: string) => {
    loaderState.value = {
      isVisible: true,
      restaurantName: restaurantName || 'GRETTO',
      cuisineType: cuisineType || 'Средиземноморская кухня',
      progress: 0,
      showStartTime: Date.now()
    }
  }

  // Инициализация лоадера - сбрасываем время показа
  const initializeLoader = () => {
    loaderState.value.showStartTime = Date.now()
    loaderState.value.isVisible = true
    loaderState.value.progress = 0
  }

  // Скрыть лоадер с учетом минимального времени показа
  const hideLoader = async (): Promise<void> => {
    if (!loaderState.value.showStartTime) {
      loaderState.value.isVisible = false
      return
    }

    const elapsedTime = Date.now() - loaderState.value.showStartTime
    const remainingTime = MIN_LOADER_DURATION - elapsedTime

    if (remainingTime > 0) {
      // Ждем оставшееся время для минимальной продолжительности
      await new Promise(resolve => setTimeout(resolve, remainingTime))
    }

    loaderState.value.isVisible = false
    loaderState.value.showStartTime = null
  }

  // Сброс состояния лоадера
  const resetLoader = () => {
    loaderState.value = {
      isVisible: true,
      restaurantName: 'GRETTO',
      cuisineType: 'Средиземноморская кухня',
      progress: 0,
      showStartTime: Date.now()
    }
  }

  // Обновить прогресс загрузки
  const updateProgress = (progress: number) => {
    loaderState.value.progress = Math.max(0, Math.min(100, progress))
  }

  // Обновить информацию о ресторане
  const updateRestaurantInfo = (name: string, cuisine: string) => {
    loaderState.value.restaurantName = name
    loaderState.value.cuisineType = cuisine
  }

  // Обертка для выполнения операции с минимальным временем показа лоадера
  const ensureMinimumDuration = async <T>(operation: () => Promise<T>): Promise<T> => {
    const result = await operation()
    await hideLoader()
    return result
  }

  // Симуляция загрузки с прогрессом
  const simulateLoading = async (duration: number = 2000): Promise<void> => {
    return new Promise((resolve) => {
      const steps = 20
      const stepDuration = duration / steps
      let currentStep = 0

      const interval = setInterval(async () => {
        currentStep++
        const progress = (currentStep / steps) * 100
        updateProgress(progress)

        if (currentStep >= steps) {
          clearInterval(interval)
          await hideLoader()
          resolve()
        }
      }, stepDuration)
    })
  }

  // Computed properties
  const isVisible = computed(() => loaderState.value.isVisible)
  const restaurantName = computed(() => loaderState.value.restaurantName)
  const cuisineType = computed(() => loaderState.value.cuisineType)
  const progress = computed(() => loaderState.value.progress)

  return {
    // State
    isVisible,
    restaurantName,
    cuisineType,
    progress,
    
    // Methods
    showLoader,
    hideLoader,
    resetLoader,
    updateProgress,
    updateRestaurantInfo,
    simulateLoading,
    ensureMinimumDuration,
    initializeLoader
  }
} 