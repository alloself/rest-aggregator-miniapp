<template>
  <div class="event-page">
    <div v-if="loading" class="event-page__loading">
      <div class="loading-spinner">Загрузка...</div>
    </div>

    <div v-else-if="error" class="event-page__error">
      {{ error }}
    </div>

    <div v-else-if="event" class="event-page__content">
      <!-- Основная карточка события -->
      <AppCard class="event-page__main-card">
        <div class="event-page__layout">
          <!-- Изображение события -->
          <div class="event-page__image-container">
            <AppImage
              v-if="event.images?.[0]"
              :src="event.images[0].url"
              :alt="event.title"
              class="event-page__image"
              object-fit="cover"
            />
            <div v-else class="event-page__image-placeholder"></div>
            
            <!-- Кнопка назад -->
            <button class="event-page__back-button" @click="handleBackClick">
              <Icon name="chevron-left" class="event-page__back-icon" />
            </button>
          </div>

          <!-- Содержимое события -->
          <div class="event-page__content-wrapper">
            <!-- Заголовки и дата -->
            <div class="event-page__header">
              <div class="event-page__date-badge">
                <span class="event-page__date-dot"></span>
                {{ formatDate(event.start_at) }}
              </div>
              
              <h1 class="event-page__title">{{ event.title }}</h1>
              <h2 class="event-page__subtitle">{{ event.subtitle }}</h2>
            </div>

            <!-- Информация о времени и цене -->
            <div class="event-page__info">
              <div class="event-page__info-item">
                <Icon name="clock" class="event-page__info-icon" />
                <span class="event-page__info-text">{{ formatTime(event.start_at) }}</span>
              </div>
              
              <div v-if="event.price" class="event-page__info-item">
                <Icon name="card" class="event-page__info-icon" />
                <span class="event-page__info-text">{{ formatPrice(event.price) }}</span>
              </div>
            </div>

            <!-- Описание -->
            <div class="event-page__description">
              <h3 class="event-page__description-title">О мероприятии</h3>
              <p class="event-page__description-text">{{ event.description }}</p>
            </div>

            <!-- Кнопка бронирования -->
            <div class="event-page__actions">
              <button class="event-page__book-button" @click="handleBooking">
                Забронировать место
              </button>
            </div>
          </div>
        </div>
      </AppCard>
    </div>

    <div v-else class="event-page__not-found">Событие не найдено</div>
  </div>
</template>

<script setup lang="ts">
import { onBeforeMount, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useEventStore } from '../entities/event';
import AppCard from '../shared/ui/AppCard.vue';
import AppImage from '../shared/ui/AppImage.vue';
import Icon from '@shared/ui/Icon.vue';
import { dayjs } from '../shared/lib/dayjs';

const route = useRoute();
const router = useRouter();

const slug = computed(() => (typeof route.params.slug === 'string' ? route.params.slug : ''));
const eventId = computed(() => (typeof route.params.eventId === 'string' ? route.params.eventId : ''));

const store = useEventStore();
const { event, loading, error } = storeToRefs(store);

const formatDate = (date: Date): string => {
  return dayjs(date).format('D MMMM');
};

const formatTime = (date: Date): string => {
  return dayjs(date).format('HH:mm');
};

const formatPrice = (price: number): string => {
  return `${price.toLocaleString('ru-RU')} ₽`;
};

const handleBackClick = () => {
  router.back();
};

const handleBooking = () => {
  // TODO: Реализовать логику бронирования
  console.log('Бронирование события:', event.value?.title);
};

onBeforeMount(async () => {
  if (slug.value && eventId.value) {
    await store.getEventData(slug.value, eventId.value);
  }
});
</script>

<style scoped>
.event-page :deep(.p-card-body) {
  padding: 0px;
}

.event-page {
  width: 100%;
  margin: 0 auto;
  background-color: var(--background-primary);
  min-height: 100vh;
  overflow-x: hidden;
  padding: 0;
}

.event-page__loading,
.event-page__error,
.event-page__not-found {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 60vh;
  text-align: center;
  padding: 40px 20px;
}

.event-page__error {
  color: var(--color-accent-warning);
  font-size: var(--font-size-body-md);
}

