<template>
  <div class="home-page">
    <!-- Header с поиском -->
    <section class="home-page__header">
      <div class="home-page__search">
        <h1 class="home-page__title">Рестораны</h1>
        <Button
          @click="navigation.goToSearch()"
          icon="magnify"
          text
          rounded
          class="home-page__search-btn"
          aria-label="Поиск ресторанов"
        />
      </div>
      
      <!-- Фильтры категорий -->
      <CategoryFilter
        :categories="restaurantCategories"
        :active-category="filters.category || 'all'"
        :include-all-option="true"
        :all-option-label="'Все рестораны'"
        :all-option-icon="'pi-apps'"
        :show-counts="true"
        @category-change="onCategoryChange"
      />
    </section>

    <!-- Список ресторанов -->
    <section class="home-page__restaurants">
      <div class="home-page__loading" v-if="loading">
        <div class="home-page__loading-text">Загружаем рестораны...</div>
        <div class="home-page__skeleton">
          <div 
            v-for="n in 6" 
            :key="n" 
            class="home-page__skeleton-item"
          />
        </div>
      </div>

      <div class="home-page__error" v-else-if="error">
        <h3>Ошибка загрузки</h3>
        <p>{{ error }}</p>
        <Button @click="loadRestaurants" label="Попробовать снова" />
      </div>

      <div class="home-page__content" v-else>
        <!-- Сортировка -->
        <div class="home-page__controls">
          <div class="home-page__sort">
            <label>Сортировка:</label>
            <select 
              v-model="filters.sort_by" 
              @change="onSortChange"
              class="home-page__sort-select"
            >
              <option value="rating">По рейтингу</option>
              <option value="name">По названию</option>
              <option value="created_at">Новые</option>
            </select>
          </div>
          
          <div class="home-page__results">
            {{ pagination.total }} ресторанов
          </div>
        </div>

        <!-- Сетка ресторанов -->
        <div class="home-page__grid">
          <RestaurantCard
            v-for="restaurant in restaurants"
            :key="restaurant.id"
            :restaurant="restaurant"
            @click="navigation.goToRestaurant(restaurant.id)"
            @favorite="onToggleFavorite(restaurant)"
            class="home-page__restaurant-card"
          />
        </div>

        <!-- Пагинация -->
        <div class="home-page__pagination" v-if="pagination.last_page > 1">
          <Button
            v-if="pagination.current_page > 1"
            @click="loadPage(pagination.current_page - 1)"
            icon="chevron-left"
            text
            label="Назад"
          />
          
          <span class="home-page__page-info">
            {{ pagination.current_page }} из {{ pagination.last_page }}
          </span>
          
          <Button
            v-if="pagination.current_page < pagination.last_page"
            @click="loadPage(pagination.current_page + 1)"
            icon="chevron-right"
            text
            label="Далее"
            icon-pos="right"
          />
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, watch, inject } from 'vue'
import { useRestaurantApi, type RestaurantFilters } from '../api/restaurants'
import { navigation } from '../router'
import type { Restaurant } from '../../shared/types/models'

// Components
import CategoryFilter from '../features/category-filter/CategoryFilter.vue'
import RestaurantCard from '../features/restaurant-info/RestaurantCard.vue'

// Состояние компонента
const loading = ref(true)
const error = ref<string | null>(null)
const restaurants = ref<Restaurant[]>([])
const restaurantCategories = ref([
  { id: '1', label: 'Итальянская', count: 12 },
  { id: '2', label: 'Японская', count: 8 },
  { id: '3', label: 'Европейская', count: 15 },
  { id: '4', label: 'Кафе', count: 20 }
])

// Фильтры и пагинация
const filters = reactive<RestaurantFilters>({
  category: 'all',
  search: '',
  min_rating: undefined,
  sort_by: 'rating',
  sort_order: 'desc',
  per_page: 12,
  page: 1
})

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 12,
  total: 0
})

// API сервис
const restaurantApi = useRestaurantApi()

// Telegram Web App
const telegramWebApp = inject('telegramWebApp', null)

/**
 * Загрузить рестораны
 */
async function loadRestaurants() {
  try {
    loading.value = true
    error.value = null

    const response = await restaurantApi.getRestaurants(filters)
    
    restaurants.value = response.data
    Object.assign(pagination, response.pagination)

  } catch (err) {
    error.value = 'Не удалось загрузить рестораны. Проверьте соединение.'
  } finally {
    loading.value = false
  }
}

