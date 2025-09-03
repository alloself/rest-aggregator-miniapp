<template>
  <div class="event-bottom-sheet">
    <div v-if="loading" class="event-bottom-sheet__loading">
      <div class="loading-spinner">Загрузка...</div>
    </div>

    <div v-else-if="error" class="event-bottom-sheet__error">
      {{ error }}
    </div>
    
    <div v-else-if="event" class="event-bottom-sheet__content">
      <HeroCarousel
        :images="carousel_images"
        :height="'287px'"
        :autoplay="false"
        :showPagination="(event.images?.length || 0) > 1"
        :paginationBottom="'12px'"
      />

      <div class="event-bottom-sheet__info">
        <div class="event-bottom-sheet__title-card">
          <h1 class="event-bottom-sheet__title">{{ event.title }}</h1>
        </div>

        <div class="event-bottom-sheet__info-cards">
          <!-- Дата и время -->
          <div class="event-bottom-sheet__info-card">
            <div class="event-bottom-sheet__info-row">
              <Icon name="calendar" class="event-bottom-sheet__info-icon" />
              <span class="event-bottom-sheet__info-text">{{ formatDate(event.start_at) }}</span>
            </div>
            <div class="event-bottom-sheet__info-row">
              <Icon name="clock" class="event-bottom-sheet__info-icon" />
              <span class="event-bottom-sheet__info-text">{{ formatTime(event.start_at) }}</span>
            </div>
          </div>

          <div v-if="event.price" class="event-bottom-sheet__price-card">
            <Icon name="card" class="event-bottom-sheet__price-icon" />
            <span class="event-bottom-sheet__price-text">{{ formatPrice(event.price) }}</span>
          </div>
        </div>

        <div class="event-bottom-sheet__description-card">
          <h3 class="event-bottom-sheet__description-title">О событии</h3>
          <div class="event-bottom-sheet__description-text" v-html="sanitizedDescription"></div>
        </div>
      </div>

      <div class="event-bottom-sheet__actions">
        <button class="event-bottom-sheet__book-button" @click="handleBooking">Забронировать</button>
        <button class="event-bottom-sheet__telegram-button" @click="handleTelegramShare">
          <Icon name="telegram-icon-fill" class="event-bottom-sheet__telegram-icon" />
        </button>
      </div>
    </div>

    <div v-else class="event-bottom-sheet__not-found">Событие не найдено</div>
  </div>
</template>

<script setup lang="ts">
import { onBeforeMount, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useEventStore } from '../../entities/event';
import Icon from '@shared/ui/Icon.vue';
import HeroCarousel from '@shared/ui/HeroCarousel.vue';
import { dayjs, sanitizeHtml, formatPrice } from '../../shared/lib';
import type { Event } from '@/shared/types/models';

type Props = {
  slug: string;
  eventSlug: Event['slug'];
};
const { slug, eventSlug } = defineProps<Props>();

const store = useEventStore();
const { event, loading, error } = storeToRefs(store);

type CarouselImage = { url: string; alt?: string };
const carousel_images = computed<CarouselImage[]>(() => {
  const imgs = event.value?.images ?? [];
  return imgs.filter((img) => Boolean(img.url)).map((img) => ({ url: img.url, alt: img.name || event.value?.title }));
});

const sanitizedDescription = computed(() => 
  sanitizeHtml(event.value?.description ?? '')
);

const formatDate = (date: Date): string => {
  return dayjs(date).format('D MMMM');
};

const formatTime = (date: Date): string => {
  return dayjs(date).format('HH:mm');
};

// Используем общий хелпер formatPrice из shared/lib

const handleBooking = () => {
  // TODO: Реализовать логику бронирования
  console.log('Бронирование события:', event.value?.title);
};

const handleTelegramShare = () => {
  // TODO: Реализовать логику поделиться в Telegram
  console.log('Поделиться в Telegram:', event.value?.title);
};

onBeforeMount(async () => {
  if (slug && eventSlug) {
    await store.getEventData(slug, eventSlug);
  }
});
</script>