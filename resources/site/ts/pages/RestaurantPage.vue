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
import { useRestaurantMedia } from '../features/restaurant-media-bottom-sheet';
import { useMiniAppStore } from '../shared/stores/miniapp';
import { useBottomSheet } from '../shared/lib/composables/useBottomSheet';
import { EventBottomSheet } from '../features/event-bottom-sheet';
import { NewsBottomSheet } from '../features/news-bottom-sheet/index';

const { slug } = defineProps<{ slug?: string }>();

const route = useRoute();

const { openMenu, openBar, openPhotos } = useRestaurantMedia();

const store = useRestaurantStore();
const { restaurant, loading, error } = storeToRefs(store);
const mini = useMiniAppStore();

const chefRecommendations = computed(() => {
  const categories = restaurant.value?.categories ?? [];
  const chief = categories.find((c) => c?.pivot?.key === 'chief_recommendations');
  return Array.isArray(chief?.dishes) ? chief!.dishes : [];
});

const contactInfo = computed(() => ({
  phone: restaurant.value?.phone,
  telegram: restaurant.value?.bot_username,
}));

const galleryImages = computed(() => {
  const images = restaurant.value?.images ?? [];
  return images
    .filter((img) => img?.pivot?.key === 'gallery')
    .map((img) => ({
      ...img,
      fileables_count: 0,
      fileables_exists: false,
    }));
});

const menuFiles = computed(() => {
  const files = restaurant.value?.files || [];
  return files
    .filter((f) => f?.pivot?.key === 'menu')
    .map((f) => ({
      ...f,
      fileables_count: 0,
      fileables_exists: false,
    }));
});

const barFiles = computed(() => {
  const files = restaurant.value?.files || [];
  return files
    .filter((f) => f?.pivot?.key === 'bar')
    .map((f) => ({
      ...f,
      fileables_count: 0,
      fileables_exists: false,
    }));
});

const restaurantName = computed(() => restaurant.value?.name || 'Ресторан');

const handleShowMenu = () => {
  if (menuFiles.value.length === 0) return;
  openMenu(restaurantName.value, menuFiles.value, 0);
};

const handleShowBar = () => {
  if (barFiles.value.length === 0) return;
  openBar(restaurantName.value, barFiles.value, 0);
};

const handleShowPhotos = () => {
  if (galleryImages.value.length === 0) return;
  openPhotos(restaurantName.value, galleryImages.value, 0);
};

onBeforeMount(async () => {
  const param = slug || route.params.slug.toString();

  store.getRestaurantData(param);

  // Telegram Mini App initialization and auth
  try {
    const tg = window.Telegram?.WebApp;
    tg?.ready();
    const initData = tg?.initData ?? '';
    if (initData) {
      await mini.authWithInitData(param);
    }

    // Авто-открытие BottomSheet по deep-link start_param
    const startParam = tg?.initDataUnsafe?.start_param;
    if (typeof startParam === 'string' && startParam.length > 0) {
      // Компактная схема: e_<eventSlug> | n_<newsSlug>
      const kind = startParam.slice(0, 2);
      const slugPart = startParam.slice(2);
      const targetSlug = slugPart.replace(/^[_-]/, '');

      const { open } = useBottomSheet();
      if (kind === 'e_' && targetSlug) {
        open(EventBottomSheet, { slug: param, eventSlug: targetSlug }, { height: 90 });
      }
      if (kind === 'n_' && targetSlug) {
        open(NewsBottomSheet, { slug: param, newsSlug: targetSlug }, { height: 90 });
      }
    }
  } catch (_) {
    // ignore mini app auth errors silently
  }
});
</script>
