<template>
  <AppCard class="restaurant-card" variant="default">
    <div class="restaurant-card__header">
      <div class="restaurant-card__title-group">
        <h2 class="restaurant-card__title">{{ restaurant.name }}</h2>
        <div v-if="restaurant.rating" class="restaurant-card__rating">
          <i class="pi pi-star-fill"></i>
          <span class="restaurant-card__rating-value">{{ restaurant.rating }}</span>
          <span v-if="restaurant.reviewCount" class="restaurant-card__review-count">
            ({{ restaurant.reviewCount }})
          </span>
        </div>
      </div>
      
      <!-- Favorite button -->
      <AppButton
        v-if="showFavorite"
        variant="ghost"
        size="small"
        @click="toggleFavorite"
        :aria-label="isFavorite ? 'Удалить из избранного' : 'Добавить в избранное'"
      >
        <i :class="[
          'pi',
          isFavorite ? 'pi-heart-fill' : 'pi-heart',
          { 'text-red-500': isFavorite }
        ]"></i>
      </AppButton>
    </div>
    
    <!-- Cuisine type -->
    <div v-if="restaurant.cuisine" class="restaurant-card__cuisine">
      {{ restaurant.cuisine }}
    </div>
    
    <!-- Description -->
    <div v-if="restaurant.description" class="restaurant-card__description">
      {{ restaurant.description }}
    </div>
    
    <!-- Tags -->
    <div v-if="restaurant.tags && restaurant.tags.length" class="restaurant-card__tags">
      <Tag
        v-for="tag in restaurant.tags"
        :key="tag"
        :value="tag"
        severity="secondary"
        rounded
      />
    </div>
    
    <!-- Action buttons -->
    <div class="restaurant-card__actions">
      <AppButton
        variant="primary"
        size="medium"
        @click="openMenu"
      >
        <i class="pi pi-book mr-sm"></i>
        Меню
      </AppButton>
      
      <AppButton
        variant="secondary"
        size="medium"
        @click="openBar"
      >
        <i class="pi pi-glass mr-sm"></i>
        Бар
      </AppButton>
      
      <AppButton
        variant="secondary"
        size="medium"
        @click="openPhotos"
      >
        <i class="pi pi-images mr-sm"></i>
        Фото
      </AppButton>
      
      <AppButton
        variant="accent"
        size="medium"
        full-width
        @click="makeReservation"
        class="restaurant-card__book-btn"
      >
        <i class="pi pi-calendar mr-sm"></i>
        Забронировать
      </AppButton>
    </div>
  </AppCard>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { AppCard, AppButton } from '../../shared/ui'
import Tag from 'primevue/tag'

interface Restaurant {
  id: string
  name: string
  cuisine?: string
  description?: string
  rating?: number
  reviewCount?: number
  tags?: string[]
}

interface RestaurantCardProps {
  restaurant: Restaurant
  showFavorite?: boolean
  favoriteInitial?: boolean
}

const props = withDefaults(defineProps<RestaurantCardProps>(), {
  showFavorite: true,
  favoriteInitial: false
})

const emit = defineEmits<{
  favoriteToggle: [restaurantId: string, isFavorite: boolean]
  openMenu: [restaurantId: string]
  openBar: [restaurantId: string]
  openPhotos: [restaurantId: string]
  makeReservation: [restaurantId: string]
}>()

const isFavorite = ref(props.favoriteInitial)

const toggleFavorite = () => {
  isFavorite.value = !isFavorite.value
  emit('favoriteToggle', props.restaurant.id, isFavorite.value)
}

const openMenu = () => {
  emit('openMenu', props.restaurant.id)
}

const openBar = () => {
  emit('openBar', props.restaurant.id)
}

const openPhotos = () => {
  emit('openPhotos', props.restaurant.id)
}

const makeReservation = () => {
  emit('makeReservation', props.restaurant.id)
}
</script>

<style>
/* Restaurant Card Component - PostCSS Nested */
.restaurant-card {
  padding: var(--spacing-xl);
  background-color: var(--color-white);
  border-radius: var(--border-radius-xl);
  margin-bottom: var(--spacing-xl);

  &__header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--spacing-md);
  }

  &__title-group {
    flex: 1;
  }

  &__title {
    font-size: var(--font-size-2xl);
    font-weight: var(--font-weight-bold);
    color: var(--color-text-primary);
    margin-bottom: var(--spacing-xs);
    line-height: 1.2;
  }

  &__rating {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    font-size: var(--font-size-sm);
    color: var(--color-text-secondary);

    .pi-star-fill {
      color: #ffd700;
      font-size: var(--font-size-sm);
    }
  }

  &__rating-value {
    font-weight: var(--font-weight-medium);
    color: var(--color-text-primary);
  }

  &__review-count {
    color: var(--color-text-secondary);
  }

  &__cuisine {
    font-size: var(--font-size-sm);
    color: var(--color-text-secondary);
    margin-bottom: var(--spacing-md);
    font-style: italic;
  }

  &__description {
    font-size: var(--font-size-base);
    line-height: 1.5;
    color: var(--color-text-primary);
    margin-bottom: var(--spacing-lg);
  }

  &__tags {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-xs);
    margin-bottom: var(--spacing-lg);
  }

  &__actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-sm);

    /* Touch-friendly buttons for Telegram Mini App */
    .btn {
      min-height: var(--touch-target-min);
      font-size: var(--font-size-sm);
      padding: var(--spacing-sm) var(--spacing-md);
    }
  }

  &__book-btn {
    grid-column: 1 / -1;
    margin-top: var(--spacing-sm);
  }
}

/* Цвета для favorite button */
.text-red-500 {
  color: #ef4444;
}

/* Responsive adjustments */
@media (max-width: 375px) {
  .restaurant-card {
    padding: var(--spacing-lg);

    &__title {
      font-size: var(--font-size-xl);
    }

    &__actions {
      grid-template-columns: 1fr;
      gap: var(--spacing-xs);
    }

    &__book-btn {
      margin-top: var(--spacing-xs);
    }
  }
}
</style> 