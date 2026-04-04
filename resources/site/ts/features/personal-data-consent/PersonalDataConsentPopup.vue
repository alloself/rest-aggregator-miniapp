<template>
  <Teleport to="body">
    <div v-if="shouldShowPopup" class="personal-data-consent-popup">
      <div class="personal-data-consent-popup__content">
        <p class="personal-data-consent-popup__text">
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
import { computed, onMounted, ref } from 'vue';
import { RouterLink, useRoute } from 'vue-router';

const PERSONAL_DATA_CONSENT_STORAGE_KEY = 'site-personal-data-consent-accepted';
const LEGAL_ROUTE_NAMES = new Set(['site-legal-personal-data', 'site-legal-privacy']);

const route = useRoute();

const isConsentResolved = ref(false);
const isConsentAccepted = ref(false);

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
  isConsentResolved.value && !isConsentAccepted.value && !isLegalRoute.value
));

const handleAcceptClick = () => {
  isConsentAccepted.value = true;
  localStorage.setItem(PERSONAL_DATA_CONSENT_STORAGE_KEY, 'true');
};

onMounted(() => {
  isConsentAccepted.value = localStorage.getItem(PERSONAL_DATA_CONSENT_STORAGE_KEY) === 'true';
  isConsentResolved.value = true;
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
}
</style>
