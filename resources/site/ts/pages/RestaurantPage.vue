<template>
  <div class="restaurant-page">
    <div v-if="loading" class="restaurant-page__loading">
      <div class="loading-spinner">Загрузка...</div>
    </div>

    <div v-else-if="error" class="restaurant-page__error">
      {{ error }}
    </div>

    <div v-else-if="restaurant" class="restaurant-page__wrapper">
      <RestaurantCard
        :restaurant="restaurant"
        :chef-recommendations="chefRecommendations"
        :contact-info="contactInfo"
        @show-menu="handleShowMenu"
        @show-bar="handleShowBar"
        @show-photos="handleShowPhotos"
      />
    </div>

    <div v-else class="restaurant-page__not-found">Ресторан не найден</div>
  </div>
</template>

<script setup lang="ts">
import { onBeforeMount, computed, reactive } from 'vue';
import { useRoute } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useRestaurantStore } from '../entities/restaurant';
import RestaurantCard from '../widgets/restaurant-page/RestaurantCard.vue';

const route = useRoute();
const slug = computed(() => (typeof route.params.slug === 'string' ? route.params.slug : ''));

const store = useRestaurantStore();
const { restaurant, loading, error } = storeToRefs(store);

const chefRecommendations = reactive([
  {
    id: 'dish-1',
    name: 'Стейк рибай',
    image: 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=240&h=240&fit=crop',
  },
  {
    id: 'dish-2',
    name: 'Паста карбонара',
    image: 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=240&h=240&fit=crop',
  },
  {
    id: 'dish-3',
    name: 'Тирамису',
    image: 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=240&h=240&fit=crop',
  },
  {
    id: 'dish-4',
    name: 'Салат Цезарь',
    image: 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=240&h=240&fit=crop',
  },
  {
    id: 'dish-5',
    name: 'Суп грибной',
    image: 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=240&h=240&fit=crop',
  },
]);

const contactInfo = computed(() => ({
  phone: '+7 (495) 123-45-67',
  telegram: 'restaurant_bot',
}));

const handleShowMenu = () => {
  console.log('Показать меню');
};

const handleShowBar = () => {
  console.log('Показать бар');
};

const handleShowPhotos = () => {
  console.log('Показать фото');
};

onBeforeMount(async () => {
  if (slug.value) {
    await store.getRestaurantData(slug.value);
  }
});
</script>

<style>
.restaurant-page {
  width: 100%;
  margin: 0 auto;
  background-color: rgba(255, 254, 251, 0.97);
  min-height: 100vh;
  overflow-x: hidden;
}

.restaurant-page__loading,
.restaurant-page__error,
.restaurant-page__not-found {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 60vh;
  text-align: center;
  padding: 40px 20px;
}

.restaurant-page__error {
  color: #dc3545;
  font-size: 16px;
}

.restaurant-page__not-found {
  color: #6c757d;
  font-size: 16px;
}

.loading-spinner {
  font-size: 16px;
  color: #6c757d;
}

.restaurant-page__wrapper {
  width: 100%;
}
</style>
