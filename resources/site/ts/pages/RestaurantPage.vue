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
import { onBeforeMount, computed } from 'vue';
import { useRoute } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useRestaurantStore } from '../entities/restaurant';
import RestaurantCard from '../widgets/restaurant-page/RestaurantCard.vue';
import PhotoReels from '@/shared/components/PhotoReels.vue';
import { useBottomSheet } from '../shared';
import { PdfBottomSheet } from '../shared/ui';
import { client } from '../shared/api/axios';
import type { User, Restaurant } from '../../../shared/types/models';
import { useMiniAppStore } from '../shared/stores/miniapp';

const { slug } = defineProps<{ slug?: string }>();

const route = useRoute();

const { open } = useBottomSheet();

const store = useRestaurantStore();
const { restaurant, loading, error } = storeToRefs(store);
const mini = useMiniAppStore();

const chefRecommendations = computed(() => {
  const categories = restaurant.value?.categories ?? [];
  const chief = categories.find((c) => c?.pivot?.key === 'chief_recommendations');
  return Array.isArray(chief?.dishes) ? chief!.dishes : [];
});

const contactInfo = computed(() => ({
  phone: '+7 (495) 123-45-67',
  telegram: 'restaurant_bot',
}));

const galleryImages = computed(() => {
  const images = restaurant.value?.images ?? [];
  return images.filter((img) => img?.pivot?.key === 'gallery');
});

const findPdfByKey = (key: 'menu' | 'bar') => {
  const files = restaurant.value?.files || [];
  return files.find((f) => f?.pivot?.key === key && f.extension.toLowerCase() === 'pdf');
};

const handleShowMenu = () => {
  const pdf = findPdfByKey('menu');
  if (!pdf) return;
  open(
    PdfBottomSheet,
    { url: pdf.url, title: 'Меню' },
    {
      showHandle: true,
      height: 90,
      closableBySwipe: true,
      closableByBackdrop: true,
      zIndex: 1200,
      class: 'restaurant-page__pdf-sheet',
    },
  );
};

const handleShowBar = () => {
  const pdf = findPdfByKey('bar');
  if (!pdf) return;
  open(
    PdfBottomSheet,
    { url: pdf.url, title: 'Барная карта' },
    {
      showHandle: true,
      height: 90,
      closableBySwipe: true,
      closableByBackdrop: true,
      zIndex: 1200,
      class: 'restaurant-page__pdf-sheet',
    },
  );
};

const handleShowPhotos = () => {
  if (galleryImages.value.length === 0) return;

  open(
    PhotoReels,
    { images: galleryImages.value, showPagination: true },
    {
      showHandle: true,
      closableByBackdrop: true,
      closableBySwipe: true,
      height: 90,
      zIndex: 1200,
      class: 'restaurant-page__photos-sheet',
    },
  );
};

onBeforeMount(async () => {
  const param = slug || route.params.slug.toString();

  store.getRestaurantData(param);

  // Telegram Mini App initialization and auth
  try {
    const tg = window.Telegram?.WebApp;
    tg?.ready?.();
    const initData = tg?.initData ?? '';
    if (initData) {
      await mini.authWithInitData(param);
    }
  } catch (_) {
    // ignore mini app auth errors silently
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

/* BottomSheet с галереей: фиксированная высота и учет handle */
.restaurant-page__photos-sheet {
  height: 80vh;
  max-height: 80vh;

  .bottom-sheet__content {
    height: calc(100% - 25px);
    overflow: hidden;
    padding: 0;
  }
}

/* BottomSheet с PDF: фиксированная высота и viewer занимает всю область */
.restaurant-page__pdf-sheet {
  height: 85vh;
  max-height: 85vh;

  .bottom-sheet__content {
    height: calc(100% - 25px);
    overflow: hidden;
    padding: 0;
  }
}
</style>
