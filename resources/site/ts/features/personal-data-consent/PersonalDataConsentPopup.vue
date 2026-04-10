<template>
  <Teleport to="body">
    <div v-if="shouldShowPopup" class="personal-data-consent-popup">
      <div
        class="personal-data-consent-popup__content"
        :class="{ 'personal-data-consent-popup__content--compact': !canShowLegalLinks }"
      >
        <p v-if="canShowLegalLinks" class="personal-data-consent-popup__text">
          Продолжая использовать приложение, вы даете
          <RouterLink :to="personalDataLink" class="personal-data-consent-popup__link">
            согласие на обработку ваших персональных данных
          </RouterLink>
          в соответствии с условиями
          <RouterLink :to="privacyLink" class="personal-data-consent-popup__link">
            политики конфиденциальности
          </RouterLink>
        </p>

        <button
          type="button"
          class="personal-data-consent-popup__button"
          @click="handleAcceptClick"
        >
          Принять
        </button>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { computed, onMounted, ref, watch } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { useRestaurantStore } from '@site/ts/entities/restaurant';
import { client } from '@site/ts/shared/api/axios';
import { hasRequiredRestaurantLegalDocuments } from '@/shared/helpers/restaurantLegalDocuments';
import type { RestaurantResourceData } from '@/shared/types/resources';

const PERSONAL_DATA_CONSENT_STORAGE_KEY = 'site-personal-data-consent-accepted';
const LEGAL_ROUTE_NAMES = new Set(['site-legal-personal-data', 'site-legal-privacy']);

const route = useRoute();
const restaurantStore = useRestaurantStore();
const { restaurant, loading: isRestaurantLoading } = storeToRefs(restaurantStore);

const isConsentResolved = ref(false);
const isConsentAccepted = ref(false);
const isLegalLinksResolved = ref(false);
const canShowLegalLinks = ref(false);

let legalLinksRequestId = 0;

const restaurantSlug = computed(() => {
  const routeSlug = route.params.slug;
  if (typeof routeSlug === 'string') {
    return routeSlug;
  }

  const queryRestaurant = route.query.restaurant;
  return typeof queryRestaurant === 'string' ? queryRestaurant : undefined;
});

const legalRouteQuery = computed(() => (
  restaurantSlug.value ? { restaurant: restaurantSlug.value } : undefined
));

const personalDataLink = computed(() => ({
  name: 'site-legal-personal-data',
  query: legalRouteQuery.value,
}));

const privacyLink = computed(() => ({
  name: 'site-legal-privacy',
  query: legalRouteQuery.value,
}));

const isLegalRoute = computed(() => (
  typeof route.name === 'string' && LEGAL_ROUTE_NAMES.has(route.name)
));

const shouldShowPopup = computed(() => (
  isConsentResolved.value
  && isLegalLinksResolved.value
  && !isConsentAccepted.value
  && !isLegalRoute.value
));

const handleAcceptClick = () => {
  isConsentAccepted.value = true;
  localStorage.setItem(PERSONAL_DATA_CONSENT_STORAGE_KEY, 'true');
};

const setLegalLinksVisibility = (files?: RestaurantResourceData['files']) => {
  canShowLegalLinks.value = hasRequiredRestaurantLegalDocuments(files);
  isLegalLinksResolved.value = true;
};

const resolveLegalLinksVisibility = async (): Promise<void> => {
  const slug = restaurantSlug.value;
  const requestId = ++legalLinksRequestId;

  if (!slug || isLegalRoute.value || isConsentAccepted.value) {
    canShowLegalLinks.value = false;
    isLegalLinksResolved.value = true;

    return;
  }

  if (restaurant.value?.slug === slug) {
    setLegalLinksVisibility(restaurant.value.files);

    return;
  }

  if (typeof route.name === 'string' && route.name === 'restaurant' && isRestaurantLoading.value) {
    isLegalLinksResolved.value = false;

    return;
  }

  isLegalLinksResolved.value = false;

  try {
    const response = await client.get<RestaurantResourceData>(`/api/site/restaurants/${slug}`);

    if (requestId !== legalLinksRequestId) {
      return;
    }

    setLegalLinksVisibility(response.data.files);
  } catch (error) {
    if (requestId !== legalLinksRequestId) {
      return;
    }

    canShowLegalLinks.value = false;
    isLegalLinksResolved.value = true;
    console.error('Failed to resolve restaurant legal documents', error);
  }
};

watch(
  [
    restaurantSlug,
    isLegalRoute,
    isConsentAccepted,
    isRestaurantLoading,
    () => restaurant.value?.slug,
    () => restaurant.value?.files,
  ],
  () => {
    if (!isConsentResolved.value) {
      return;
    }

    void resolveLegalLinksVisibility();
  },
  { immediate: true },
);

onMounted(() => {
  isConsentAccepted.value = localStorage.getItem(PERSONAL_DATA_CONSENT_STORAGE_KEY) === 'true';
  isConsentResolved.value = true;
  void resolveLegalLinksVisibility();
});
</script>

<style scoped>
.personal-data-consent-popup {
  position: fixed;
  right: 16px;
  bottom: 16px;
  left: 16px;
  z-index: 60;
  display: flex;
  justify-content: center;
  pointer-events: none;
}

.personal-data-consent-popup__content {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px;
  border: 1px solid rgb(0 0 0 / 10%);
  border-radius: 14px;
  background: #fffefb;
  box-shadow: 0 8px 24px rgb(0 0 0 / 8%);
  pointer-events: auto;
}

.personal-data-consent-popup__content--compact {
  width: fit-content;
  max-width: 100%;
  justify-content: center;
}

.personal-data-consent-popup__text {
  margin: 0;
  color: #000;
  font-size: 12px;
  line-height: 1.2;
}

.personal-data-consent-popup__link {
  color: inherit;
  text-decoration: underline;
}

.personal-data-consent-popup__button {
  flex-shrink: 0;
  min-width: 78px;
  padding: 10px;
  border: 0;
  border-radius: 20px;
  background: #eee;
  color: #000;
  font-size: 12px;
  font-weight: 600;
  line-height: 1.3;
  text-align: center;
  cursor: pointer;
}

@media (max-width: 480px) {
  .personal-data-consent-popup__content {
    width: 100%;
  }

  .personal-data-consent-popup__content--compact {
    width: fit-content;
  }
}
</style>
