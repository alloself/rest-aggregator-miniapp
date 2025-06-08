<template>
  <div class="enhanced-suspense-wrapper">
    <!-- Enhanced Suspense с минимальным временем показа -->
    <Suspense
      @pending="onPending"
      @resolve="onResolve"
      @fallback="onFallback"
    >
      <!-- Основной контент -->
      <template #default>
        <RestaurantPageContent :slug="slug" />
      </template>
      
      <!-- Fallback с минимальным временем показа -->
      <template #fallback>
        <div v-if="shouldShowFallback">
          <RestaurantLoader 
            :restaurant-name="loaderInfo.name"
            :cuisine-type="loaderInfo.cuisine"
          />
        </div>
      </template>
    </Suspense>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onUnmounted } from 'vue'

// Local components
import RestaurantLoader from './RestaurantLoader.vue'
import RestaurantPageContent from './RestaurantPageContent.vue'

// Props
interface Props {
  slug: string
  minDuration?: number // Минимальная длительность показа лоадера в мс
}

const props = withDefaults(defineProps<Props>(), {
  minDuration: 2500
})

// Состояние
const fallbackStartTime = ref<number | null>(null)
const shouldShowFallback = ref(false)
const isResolved = ref(false)

let hideTimeout: ReturnType<typeof setTimeout> | null = null

// Динамическая информация для лоадера
const loaderInfo = computed(() => {
  const restaurantNames: Record<string, { name: string; cuisine: string }> = {
    'amazing-italian-restaurant': {
      name: 'Amazing Italian',
      cuisine: 'Итальянская кухня'
    },
    'gretto': {
      name: 'GRETTO',
      cuisine: 'Средиземноморская кухня'
    },
    default: {
      name: 'Ресторан',
      cuisine: 'Европейская кухня'
    }
  }
  
  return restaurantNames[props.slug] || restaurantNames.default
})

// Обработчики событий Suspense с минимальным временем показа
const onPending = () => {
  console.log('🔄 Enhanced Suspense: Начинается загрузка')
  // Сбрасываем состояние при новой загрузке
  isResolved.value = false
  // ИСПРАВЛЕНИЕ: Показываем лоадер сразу при pending
  fallbackStartTime.value = Date.now()
  shouldShowFallback.value = true
  if (hideTimeout) {
    clearTimeout(hideTimeout)
    hideTimeout = null
  }
}

const onFallback = () => {
  console.log('⏳ Enhanced Suspense: Fallback активирован')
  // Лоадер уже показан в onPending, просто логируем
  if (!fallbackStartTime.value) {
    fallbackStartTime.value = Date.now()
    shouldShowFallback.value = true
  }
}

const onResolve = () => {
  console.log('✅ Enhanced Suspense: Контент загружен, проверяем минимальное время')
  isResolved.value = true
  
  if (!fallbackStartTime.value) {
    // Если fallback не показывался, сразу показываем контент
    shouldShowFallback.value = false
    return
  }
  
  const elapsed = Date.now() - fallbackStartTime.value
  const remaining = props.minDuration - elapsed
  
  if (remaining > 0) {
    console.log(`⏱️ Enhanced Suspense: Ждем еще ${remaining}мс для минимального времени`)
    hideTimeout = setTimeout(() => {
      shouldShowFallback.value = false
      console.log('✨ Enhanced Suspense: Минимальное время выполнено, показываем контент')
    }, remaining)
  } else {
    console.log('✨ Enhanced Suspense: Минимальное время уже выполнено')
    shouldShowFallback.value = false
  }
}

// Очистка при размонтировании
onUnmounted(() => {
  if (hideTimeout) {
    clearTimeout(hideTimeout)
  }
})
</script>

<style>
/* =============================================================================
   ENHANCED SUSPENSE WRAPPER - БЭМ METHODOLOGY STYLES
   ============================================================================= */

.enhanced-suspense-wrapper {
  width: 100%;
  min-height: 100vh;
}
</style> 