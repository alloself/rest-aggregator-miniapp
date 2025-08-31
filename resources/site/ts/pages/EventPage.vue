<template>
  <div class="event-page">
    <div v-if="loading" class="restaurant-page__loading">
      <div class="loading-spinner">Загрузка...</div>
    </div>

    <div v-else-if="error" class="restaurant-page__error">
      {{ error }}
    </div>
    <div v-else-if="event" class="event-page__content">
      <HeroCarousel
        :images="carousel_images"
        :height="'287px'"
        :autoplay="false"
        :showPagination="(event.images?.length || 0) > 1"
        :paginationBottom="'12px'"
      />

      <button class="event-page__back-button" @click="handleBackClick">
        <Icon name="arrow" :size="18" class="event-page__back-icon" />
      </button>

      <div class="event-page__overlay-content">
        <div class="event-page__title-card">
          <h1 class="event-page__title">{{ event.title }}</h1>
        </div>

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

          <div v-if="event.price" class="event-page__price-card">
            <Icon name="card" class="event-page__price-icon" />
            <span class="event-page__price-text">{{ formatPrice(event.price) }}</span>
          </div>
        </div>

        <div class="event-page__description-card">
          <h3 class="event-page__description-title">О событии</h3>
          <div class="event-page__description-text" v-html="event.description"></div>
        </div>
      </div>

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
