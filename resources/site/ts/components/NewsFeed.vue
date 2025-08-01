<template>
  <section class="news-feed">
    <!-- Category Filter -->
    <CategoryFilter
      :categories="categories"
      :active-category="activeCategory"
      :include-all-option="true"
      :all-option-label="'Все'"
      :all-option-icon="'pi-apps'"
      :show-counts="false"
      @category-change="onCategoryChange"
    />

    <!-- News List with Infinite Scroll -->
    <div 
      class="news-feed__content"
      ref="scrollContainer"
    >
      <div 
        v-for="item in newsItems"
        :key="item.id"
        class="news-feed__item"
      >
        <Card class="news-card">
          <template #content>
            <div class="news-card__content">
              <!-- News Image -->
              <div class="news-card__image-container">
                <Image 
                  :src="item.image"
                  :alt="item.title"
                  class="news-card__image"
                />
              </div>
              
              <!-- News Content -->
              <div class="news-card__text">
                <h3 class="news-card__title">
                  {{ item.title }}
                </h3>
                
                <!-- Description with collapse/expand -->
                <div class="news-card__description-container">
                  <CollapsibleText 
                    :text="item.description"
                    :character-limit="120"
                    expand-button-text="еще"
                    collapse-button-text="свернуть"
                    :animation-duration="300"
                  />
                </div>
              </div>
            </div>
          </template>
        </Card>
      </div>

      <!-- Loading Indicator -->
      <div 
        v-if="isLoading" 
        class="news-feed__loading"
      >
        <div class="news-feed__spinner"></div>
      </div>

      <!-- End Message -->
      <div 
        v-if="hasReachedEnd && newsItems.length > 0" 
        class="news-feed__end-message"
      >
        Все новости загружены
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import Card from 'primevue/card'
import Image from 'primevue/image'
import CollapsibleText from './CollapsibleText.vue'
import CategoryFilter from '../features/category-filter/CategoryFilter.vue'

// Types
interface NewsItem {
  id: string
  title: string
  description: string
  image: string
  category: string
  publishedAt: string
}

interface Category {
  id: string
  label: string
  icon?: string
}

// Props
interface Props {
  restaurantSlug?: string
}

const props = withDefaults(defineProps<Props>(), {
  restaurantSlug: ''
})

// Reactive data
const scrollContainer = ref<HTMLElement>()
const activeCategory = ref('all')
const newsItems = ref<NewsItem[]>([])
const isLoading = ref(false)
const hasReachedEnd = ref(false)
const currentPage = ref(1)
const itemsPerPage = 10

// Categories configuration
const categories: Category[] = [
  { id: 'news', label: 'Новости', icon: 'pi-megaphone' },
  { id: 'events', label: 'События', icon: 'pi-calendar' },
  { id: 'offers', label: 'Предложения', icon: 'pi-percentage' }
]

// Mock data
const mockNewsItems: NewsItem[] = [
  {
    id: '1',
    title: 'Новый взгляд на классику 🍊🍸',
    description: 'Встречайте наш авторский Negroni Twist — коктейль с характером, где горечь переплетается с пряной глубиной, а классика обретает новое лицо... еще',
    image: '/images/news/news-1.png',
    category: 'news',
    publishedAt: '2025-06-08'
  },
  {
    id: '2',
    title: 'French Bubble от Charlie 🍸',
    description: 'French Bubble от Charlie — элегантное игристое из Бургундии, созданное из 100% Шардоне. Идеальное вино в качестве аперитива, а так же к вашему ужину... еще',
    image: 'https://images.unsplash.com/photo-1547573854-74d2a71d0826?w=410&h=238&fit=crop&q=80',
    category: 'news',
    publishedAt: '2025-06-07'
  },
  {
    id: '3',
    title: 'Джаз вечер в Gretto',
    description: 'Приглашаем на уютный джаз вечер с живой музыкой и авторскими коктейлями. Каждую пятницу с 19:00',
    image: 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=410&h=238&fit=crop&q=80',
    category: 'events',
    publishedAt: '2025-06-06'
  },
  {
    id: '4',
    title: 'Счастливые часы 🍹',
    description: 'Скидка 30% на все коктейли с 17:00 до 19:00 по будням. Приходите после работы и расслабьтесь!',
    image: 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?w=410&h=238&fit=crop&q=80',
    category: 'offers',
    publishedAt: '2025-06-05'
  }
]

