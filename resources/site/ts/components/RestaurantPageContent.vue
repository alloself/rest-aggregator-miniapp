<template>
  <div class="restaurant-page-content">
    <!-- Hero Carousel -->
    <div class="restaurant-hero">
      <RestaurantHeroCarousel 
        :images="heroImages"
        :autoplay="4000"
        height="298px"
      />
    </div>

    <!-- Restaurant Card -->
    <div class="restaurant-card">
      <div class="restaurant-card__content">
        <!-- Restaurant Name and Brief Description -->
        <div class="restaurant-info">
          <h1 class="restaurant-info__title">
            {{ restaurant?.name || 'GRETTO' }}
          </h1>
          <p class="restaurant-info__description">
            {{ restaurant?.description || 'Средиземноморские завтраки и обеды. Греческая кухня.' }}
          </p>
        </div>

        <!-- Restaurant Details Section -->
        <div class="restaurant-details">
          <!-- Average Check -->
          <div class="restaurant-details__item">
            <div class="restaurant-details__icon">
              <MdiIcon name="credit-card-outline" size="sm" color="gray" />
            </div>
            <span class="restaurant-details__text">3000 ₽</span>
          </div>

          <!-- Work Schedule -->
          <div class="restaurant-details__item restaurant-details__item--schedule">
            <div class="restaurant-details__icon">
              <MdiIcon name="clock-outline" size="sm" color="gray" />
            </div>
            <div class="restaurant-details__schedule">
              <div>пн-пт: 14:00 - 23:00</div>
              <div>сб-вс: 14:00 - 23:00</div>
            </div>
          </div>

          <!-- Address -->
          <div class="restaurant-details__item">
            <div class="restaurant-details__icon">
              <MdiIcon name="map-marker-outline" size="sm" color="gray" />
            </div>
            <span class="restaurant-details__text">{{ restaurant?.address || 'Наб. реки Фонтанки, 1' }}</span>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="restaurant-actions">
          <div class="restaurant-actions__buttons">
            <Button 
              label="Меню"
              @click="showMenu"
              class="btn btn--menu"
            />
            <Button 
              label="Бар"
              @click="showBar"
              class="btn btn--bar"
            />
            <Button 
              label="Фото"
              @click="showPhotos"
              class="btn btn--photo"
            />
            <!-- Contact Dropdown -->
            <ContactDropdown 
              phone="+7 (495) 123-45-67"
              telegram-username="gretto_restaurant"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Restaurant Full Description -->
    <div class="restaurant-description">
      <CollapsibleText 
        :text="fullDescription"
        :character-limit="120"
        expand-button-text="еще"
        collapse-button-text="свернуть"
        :animation-duration="300"
      />
    </div>

    <!-- Chef Recommendations -->
    <section class="chef-recommendations">
      <h2 class="chef-recommendations__title">
        Шеф рекомендует
      </h2>
      
      <div class="chef-recommendations__gallery">
        <!-- Featured Large Item -->
        <div class="chef-recommendations__featured">
          <div class="chef-recommendations__featured-image">
            <Image 
              src="/images/chef-recommendations/featured-dish.png"
              alt="Фирменное блюдо"
              class="chef-recommendations__image-featured"
            />
          </div>
        </div>
        
        <!-- Regular Items -->
        <div class="chef-recommendations__item">
          <Image 
            src="/images/chef-recommendations/dish-1.png"
            alt="Рекомендуемое блюдо 1"
            class="chef-recommendations__image"
          />
        </div>
        
        <div class="chef-recommendations__item">
          <Image 
            src="/images/chef-recommendations/dish-2.png"
            alt="Рекомендуемое блюдо 2"
            class="chef-recommendations__image"
          />
        </div>
        
        <div class="chef-recommendations__item">
          <Image 
            src="/images/chef-recommendations/dish-3.png"
            alt="Рекомендуемое блюдо 3"
            class="chef-recommendations__image"
          />
        </div>
        
        <div class="chef-recommendations__item">
          <Image 
            src="/images/chef-recommendations/dish-4.png"
            alt="Рекомендуемое блюдо 4"
            class="chef-recommendations__image"
          />
        </div>
      </div>
    </section>

    <!-- News Feed with Tabs and Infinite Scroll -->
    <NewsFeed :restaurant-slug="slug" />

    <!-- Footer -->
    <div class="restaurant-footer">
      <p class="restaurant-footer__text">By Eat.Drink.Repeat</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { Restaurant } from '@shared/types'
import { useRestaurantStore, useRestaurantDetailsStore } from '@shared/stores/restaurant'

// PrimeVue components
import Card from 'primevue/card'
import Image from 'primevue/image'
import Button from 'primevue/button'

// Shared UI components
import { MdiIcon, RestaurantHeroCarousel } from '@shared/ui'

