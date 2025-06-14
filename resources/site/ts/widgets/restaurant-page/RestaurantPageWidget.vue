<template>
  <div class="restaurant-page-widget">
    <!-- Hero Section -->
    <section class="container">
      <RestaurantHero 
        :images="heroImages"
        :auto-slide="true"
        :auto-slide-interval="5000"
        @image-change="handleImageChange"
      />
    </section>
    
    <!-- Restaurant Info Section -->
    <section class="container">
      <RestaurantCard
        :restaurant="restaurant"
        :show-favorite="true"
        :favorite-initial="false"
        @favorite-toggle="(restaurantId, isFavorite) => $emit('favoriteToggle', restaurantId, isFavorite)"
        @open-menu="(restaurantId) => $emit('menuAction', 'menu', restaurantId)"
        @open-bar="(restaurantId) => $emit('menuAction', 'bar', restaurantId)"  
        @open-photos="(restaurantId) => $emit('menuAction', 'photos', restaurantId)"
        @make-reservation="(restaurantId) => $emit('menuAction', 'reservation', restaurantId)"
      />
    </section>
    
    <!-- Chef Recommendations Section -->
    <section class="chef-recommendations">
      <h3 class="chef-recommendations__title">Рекомендации шефа</h3>
      <div class="chef-recommendations__gallery">
        <div 
          v-for="dish in chefRecommendations"
          :key="dish.id"
          class="chef-recommendations__item"
        >
          <AppImage
            :src="dish.image"
            :alt="dish.name"
            width="120"
            height="120"
            object-fit="cover"
            border-radius="lg"
          />
        </div>
      </div>
    </section>
    
    <!-- Category Filter Section -->
    <section>
      <CategoryFilter
        :categories="categories"
        :active-category="activeCategory"
        :include-all-option="true"
        :all-option-label="'Все'"
        :all-option-icon="'pi-apps'"
        :show-counts="false"
        @category-change="(categoryId, category) => $emit('categoryChange', categoryId, category)"
      />
    </section>
    
    <!-- Content Section -->
    <section class="container">
      <div class="content-list">
        <NewsCard
          v-for="item in filteredContent"
          :key="item.id"
          :news-item="item"
        />
      </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
      <div class="footer__text">
        Работаем на платформе Restaurant Aggregator
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { computed, reactive } from 'vue'
import RestaurantHero from '../../features/restaurant-hero/RestaurantHero.vue'
import RestaurantCard from '../../features/restaurant-info/RestaurantCard.vue'
import CategoryFilter from '../../features/category-filter/CategoryFilter.vue'
import { AppImage } from '../../shared/ui'

// Простой компонент новостей для демонстрации
import NewsCard from './NewsCard.vue'

interface RestaurantImage {
  src: string
  alt: string
}

interface Restaurant {
  id: string
  name: string
  cuisine?: string
  description?: string
  rating?: number
  reviewCount?: number
  tags?: string[]
}

interface Category {
  id: string
  label: string
  icon?: string
  count?: number
}

interface NewsItem {
  id: string
  title: string
  excerpt: string
  image?: string
  date: string
  category: string
}

interface RestaurantPageWidgetProps {
  restaurant: Restaurant
  heroImages: RestaurantImage[]
  categories: Category[]
  activeCategory: string
}

const props = defineProps<RestaurantPageWidgetProps>()

const emit = defineEmits<{
  categoryChange: [categoryId: string, category: Category]
  favoriteToggle: [restaurantId: string, isFavorite: boolean]
  menuAction: [action: string, restaurantId: string]
}>()

// Mock data для рекомендаций шефа
const chefRecommendations = reactive([
  {
    id: 'dish-1',
    name: 'Стейк рибай',
    image: 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=240&h=240&fit=crop'
  },
  {
    id: 'dish-2', 
    name: 'Паста карбонара',
    image: 'https://images.unsplash.com/photo-1621996346565-e3dbc353d2e5?w=240&h=240&fit=crop'
  },
  {
    id: 'dish-3',
    name: 'Тирамису',
    image: 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=240&h=240&fit=crop'
  }
])

// Mock данные для контента
const mockContent = reactive([
  {
    id: 'news-1',
    title: 'Новое сезонное меню',
    excerpt: 'Представляем обновленное меню с блюдами из сезонных продуктов',
    image: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=200&fit=crop',
    date: '2025-01-15',
    category: 'news'
  },
  {
    id: 'event-1',
    title: 'Винный вечер с сомелье',
    excerpt: 'Приглашаем на дегустацию французских вин с экспертом',
    image: 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400&h=200&fit=crop',
    date: '2025-01-20',
    category: 'events'
  },
  {
    id: 'offer-1',
    title: 'Скидка 20% на бизнес-ланчи',
    excerpt: 'Специальное предложение для деловых встреч в будние дни',
    image: 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=400&h=200&fit=crop',
    date: '2025-01-18',
    category: 'offers'
  }
])

const filteredContent = computed(() => {
  if (props.activeCategory === 'all') {
    return mockContent
  }
  return mockContent.filter(item => item.category === props.activeCategory)
})

const handleImageChange = (index: number, image: RestaurantImage) => {
}
</script>

<style>
/* Restaurant Page Widget Component - Standard CSS */
.restaurant-page-widget {
  width: 100%;
  background-color: #FFFFFF;
}

.chef-recommendations {
  margin-bottom: 24px;
}

.chef-recommendations__title {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 16px;
  padding: 0 16px;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  color: #000000;
  letter-spacing: -0.04em;
}

.chef-recommendations__gallery {
  display: flex;
  gap: 4px;
  overflow-x: auto;
  padding: 0 16px;
  -webkit-overflow-scrolling: touch;
}

.chef-recommendations__gallery::-webkit-scrollbar {
  display: none;
}

.chef-recommendations__item {
  flex: 0 0 auto;
  width: 120px;
  height: 120px;
  border-radius: 20px;
  overflow: hidden;
}

.content-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.footer {
  padding: 32px 16px;
  background-color: #F4F3F3;
  margin-top: 40px;
  text-align: center;
}

.footer__text {
  font-size: 14px;
  color: rgba(0, 0, 0, 0.5);
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Responsive adjustments */
@media (max-width: 375px) {
  .chef-recommendations__item {
    width: 100px;
    height: 100px;
  }
}
</style> 