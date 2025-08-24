<template>
  <div class="event-card" @click="handleCardClick">
    <div class="event-card__image-container">
      <AppImage
        v-if="item.images?.[0]"
        :src="item.images[0].url"
        :alt="item.title"
        class="event-card__image"
        object-fit="cover"
        :width="120"
        :height="120"
      />
      <div v-else class="event-card__image-placeholder"></div>
    </div>

    <div class="event-card__content">
      <div class="event-card__date-tag">
        {{ formatDate(item.start_at) }}
      </div>

      <h3 class="event-card__title">{{ item.title }}</h3>
      <p class="event-card__description">{{ item.subtitle }}</p>

      <div class="event-card__details">
        <div class="event-card__detail">
          <Icon name="clock" class="event-card__detail-icon" />
          <span class="event-card__detail-text">{{ formatTime(item.start_at) }}</span>
        </div>

        <div v-if="item.price" class="event-card__detail">
          <Icon name="card" class="event-card__detail-icon" />
          <span class="event-card__detail-text">{{ formatPrice(item.price) }}</span>
        </div>
      </div>
    </div>

    <div class="event-card__arrow">
      <Icon name="arrow" class="event-card__arrow-icon" />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Event as EventItem } from '@/shared';
import AppImage from '@site/ts/shared/ui/AppImage.vue';
import Icon from '@shared/ui/Icon.vue';

const { item } = defineProps<{ item: EventItem }>();

const emit = defineEmits<{
  click: [item: EventItem];
}>();

const handleCardClick = () => {
  emit('click', item);
};

const formatDate = (date: Date): string => {
  const eventDate = new Date(date);
  const day = eventDate.getDate();
  const month = eventDate.toLocaleDateString('ru-RU', { month: 'long' });
  return `${day} ${month}`;
};

const formatTime = (date: Date): string => {
  const eventDate = new Date(date);
  return eventDate.toLocaleTimeString('ru-RU', {
    hour: '2-digit',
    minute: '2-digit',
  });
};

const formatPrice = (price: number): string => {
  return `${price.toLocaleString('ru-RU')} ₽`;
};
</script>

<style scoped>
.event-card {
  display: flex;
  align-items: stretch;
  padding: 16px;
  border: 1px solid var(--border-default);
  border-radius: var(--border-radius-card);
  background: var(--bg-card);
  box-shadow: var(--box-shadow-card);
  cursor: pointer;
  transition: all var(--transition-default);
  gap: 16px;
  position: relative;
  overflow: hidden;
}

.event-card:hover {
  box-shadow: var(--box-shadow-elevated);
  transform: translateY(-2px);
}

.event-card__image-container {
  position: relative;
  flex-shrink: 0;
  width: 120px;
  height: 120px;
  border-radius: var(--border-radius-medium);
  overflow: hidden;
}

.event-card__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.event-card__image-placeholder {
  width: 100%;
  height: 100%;
  background: var(--surface-default);
  border-radius: var(--border-radius-medium);
}

.event-card__date-tag {
  position: absolute;
  top: 16px;
  right: 16px;
  background: #fee68a;
  color: #1a1a1a;
  padding: 4px 8px;
  border-radius: var(--border-radius-full);
  font-size: var(--font-size-body-sm);
  font-weight: 500;
  line-height: 1;
}

.event-card__content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-width: 0;
  position: relative;
}

.event-card__title {
  font-size: var(--font-size-heading-sm);
  font-weight: 600;
  color: var(--text-primary);
  line-height: 1.3;
  margin: 0;
  overflow: hidden;
}

.event-card__description {
  font-size: var(--font-size-body-default);
  color: var(--text-secondary);
  line-height: 1.4;
  margin: 0;
  overflow: hidden;
}

.event-card__details {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-top: auto;
}

.event-card__detail {
  display: flex;
  align-items: center;
  gap: 6px;
}

.event-card__detail-icon {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
  color: var(--text-secondary);
}

.event-card__detail-text {
  font-size: var(--font-size-body-sm);
  color: var(--text-primary);
  font-weight: 500;
}

.event-card__arrow {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  flex-shrink: 0;
  align-self: center;
}

.event-card__arrow-icon {
  width: 20px;
  height: 20px;
  color: var(--text-secondary);
  transition: transform var(--transition-fast);
}

.event-card:hover .event-card__arrow-icon {
  transform: translateX(2px);
}

@media (max-width: 480px) {
  .event-card {
    padding: 12px;
    gap: 12px;
  }

  .event-card__image-container {
    width: 100px;
    height: 100px;
  }

  .event-card__date-tag {
    top: 12px;
    right: 12px;
  }

  .event-card__title {
    font-size: var(--font-size-body-md);
  }

  .event-card__description {
    font-size: var(--font-size-body-sm);
  }
}
</style>
