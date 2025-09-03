<template>
  <div class="restaurant-card__details">
    <div v-if="restaurant?.working_hours" class="restaurant-card__detail-item restaurant-card__detail-item--schedule">
      <div class="restaurant-card__detail-icon restaurant-card__detail-icon--schedule">
        <Icon name="clock" size="16" color="gray" />
      </div>
      <div class="restaurant-card__detail-schedule">
        <WorkingHours :working-hours="restaurant.working_hours" />
      </div>
    </div>

    <div v-if="restaurant?.average_receipt" class="restaurant-card__detail-item">
      <div class="restaurant-card__detail-icon">
        <Icon name="card" size="16" color="gray" />
      </div>
      <span class="restaurant-card__detail-text">{{ formattedAverageReceipt }}</span>
    </div>

    <div v-if="restaurant?.address" class="restaurant-card__detail-item">
      <div class="restaurant-card__detail-icon">
        <Icon name="location" size="16" color="gray" />
      </div>
      <span 
        class="restaurant-card__detail-text restaurant-card__detail-text--clickable"
        @click="handleAddressClick"
      >
        {{ restaurant.address }}
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { RestaurantResourceData } from '@shared/types/resources';
import { Icon } from '@/shared';
import { WorkingHours } from '../../shared/ui';

interface Props {
  restaurant: RestaurantResourceData | null;
}

const props = defineProps<Props>();

const formattedAverageReceipt = computed(() => {
  if (!props.restaurant?.average_receipt) return '';

  const receipt = props.restaurant.average_receipt.toString().trim();
  const isOnlyNumber = /^\d+$/.test(receipt);
  if (isOnlyNumber) return `${receipt} ₽`;
  return receipt;
});

/**
 * Генерирует ссылку для поиска адреса в Яндекс.Картах.
 * Используется text=, так как rtext=~address может открывать приложение без точки.
 * TODO: когда появятся координаты ресторана, перейти на pt=lon,lat или rtext=from~to с координатами.
 */
const generateYandexMapsUrl = (address: string): string => {
  const encoded_address = encodeURIComponent(address);
  return `https://yandex.ru/maps/?text=${encoded_address}`;
};

/**
 * Обработчик клика по адресу - открывает маршрут в Яндекс картах
 */
const handleAddressClick = (): void => {
  if (!props.restaurant?.address) return;
  
  const mapsUrl = generateYandexMapsUrl(props.restaurant.address);
  window.open(mapsUrl, '_blank');
};
</script>

<style scoped>
.restaurant-card__detail-text--clickable {
  cursor: pointer;
  transition: opacity 0.2s ease;
}

.restaurant-card__detail-text--clickable:hover {
  opacity: 0.7;
}

.restaurant-card__detail-text--clickable:active {
  opacity: 0.5;
}
</style>