.event-page__not-found {
  color: var(--text-secondary);
  font-size: var(--font-size-body-md);
}

.loading-spinner {
  font-size: var(--font-size-body-md);
  color: var(--text-secondary);
}

.event-page__content {
  width: 100%;
  padding: 0;
}

.event-page__main-card {
  margin: 0;
  border-radius: 0;
  box-shadow: none;
  overflow: hidden;
}

.event-page__layout {
  display: flex;
  flex-direction: column;
}

.event-page__image-container {
  position: relative;
  width: 100%;
  height: 300px;
  overflow: hidden;
  border-radius: 0 0 40px 40px;
  overflow: hidden;
}

.event-page__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.event-page__image-placeholder {
  width: 100%;
  height: 100%;
  background: var(--surface-default);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-secondary);
}

.event-page__back-button {
  position: absolute;
  top: 20px;
  left: 20px;
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.9);
  border: none;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all var(--transition-default);
  backdrop-filter: blur(10px);
}

.event-page__back-button:hover {
  background: rgba(255, 255, 255, 1);
  transform: scale(1.05);
}

.event-page__back-icon {
  width: 20px;
  height: 20px;
  color: var(--text-primary);
}

.event-page__content-wrapper {
  padding: 24px 20px;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.event-page__header {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.event-page__date-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  width: fit-content;
  height: 32px;
  background: var(--color-accent-secondary);
  color: var(--text-primary);
  border-radius: var(--border-radius-card);
  padding: 0 12px;
  font-size: var(--font-size-caption-default);
  font-weight: 500;
  line-height: 1;
}

.event-page__date-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: var(--color-accent-warning);
  flex-shrink: 0;
}

.event-page__title {
  font-size: var(--font-size-heading-lg);
  font-weight: 600;
  color: var(--text-primary);
  line-height: 1.2;
  letter-spacing: -1%;
  margin: 0;
}

.event-page__subtitle {
  font-size: var(--font-size-heading-sm);
  font-weight: 400;
  color: var(--text-secondary);
  line-height: 1.3;
  letter-spacing: -2%;
  margin: 0;
}

.event-page__info {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.event-page__info-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.event-page__info-icon {
  width: 20px;
  height: 20px;
  color: var(--text-primary);
  flex-shrink: 0;
}

.event-page__info-text {
  font-size: var(--font-size-body-md);
  color: var(--text-primary);
  font-weight: 500;
  line-height: 1.2;
}

.event-page__description {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.event-page__description-title {
  font-size: var(--font-size-heading-sm);
  font-weight: 600;
  color: var(--text-primary);
  line-height: 1.2;
  margin: 0;
}

.event-page__description-text {
  font-size: var(--font-size-body-md);
  color: var(--text-primary);
  line-height: 1.5;
  letter-spacing: -1%;
  margin: 0;
}

.event-page__actions {
  margin-top: 8px;
}

.event-page__book-button {
  width: 100%;
  height: 52px;
  background: var(--color-accent-primary);
  border: none;
  border-radius: var(--border-radius-card);
  color: var(--text-primary);
  font-size: var(--font-size-body-md);
  font-weight: 600;
  cursor: pointer;
  transition: all var(--transition-default);
  display: flex;
  align-items: center;
  justify-content: center;
}

.event-page__book-button:hover {
  background: var(--color-accent-primary-hover);
  transform: translateY(-1px);
}

.event-page__book-button:active {
  background: var(--color-accent-primary-active);
  transform: translateY(0);
}

@media (min-width: 768px) {
  .event-page__layout {
    flex-direction: row;
    max-width: 1200px;
    margin: 0 auto;
  }

  .event-page__image-container {
    width: 50%;
    height: 500px;
  }

  .event-page__content-wrapper {
    width: 50%;
    padding: 40px;
  }
}

@media (max-width: 480px) {
  .event-page__content-wrapper {
    padding: 20px 16px;
    gap: 20px;
  }

  .event-page__title {
    font-size: var(--font-size-heading-md);
  }

  .event-page__subtitle {
    font-size: var(--font-size-body-md);
  }
}
</style>
