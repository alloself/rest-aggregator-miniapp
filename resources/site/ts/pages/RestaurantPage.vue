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
  <BottomSheetContainer />
</template>

<script setup lang="ts">
import { onBeforeMount, computed } from 'vue';
import { useRoute } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useRestaurantStore } from '../entities/restaurant';
import RestaurantCard from '../widgets/restaurant-page/RestaurantCard.vue';
import { BottomSheetContainer } from '../features/bottom-sheet';
import PhotoReels from '@/shared/components/PhotoReels.vue';
import { useBottomSheet } from '../shared';
import { PdfBottomSheet } from '../shared/ui';

const { slug } = defineProps<{ slug?: string }>();

const route = useRoute();

const { open } = useBottomSheet();

const store = useRestaurantStore();
const { restaurant, loading, error } = storeToRefs(store);

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
});
</script>
