<template>
  <div class="restaurant-page-content">
    <div v-if="loading" class="restaurant-page-content__loading">
      <div class="loading-spinner">Загрузка...</div>
    </div>

    <div v-else-if="error" class="restaurant-page-content__error">
      {{ error }}
    </div>

    <div v-else-if="restaurant" class="restaurant-page-content__wrapper">
      <RestaurantCard
        :restaurant="restaurant"
        :chef-recommendations="chefRecommendations"
        :contact-info="contactInfo"
        @show-menu="handleShowMenu"
        @show-bar="handleShowBar"
        @show-photos="handleShowPhotos"
      />
    </div>

    <div v-else class="restaurant-page-content__not-found">Ресторан не найден</div>
  </div>
</template>

<script setup lang="ts">
import { onBeforeMount, computed, reactive } from 'vue';
import { storeToRefs } from 'pinia';
import { useRestaurantStore } from '@site/ts/stores/restaurant';
import RestaurantCard from './RestaurantCard.vue';

interface Props {
  slug: string;
}

const { slug } = defineProps<Props>();

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
  // Здесь можно добавить логику открытия меню
};

const handleShowBar = () => {
  console.log('Показать бар');
  // Здесь можно добавить логику открытия барной карты
};

const handleShowPhotos = () => {
  console.log('Показать фото');
  // Здесь можно добавить логику открытия галереи фото
};

onBeforeMount(async () => {
  await store.getRestaurantData(slug);
});
</script>

<style scoped>
.restaurant-page-content {
  min-height: 100vh;
  background-color: #fff;
}

.restaurant-page-content__loading,
.restaurant-page-content__error,
.restaurant-page-content__not-found {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 60vh;
  text-align: center;
  padding: 40px 20px;
}

.restaurant-page-content__error {
  color: #dc3545;
  font-size: 16px;
}

.restaurant-page-content__not-found {
  color: #6c757d;
  font-size: 16px;
}

.loading-spinner {
  font-size: 16px;
  color: #6c757d;
}

.restaurant-page-content__wrapper {
  width: 100%;
}
</style>
