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
      <span class="restaurant-card__detail-text">{{ restaurant.address }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { RestaurantResourceData } from '@/shared/types/resources';
import { Icon } from '@/shared';
import { WorkingHours } from '../../../shared/ui';

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
</script>
