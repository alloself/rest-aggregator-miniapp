<template>
  <div class="restaurant-card">
    <!-- Hero Carousel -->
    <div class="restaurant-card__hero-carousel">
      <HeroCarousel :images="images" :autoplay="4000" height="300px" />
    </div>

    <!-- Restaurant Card -->
    <div class="restaurant-card__card">
      <div class="restaurant-card__content">
        <!-- Restaurant Name and Brief Description -->
        <div class="restaurant-card__header">
          <h1 class="restaurant-card__title">
            {{ restaurant?.name }}
          </h1>
          <p v-if="restaurant?.subtitle" class="restaurant-card__description">
            {{ restaurant.subtitle }}
          </p>
        </div>

        <!-- Restaurant Details Section -->
        <div class="restaurant-card__details">
          <!-- Work Schedule -->
          <div
            v-if="restaurant?.working_hours"
            class="restaurant-card__detail-item restaurant-card__detail-item--schedule"
          >
            <div class="restaurant-card__detail-icon restaurant-card__detail-icon--schedule">
              <Icon name="clock" size="16" color="gray" />
            </div>
            <div class="restaurant-card__detail-schedule">
              <WorkingHours :working-hours="restaurant.working_hours as any" />
            </div>
          </div>

          <!-- Average Check -->
          <div v-if="restaurant?.average_receipt" class="restaurant-card__detail-item">
            <div class="restaurant-card__detail-icon">
              <Icon name="card" size="16" color="gray" />
            </div>
            <span class="restaurant-card__detail-text">{{ formattedAverageReceipt }}</span>
          </div>

          <!-- Address -->
          <div v-if="restaurant?.address" class="restaurant-card__detail-item">
            <div class="restaurant-card__detail-icon">
              <Icon name="location" size="16" color="gray" />
            </div>
            <span class="restaurant-card__detail-text">{{ restaurant.address }}</span>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="restaurant-card__actions">
          <AppButton label="Меню" variant="secondary" size="small" @click="showMenu" fullWidth />
          <AppButton label="Бар" variant="secondary" size="small" @click="showBar" fullWidth />
          <AppButton label="Фото" variant="secondary" size="small" @click="showPhotos" fullWidth />
          <!-- Contact Dropdown -->
          <ContactDropdown
            v-if="contactInfo.phone || contactInfo.telegram"
            :phone="contactInfo.phone"
            :telegram-username="contactInfo.telegram"
          />
        </div>
      </div>
    </div>

    <!-- Restaurant Full Description -->
    <div v-if="restaurant?.description" class="restaurant-card__full-description">
      <CollapsibleText
        :text="restaurant.description"
        :character-limit="120"
        expand-button-text="еще"
        collapse-button-text="свернуть"
        :animation-duration="300"
        class="restaurant-card__full-description-text"
      />
    </div>

    <!--    Chef Recommendations-->
    <section v-if="chefRecommendations.length > 0" class="restaurant-card__chef-section">
      <h2 class="restaurant-card__chef-title">Шеф рекомендует</h2>

      <div class="restaurant-card__chef-items">
        <!-- Featured Large Item -->
        <div v-if="chefRecommendations" class="restaurant-card__chef-featured">
          <div class="restaurant-card__chef-featured-image">
            <Icon name="chief" class="restaurant-card__chef-image" />
          </div>
        </div>

        <!-- Regular Items -->
        <div
          v-for="dish in chefRecommendations"
          :key="dish.id"
          class="restaurant-card__chef-item"
          @click="handleDishClick(dish)"
        >
          <AppImage
            :src="dish.images?.[0]?.url || ''"
            :alt="dish.name"
            width="120"
            height="120"
            object-fit="cover"
            border-radius="lg"
            class="restaurant-card__chef-image restaurant-card__chef-image--regular"
          />
        </div>
      </div>
    </section>

    <Categories v-if="restaurant?.slug" :slug="restaurant.slug" />

    <!-- Footer -->
    <div class="restaurant-card__footer">
      <p class="restaurant-card__footer-text">By Eat.Drink.Repeat</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Dish, Restaurant } from '@/shared';

// Shared UI components
import { Icon } from '@/shared';
import HeroCarousel from '@shared/ui/HeroCarousel.vue';
import { AppButton, AppImage, WorkingHours } from '../../shared/ui';
import { bottomSheet } from '../../shared/lib/composables';

// Local components
import ContactDropdown from '../../shared/ui/ContactDropdown.vue';
import CollapsibleText from '../../shared/ui/CollapsibleText.vue';
import Categories from './Categories.vue';
import { DishBottomSheet } from '../../entities/dish/ui';

interface ContactInfo {
  phone?: string;
  telegram?: string;
}

interface Props {
  restaurant: Restaurant | null;
  chefRecommendations?: Dish[];
  contactInfo?: ContactInfo;
}

const props = withDefaults(defineProps<Props>(), {
  chefRecommendations: () => [],
  contactInfo: () => ({}),
});

const emit = defineEmits<{
  showMenu: [];
  showBar: [];
  showPhotos: [];
}>();

const images = computed(() => props.restaurant?.images || []);

const formattedAverageReceipt = computed(() => {
  if (!props.restaurant?.average_receipt) return '';

  const receipt = props.restaurant.average_receipt.toString().trim();

  const isOnlyNumber = /^\d+$/.test(receipt);

  if (isOnlyNumber) {
    return `${receipt} ₽`;
  }

  return receipt;
});

// Methods
const showMenu = () => {
  emit('showMenu');
};

const showBar = () => {
  emit('showBar');
};

const showPhotos = () => {
  emit('showPhotos');
};

/**
 * Обработка клика по блюду - открытие bottom sheet
 */
const handleDishClick = (dish: Dish) => {
  bottomSheet.open(DishBottomSheet, {
    dish,
  });
};
</script>
