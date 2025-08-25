<template>
  <div class="event-page">
    <div v-if="loading" class="restaurant-page__loading">
      <div class="loading-spinner">Загрузка...</div>
    </div>

    <div v-else-if="error" class="restaurant-page__error">
      {{ error }}
    </div>
    <div v-else-if="event" class="event-page__content">
      <!-- Карусель изображений с overlay контентом -->

      <!-- Карусель изображений -->
      <HeroCarousel
        :images="carousel_images"
        :height="'287px'"
        :autoplay="false"
        :showPagination="(event.images?.length || 0) > 1"
        :paginationBottom="'12px'"
      />

      <!-- Кнопка назад -->
      <button class="event-page__back-button" @click="handleBackClick">
        <Icon name="arrow" :size="18" class="event-page__back-icon" />
      </button>

      <!-- Контент поверх изображения -->
      <div class="event-page__overlay-content">
        <!-- Заголовок события -->
        <div class="event-page__title-card">
          <h1 class="event-page__title">{{ event.title }}</h1>
        </div>

        <!-- Информационные карточки -->
        <div class="event-page__info-cards">
          <!-- Дата и время -->
          <div class="event-page__info-card">
            <div class="event-page__info-row">
              <Icon name="calendar" class="event-page__info-icon" />
              <span class="event-page__info-text">{{ formatDate(event.start_at) }}</span>
            </div>
            <div class="event-page__info-row">
              <Icon name="clock" class="event-page__info-icon" />
              <span class="event-page__info-text">{{ formatTime(event.start_at) }}</span>
            </div>
          </div>

          <!-- Цена -->
          <div v-if="event.price" class="event-page__price-card">
            <Icon name="card" class="event-page__price-icon" />
            <span class="event-page__price-text">{{ formatPrice(event.price) }}</span>
          </div>
        </div>

        <!-- Описание -->
        <div class="event-page__description-card">
          <h3 class="event-page__description-title">О событии</h3>
          <div class="event-page__description-text" v-html="event.description"></div>
        </div>
      </div>

      <!-- Фиксированная нижняя панель -->
      <div class="event-page__bottom-panel">
        <button class="event-page__book-button" @click="handleBooking">Забронировать</button>
        <button class="event-page__telegram-button" @click="handleTelegramShare">
          <Icon name="telegram-icon-fill" class="event-page__telegram-icon" />
        </button>
      </div>
    </div>

    <div v-else class="event-page__not-found">Событие не найдено</div>
  </div>
</template>

<script setup lang="ts">
import { onBeforeMount, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useEventStore } from '../entities/event';
import Icon from '@shared/ui/Icon.vue';
import HeroCarousel from '@shared/ui/HeroCarousel.vue';
import { dayjs } from '../shared/lib/dayjs';

const route = useRoute();
const router = useRouter();

const slug = computed(() => (typeof route.params.slug === 'string' ? route.params.slug : ''));
const eventSlug = computed(() => (typeof route.params.eventSlug === 'string' ? route.params.eventSlug : ''));

const store = useEventStore();
const { event, loading, error } = storeToRefs(store);

// Изображения для универсальной карусели
type CarouselImage = { url: string; alt?: string };
const carousel_images = computed<CarouselImage[]>(() => {
  const imgs = event.value?.images ?? [];
  return imgs.filter((img) => Boolean(img.url)).map((img) => ({ url: img.url, alt: img.name || event.value?.title }));
});

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

const handleTelegramShare = () => {
  // TODO: Реализовать логику поделиться в Telegram
  console.log('Поделиться в Telegram:', event.value?.title);
};

onBeforeMount(async () => {
  if (slug.value && eventSlug.value) {
    await store.getEventData(slug.value, eventSlug.value);
  }
});
</script>

<style scoped>
.event-page {
  width: 100%;
  margin: 0 auto;
  min-height: 100vh;
  overflow-x: hidden;
  padding: 0;
  position: relative;
}

.event-page__not-found {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 60vh;
  text-align: center;
  padding: 40px 20px;
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
  padding-bottom: 120px; /* Пространство для нижней панели */
}

/* Главный контейнер с каруселью */
.event-page__hero {
  position: relative;
  width: 100%;
  height: 100vh;
  max-height: 1137px;
  overflow: hidden;
}