// Local components
import NewsFeed from './NewsFeed.vue'
import ContactDropdown from './ContactDropdown.vue'
import CollapsibleText from './CollapsibleText.vue'

interface Props {
  slug: string
}

const props = defineProps<Props>()

// Stores
const restaurantStore = useRestaurantStore()
const detailsStore = useRestaurantDetailsStore()

// Data
const restaurant = ref<Restaurant | null>(null)
const fullDescription = ref('Gretto – место, где еда становится образом жизни. Свежие, натуральные ингредиенты с упором на овощи, сыры и дары моря. Место, где еда становится образом жизни. Свежие, натуральные ингредиенты с упором на овощи, сыры и дары моря. Свежие, натуральные ингредиенты с упором на овощи, сыры и дары моря. Здесь готовят с душой и используют только лучшие продукты. Каждое блюдо – это произведение искусства, которое радует не только глаз, но и вкусовые рецепторы.')

// Hero images for carousel
const heroImages = ref([
  {
    url: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=440&h=298&fit=crop&q=80',
    alt: 'Интерьер ресторана'
  },
  {
    url: 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=440&h=298&fit=crop&q=80', 
    alt: 'Зал ресторана'
  },
  {
    url: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=440&h=298&fit=crop&q=80',
    alt: 'Терраса ресторана'
  }
])

// Methods
const showMenu = () => {
  // TODO: Реализовать показ меню
}

const showBar = () => {
  // TODO: Реализовать показ бара
}

const showPhotos = () => {
  // TODO: Реализовать показ фото галереи
}

// Компонент с async setup - автоматически переводит Suspense в состояние loading
console.log(`🚀 RestaurantPageContent: Начинаем загрузку для slug: ${props.slug}`)

// Top-level await - ключевая особенность для Suspense
restaurant.value = await restaurantStore.fetchRestaurant(props.slug)
await detailsStore.fetchChefRecommendations(props.slug)
await detailsStore.fetchNews(props.slug)

console.log(`✅ RestaurantPageContent: Загрузка завершена для ${restaurant.value?.name}`)
</script>

<style>
/* =============================================================================
   RESTAURANT PAGE CONTENT - БЭМ METHODOLOGY STYLES
   ============================================================================= */

.restaurant-page-content {
  background-color: rgba(255, 254, 251, 0.97);
}

/* Restaurant Hero Section */
.restaurant-hero {
  /* Styles handled by RestaurantHeroCarousel component */
}

/* Restaurant Card */
.restaurant-card {
  margin: -36px 16px 0px 16px;
  background-color: #FFFFFF;
  border-radius: 20px;
  box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.15);
  position: relative;
  z-index: 10;
}

.restaurant-card__content {
  padding: 24px;
}

/* Restaurant Info */
.restaurant-info {
  margin-bottom: 24px;
}

.restaurant-info__title {
  font-size: 20px;
  font-weight: 600;
  color: #1C1C1E;
  line-height: 1.3;
  margin-bottom: 12px;
}

.restaurant-info__description {
  font-size: 17px;
  color: #000000;
  line-height: 1.3;
  letter-spacing: -0.03em;
}

/* Restaurant Details */
.restaurant-details {
  margin-bottom: 24px;
}

.restaurant-details__item {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
}

.restaurant-details__item--schedule {
  align-items: flex-start;
}

.restaurant-details__item--schedule .restaurant-details__icon {
  margin-top: 2px;
}

