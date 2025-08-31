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

const formatPrice = (price: number): string => {
  return `${price.toLocaleString('ru-RU')} ₽`;
};
</script>

<style scoped>
.event-card {
  cursor: pointer;
  transition: all var(--transition-default);
  position: relative;
  overflow: hidden;
  box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.15);
  margin-inline: 16px;
}

.event-card__layout {
  display: flex;
  align-items: stretch;
  gap: 18px;
  padding: 0;
  position: relative;
  min-height: 180px;
}

.event-card__image-container {
  position: relative;
  flex-shrink: 0;
  width: 140px;
  border-radius: var(--border-radius-medium);
  overflow: hidden;
}

.event-card__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.event-card__image-placeholder {
  width: 139px;
  height: 173px;
  background: var(--surface-default);
  border-radius: var(--border-radius-medium);
}

.event-card__date-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  height: 28px;
  background: var(--color-accent-secondary);
  color: var(--text-primary);
  border-radius: var(--border-radius-card);
  justify-content: center;
  font-size: var(--font-size-caption-default);
  font-weight: 500;
  line-height: 1;
}

.event-card__sold-out-badge {
  background-color: #9d9d9d !important;
  color: #ffffff !important;
  border-radius: 9999px;
  height: 28px;
  padding: 0 10px;
  font-size: var(--font-size-caption-default);
  font-weight: 600;
  line-height: 28px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.event-card__date-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: var(--color-accent-warning);
}

.event-card__content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 12px;
  min-width: 0;
  position: relative;
  align-items: flex-start;
}

.event-card__date-badges {
  display: flex;
  gap: 8px;
}

.event-card__title {
  font-size: var(--font-size-heading-sm);
  font-weight: 600;
  color: var(--text-primary);
  line-height: 1.1;
  letter-spacing: -1%;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.event-card__description {
  font-size: var(--font-size-body-sm);
  color: var(--text-primary);
  line-height: 1.1;
  letter-spacing: -2%;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.event-card__chips {
  display: flex;
  align-items: center;
  margin-top: auto;
}

.event-card__chip {
  height: 30px;
  background: transparent !important;
  border: none !important;
  border-radius: 30px !important;
  padding: 8px !important;
}

.event-card__chip--time .p-chip-text {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  justify-content: center;
}

.event-card__chip--price .p-chip-text {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  justify-content: center;
}

.event-card__chip-icon {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
}
.event-card__chip-icon--time {
  color: var(--text-primary);
}
.event-card__chip-icon--price {
  color: var(--text-primary);
  width: 16px;
  height: 12px;
}

.event-card__chip-text {
  font-size: var(--font-size-body-sm);
  color: var(--text-primary);
  font-weight: 400;
  line-height: 1;
  letter-spacing: -2%;
}
</style>