/* Кнопка назад */
.event-page__back-button {
  position: absolute;
  top: 14px;
  left: 14px;
  width: 30px;
  height: 30px;
  background: rgba(0, 0, 0, 0.42);
  border: none;
  border-radius: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all var(--transition-default);
  backdrop-filter: blur(4px);
  z-index: 10;
}

.event-page__back-button:hover {
  background: rgba(0, 0, 0, 0.6);
  transform: scale(1.05);
}

.event-page__back-icon {
  width: 13px;
  height: 9px;
  color: #ffffff;
}

/* Контент поверх изображения */
.event-page__overlay-content {
  position: absolute;
  top: 308px;
  left: 20px;
  right: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

/* Карточка заголовка */
.event-page__title-card {
  background: rgba(238, 238, 238, 0.5);
  border-radius: 20px;
  padding: 20px;
  backdrop-filter: blur(10px);
}

.event-page__title {
  font-weight: 500;
  font-size: 18px;
  line-height: 1.1;
  letter-spacing: -1%;
  color: #000000;
  margin: 0;
}

/* Информационные карточки */
.event-page__info-cards {
  display: flex;
  gap: 10px;
  align-items: center;
}

.event-page__info-card {
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(238, 238, 238, 0.5);
  border-radius: 20px;
  padding: 20px 32px;
  backdrop-filter: blur(10px);
  position: relative;
  gap: 25px;
  flex: 1;
}

.event-page__info-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 2px;
}

.event-page__info-row:last-child {
  margin-bottom: 0;
}

.event-page__info-icon {
  width: 16px;
  height: 16px;
  color: #000000;
  flex-shrink: 0;
}

.event-page__info-text {
  font-weight: 500;
  font-size: 14px;
  line-height: 1;
  color: #000000;
}

/* Карточка цены */
.event-page__price-card {
  background: rgba(238, 238, 238, 0.5);
  border-radius: 20px;
  padding: 20px;
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  gap: 8px;
  justify-content: center;
  width: fit-content;
  height: 56px;
}

.event-page__price-text {
  font-weight: 500;
  font-size: 14px;
  line-height: 1;
}

/* Карточка описания */
.event-page__description-card {
  background: rgba(238, 238, 238, 0.5);
  border-radius: 20px;
  padding: 22px 30px;
  backdrop-filter: blur(10px);
}

.event-page__description-title {
  font-weight: 500;
  font-size: 16px;
  line-height: 1.1;
  margin-bottom: 16px;
}

.event-page__description-text {
  font-size: 16px;
  line-height: 1.3;
  margin: 0;
  max-height: 417px;
  overflow-y: auto;
}

/* Фиксированная нижняя панель */
.event-page__bottom-panel {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: 120px;
  background: #fffefd;
  box-shadow: 4px 0px 20px 2px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  padding: 31px 25px;
  gap: 7px;
  z-index: 1000;
}

.event-page__book-button {
  flex: 1;
  height: 60px;
  background: rgba(236, 246, 220, 0.96);
  border: none;
  border-radius: 30px;
  color: #000000;
  font-weight: 500;
  font-size: 16px;
  line-height: 1.21;
  letter-spacing: -1%;
  cursor: pointer;
  transition: all var(--transition-default);
  display: flex;
  align-items: center;
  justify-content: center;
}

.event-page__book-button:hover {
  background: rgba(236, 246, 220, 1);
  transform: translateY(-1px);
}

.event-page__book-button:active {
  background: rgba(226, 236, 210, 1);
  transform: translateY(0);
}

.event-page__telegram-button {
  width: 60px;
  height: 60px;
  background: rgba(238, 238, 238, 0.5);
  border: none;
  border-radius: 30px;
  cursor: pointer;
  transition: all var(--transition-default);
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(10px);
}

.event-page__telegram-button:hover {
  background: rgba(238, 238, 238, 0.8);
  transform: translateY(-1px);
}

.event-page__telegram-button:active {
  background: rgba(228, 228, 228, 0.8);
  transform: translateY(0);
}

.event-page__telegram-icon {
  width: 22px;
  height: 20px;
  color: #000000;
}

@media (max-width: 480px) {
  .event-page__info-cards {
    gap: 8px;
  }

  .event-page__price-icon {
    left: 35px;
  }

  .event-page__bottom-panel {
    padding: 20px 16px;
  }

  .event-page__overlay-content {
    left: 16px;
    right: 16px;
  }
}
</style>