.restaurant-details__icon {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.restaurant-details__text {
  font-size: 15px;
  color: #000000;
  letter-spacing: -0.05em;
}

.restaurant-details__schedule {
  font-size: 15px;
  color: #000000;
  letter-spacing: -0.05em;
  line-height: 1.3;
}

/* MDI SVG Icons */
.mdi-icon {
  width: 100%;
  height: 100%;
  object-fit: contain;
  filter: brightness(0); /* Makes icons black */
}

.restaurant-details__icon .mdi-icon {
  width: 20px;
  height: 20px;
}

.btn__icon .mdi-icon {
  width: 16px;
  height: 16px;
}

/* Restaurant Actions */
.restaurant-actions__buttons {
  display: flex;
  gap: 4px;
}

/* Buttons */
.btn {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
  font-weight: 500 !important;
  font-size: 15px !important;
  letter-spacing: -0.05em !important;
  border: none !important;
  border-radius: 20px !important;
  cursor: pointer;
  transition: all 0.25s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn--menu,
.btn--bar,
.btn--photo {
  flex: 1;
  height: 39px;
  background-color: #F4F3F3;
  color: #000000;
  padding: 10px;
}

.btn--menu:hover,
.btn--bar:hover,
.btn--photo:hover {
  background-color: #E8E8E8;
}

.btn__icon {
  width: 19px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Restaurant Description */
.restaurant-description {
  margin: -8px 16px 24px 15px;
  background-color: rgba(244, 243, 243, 0.68);
  border-radius: 0 0 20px 20px;
  padding: 32px 18px 24px 18px;
  position: relative;
}

/* Override CollapsibleText styles for restaurant description */
.restaurant-description .collapsible-text__text {
  font-size: 15px;
  color: #000000;
  text-align: center;
  line-height: 1.3;
  letter-spacing: -0.03em;
}

.restaurant-description .collapsible-text__toggle-inline {
  font-size: 15px;
  letter-spacing: -0.03em;
}

.restaurant-description .collapsible-text__toggle-collapse {
  font-size: 15px;
  letter-spacing: -0.03em;
}

/* Chef Recommendations */
.chef-recommendations {
  margin-bottom: 24px;
  padding: 0;
}

.chef-recommendations__title {
  font-size: 18px;
  font-weight: 600;
  color: #000000;
  margin-bottom: 16px;
  padding: 0 16px;
  letter-spacing: -0.04em;
}

.chef-recommendations__gallery {
  display: flex;
  gap: 4px;
  overflow-x: auto;
  padding: 0 16px 8px 0; /* Добавляем padding справа чтобы последний элемент не прижимался к краю */
}

.chef-recommendations__gallery::-webkit-scrollbar {
  display: none;
}

.chef-recommendations__featured {
  flex-shrink: 0;
  width: 90px;
  height: 100px;
  background-color: rgba(255, 251, 233, 0.68);
  border-radius: 0 30px 30px 0; /* Исправлено согласно макету */
  padding: 20px 21px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.chef-recommendations__featured-image {
  width: 52px;
  height: 52px;
  border-radius: 0; /* Убираем закругление для featured image */
  overflow: hidden;
}

.chef-recommendations__item {
  flex-shrink: 0;
  width: 100px;
  height: 100px;
  border-radius: 20px;
  overflow: hidden;
  cursor: pointer;
}

.chef-recommendations__image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  border-radius: 20px;
}

.chef-recommendations__image-featured img {
  max-width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  border-radius: 0; /* Без закругления для featured изображения */
}

/* Footer */
.restaurant-footer {
  text-align: center;
  padding: 24px 0;
}

.restaurant-footer__text {
  font-size: 16px;
  font-weight: 500;
  color: rgba(0, 0, 0, 0.35);
  line-height: 1.3;
}

/* PrimeVue Overrides */
.p-card {
  border: none;
  box-shadow: none;
}

.p-card-content {
  padding: 0;
}

.p-button {
  border: none;
}

.p-image-preview-indicator {
  display: none;
}

/* Maximum Specificity Button Overrides - Force PrimeVue Override */
.restaurant-page-content .restaurant-actions__buttons .btn.p-button.p-component {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
  font-weight: 500 !important;
  font-size: 15px !important;
  letter-spacing: -0.05em !important;
  border-radius: 20px !important;
  border: none !important;
  box-shadow: none !important;
  transition: all 0.25s ease !important;
}

.restaurant-page-content .restaurant-actions__buttons .btn.p-button.p-component.btn--menu,
.restaurant-page-content .restaurant-actions__buttons .btn.p-button.p-component.btn--bar, 
.restaurant-page-content .restaurant-actions__buttons .btn.p-button.p-component.btn--photo {
  background: #F4F3F3 !important;
  background-color: #F4F3F3 !important;
  color: #000000 !important;
  border: none !important;
  border-radius: 20px !important;
}

.restaurant-page-content .restaurant-actions__buttons .btn.p-button.p-component.btn--menu:hover,
.restaurant-page-content .restaurant-actions__buttons .btn.p-button.p-component.btn--bar:hover,
.restaurant-page-content .restaurant-actions__buttons .btn.p-button.p-component.btn--photo:hover,
.restaurant-page-content .restaurant-actions__buttons .btn.p-button.p-component.btn--menu:focus,
.restaurant-page-content .restaurant-actions__buttons .btn.p-button.p-component.btn--bar:focus,
.restaurant-page-content .restaurant-actions__buttons .btn.p-button.p-component.btn--photo:focus,
.restaurant-page-content .restaurant-actions__buttons .btn.p-button.p-component.btn--menu:active,
.restaurant-page-content .restaurant-actions__buttons .btn.p-button.p-component.btn--bar:active,
.restaurant-page-content .restaurant-actions__buttons .btn.p-button.p-component.btn--photo:active {
  background: #E8E8E8 !important;
  background-color: #E8E8E8 !important;
  color: #000000 !important;
  box-shadow: none !important;
  outline: none !important;
}

/* Additional PrimeVue reset for buttons */
.restaurant-page-content .p-button.p-component {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
}

/* Remove PrimeVue button focus styles globally for this page */
.restaurant-page-content .p-button:focus {
  box-shadow: none !important;
  outline: none !important;
}

.restaurant-page-content .p-button:active {
  transform: none !important;
}
</style> 