// Computed
const filteredItems = computed(() => {
  if (activeCategory.value === 'all') {
    return mockNewsItems
  }
  return mockNewsItems.filter(item => item.category === activeCategory.value)
})

// Methods
const onCategoryChange = async (categoryId: string) => {
  activeCategory.value = categoryId
  // Reset pagination
  currentPage.value = 1
  hasReachedEnd.value = false
  newsItems.value = []

  await nextTick()
  loadMoreItems()
}

const loadMoreItems = () => {
  if (isLoading.value || hasReachedEnd.value) return

  isLoading.value = true

  // Simulate API call
  setTimeout(() => {
    const startIndex = (currentPage.value - 1) * itemsPerPage
    const endIndex = startIndex + itemsPerPage
    const newItems = filteredItems.value.slice(startIndex, endIndex)

    if (newItems.length === 0) {
      hasReachedEnd.value = true
    } else {
      newsItems.value.push(...newItems)
      currentPage.value++
    }

    isLoading.value = false
  }, 500)
}

const handleScroll = () => {
  const { scrollTop, scrollHeight, clientHeight } = document.documentElement

  // Load more when near bottom (200px threshold)
  if (scrollHeight - scrollTop <= clientHeight + 200) {
    loadMoreItems()
  }
}

// Lifecycle
onMounted(() => {
  loadMoreItems()
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
/* News Feed Component */
.news-feed {
  margin-bottom: 24px;
}

.news-feed__content {
  padding: 0 15px;
}

.news-feed__item {
  margin-bottom: 16px;
}

.news-feed__item:last-child {
  margin-bottom: 0;
}

.news-feed__loading {
  display: flex;
  justify-content: center;
  padding: 20px;
}

.news-feed__spinner {
  width: 24px;
  height: 24px;
  border: 2px solid rgba(0, 0, 0, 0.1);
  border-left: 2px solid #000000;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.news-feed__end-message {
  text-align: center;
  padding: 20px;
  color: rgba(0, 0, 0, 0.5);
  font-size: 14px;
}

/* News Card (соответствует макету Figma) */
.news-card {
  background-color: #FFFFFF;
  border-radius: 20px;
  box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  border: none;
  width: 100%;
  max-width: 100%; /* Убираем фиксированную ширину чтобы карточка не выходила за край */
}

.news-card__content {
  position: relative;
  padding: 0;
}

.news-card__image-container {
  width: 100%;
  height: 238px;
  overflow: hidden;
  border-radius: 20px 20px 0 0;
}

.news-card__image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.news-card__text {
  padding: 18px;
  display: flex;
  flex-direction: column;
  gap: 11px;
}

.news-card__title {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  font-size: 16px;
  font-weight: 500;
  color: #000000;
  line-height: 0.95;
  letter-spacing: -0.01em;
  margin: 0;
}

/* Override CollapsibleText styles for news cards */
.news-card__description-container .collapsible-text__text {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  font-size: 14px;
  font-weight: 400;
  color: #000000;
  line-height: 1.1;
  margin: 0;
}

.news-card__description-container .collapsible-text__toggle-inline {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  font-size: 14px;
  font-weight: 400;
}

.news-card__description-container .collapsible-text__toggle-collapse {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  font-size: 14px;
  font-weight: 400;
}

.news-card__description-container .collapsible-text__collapse-container {
  margin-top: 8px;
}

/* Animations */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* PrimeVue Overrides */
:deep(.p-card) {
  border: none;
  background: transparent;
}

:deep(.p-card-content) {
  padding: 0;
}

:deep(.p-card-body) {
  padding: 0;
}

/* Восстанавливаем тень для news-card */
:deep(.news-card .p-card) {
  background-color: #FFFFFF;
  border-radius: 20px;
  box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.15);
}
</style> 