/**
 * Загрузить конкретную страницу
 */
async function loadPage(page: number) {
  filters.page = page
  await loadRestaurants()
}

/**
 * Обработка смены категории
 */
function onCategoryChange(categoryId: string) {
  filters.category = categoryId === 'all' ? '' : categoryId
  filters.page = 1
  loadRestaurants()
}

/**
 * Обработка смены сортировки
 */
function onSortChange() {
  filters.page = 1
  loadRestaurants()
}

/**
 * Добавить/убрать из избранного
 */
async function onToggleFavorite(restaurant: Restaurant) {
  try {
    const response = await restaurantApi.toggleFavorite(restaurant.id)
    
    // Обновляем локальное состояние
    restaurant.is_favorite = response.is_favorite
    
    // Показываем уведомление в Telegram
    if (telegramWebApp) {
      telegramWebApp.showAlert(response.message)
    }
  } catch (err) {
    if (telegramWebApp) {
      telegramWebApp.showAlert('Ошибка при добавлении в избранное')
    }
  }
}

// Lifecycle hooks
onMounted(() => {
  loadRestaurants()
  
  // Настройка Telegram Web App для главной страницы
  if (telegramWebApp) {
    telegramWebApp.ready()
    telegramWebApp.expand()
    telegramWebApp.BackButton.hide()
    telegramWebApp.MainButton.hide()
  }
})

// Watchers
watch(() => filters.category, () => {
})
</script>

<style>
.home-page {
  min-height: 100vh;
  background-color: var(--color-background);

  &__header {
    padding: var(--spacing-lg) var(--spacing-md);
    background-color: var(--color-white);
    border-bottom: 1px solid var(--color-border);
    position: sticky;
    top: 0;
    z-index: 10;
  }

  &__search {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-md);
  }

  &__title {
    font-size: var(--font-size-2xl);
    font-weight: var(--font-weight-bold);
    color: var(--color-text-primary);
    margin: 0;
  }

  &__search-btn {
    color: var(--color-text-secondary);
  }

  &__restaurants {
    padding: var(--spacing-md);
  }

  &__loading {
    text-align: center;
    padding: var(--spacing-xl) 0;
  }

  &__loading-text {
    font-size: var(--font-size-lg);
    color: var(--color-text-secondary);
    margin-bottom: var(--spacing-lg);
  }

  &__skeleton {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--spacing-md);
  }

  &__skeleton-item {
    height: 280px;
    background: linear-gradient(
      90deg,
      var(--color-gray-200) 25%,
      var(--color-gray-100) 50%,
      var(--color-gray-200) 75%
    );
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
    border-radius: var(--border-radius-lg);
  }

  &__error {
    text-align: center;
    padding: var(--spacing-xl);
    
    h3 {
      color: var(--color-danger);
      margin-bottom: var(--spacing-md);
    }

    p {
      color: var(--color-text-secondary);
      margin-bottom: var(--spacing-lg);
    }
  }

  &__controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-lg);
    padding: var(--spacing-sm) 0;
  }

  &__sort {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    
    label {
      font-size: var(--font-size-sm);
      color: var(--color-text-secondary);
    }
  }

  &__sort-select {
    padding: var(--spacing-xs) var(--spacing-sm);
    border: 1px solid var(--color-border);
    border-radius: var(--border-radius-md);
    background-color: var(--color-white);
    font-size: var(--font-size-sm);
    color: var(--color-text-primary);
  }

  &__results {
    font-size: var(--font-size-sm);
    color: var(--color-text-secondary);
  }

  &__grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--spacing-lg);
  }

  &__restaurant-card {
    cursor: pointer;
    transition: transform 0.2s ease;

    &:hover {
      transform: translateY(-2px);
    }

    &:active {
      transform: translateY(0);
    }
  }

  &__pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-lg);
    margin-top: var(--spacing-xl);
    padding: var(--spacing-lg) 0;
  }

  &__page-info {
    font-size: var(--font-size-sm);
    color: var(--color-text-secondary);
    font-weight: var(--font-weight-medium);
  }
}

@keyframes skeleton-loading {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

/* Responsive для больших экранов */
@media (min-width: 768px) {
  .home-page__grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .home-page__grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
</style> 