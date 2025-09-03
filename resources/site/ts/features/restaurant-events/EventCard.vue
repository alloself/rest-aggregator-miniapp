<template>
  <Card class="event-card" @click="handleCardClick">
    <template #content>
      <div class="event-card__layout">
        <div class="event-card__image-container">
          <AppImage
            v-if="item.images?.[0]"
            :src="item.images[0].url"
            :alt="item.title"
            class="event-card__image"
            object-fit="cover"
          />
          <div v-else class="event-card__image-placeholder"></div>
        </div>

        <div class="event-card__content">
          <div class="event-card__date-badges">
            <Badge class="event-card__date-badge" severity="secondary">
              <template #default>
                <span class="event-card__date-dot"></span>
                {{ formatDate(item.start_at) }}
              </template>
            </Badge>
            <Badge v-if="item.is_sold_out" class="event-card__sold-out-badge" value="Sold out" />
          </div>

          <h3 class="event-card__title">{{ item.title }}</h3>
          <p class="event-card__description">{{ item.subtitle }}</p>

          <div class="event-card__chips">
            <Chip class="event-card__chip event-card__chip--time">
              <template #default>
                <Icon name="clock" class="event-card__chip-icon event-card__chip-icon--time" />
                <span class="event-card__chip-text">{{ formatTime(item.start_at) }}</span>
              </template>
            </Chip>

            <Chip v-if="item.price" class="event-card__chip event-card__chip--price">
              <template #default>
                <Icon name="card" class="event-card__chip-icon event-card__chip-icon--price" />
                <span class="event-card__chip-text">{{ formatPrice(item.price) }}</span>
              </template>
            </Chip>
          </div>
        </div>
      </div>
    </template>
  </Card>
</template>

<script setup lang="ts">
import type { Event as EventItem } from '@/shared';
import AppImage from '@site/ts/shared/ui/AppImage.vue';
import Icon from '@shared/ui/Icon.vue';
import { dayjs } from '@site/ts/shared/lib/dayjs';

const { item } = defineProps<{ item: EventItem }>();

const emit = defineEmits<{
  click: [item: EventItem];
}>();

const handleCardClick = () => {
  emit('click', item);
};

const formatDate = (date: Date): string => {
  return dayjs(date).format('D MMMM');
};

const formatTime = (date: Date): string => {
  return dayjs(date).format('HH:mm');
};

const formatPrice = (price: number | string): string => {
  const raw = typeof price === 'string' ? price : String(price);
  const beforeDot = raw.split('.')[0];
  const cleaned = beforeDot.replace(/[^\d-]/g, '');
  const numeric = Number(cleaned);
  return `${Number.isFinite(numeric) ? numeric.toLocaleString('ru-RU') : beforeDot} ₽`;
};
</script>
