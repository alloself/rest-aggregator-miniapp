<template>
  <div class="restaurant-card">
    <div class="restaurant-card__hero-carousel">
      <HeroCarousel :images="images" :autoplay="4000" height="300px" :show-pagination="false" />
      <RepeatButton
        class="restaurant-card__like-button"
        :is-repeated="mini.likedByMe"
        :friends-count="allUsersWhoLiked.length"
        :friends="allUsersWhoLiked"
        :disabled="isLiking"
        @toggle="handleToggleLike"
        @friends-click="handleFriendsClick"
      />
    </div>

    <div class="restaurant-card__card">
      <div class="restaurant-card__content">
        <RestaurantCardHeader :restaurant="restaurant" />

        <RestaurantCardDetails :restaurant="restaurant" />

        <RestaurantCardActions
          :contact-info="contactInfo"
          @show-menu="showMenu"
          @show-bar="showBar"
          @show-photos="showPhotos"
        />
      </div>
    </div>

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

    <ChefRecommendations :chef-recommendations="chefRecommendations" />

    <Feed v-if="restaurant?.slug" :slug="restaurant.slug" />

    <div class="restaurant-card__footer">
      <p class="restaurant-card__footer-text">By Eat.Drink.Repeat</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';

import HeroCarousel from '@shared/ui/HeroCarousel.vue';
import { useMiniAppStore } from '../../shared/stores/miniapp';
import { User } from '@/shared';

import CollapsibleText from '../../shared/ui/CollapsibleText.vue';
import Feed from './Feed.vue';
import { DishResourceData, RestaurantResourceData } from '@/shared/types/resources';
import ChefRecommendations from './ChefRecommendations.vue';
import RestaurantCardHeader from '../../features/restaurant-card/RestaurantCardHeader.vue';
import RestaurantCardDetails from '../../features/restaurant-card/RestaurantCardDetails.vue';
import RestaurantCardActions from '../../features/restaurant-card/RestaurantCardActions.vue';
import RepeatButton from '../../features/restaurant-card/RepeatButton.vue';
import { FriendsBottomSheet } from '../../features/friends-bottom-sheet';
import { useBottomSheet } from '../../shared';

interface ContactInfo {
  phone?: string;
  telegram?: string;
}

interface Props {
  restaurant: RestaurantResourceData | null;
  chefRecommendations?: DishResourceData[];
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

const images = computed(() => props.restaurant?.images?.filter((img) => img?.pivot?.key === 'cover') || []);

const mini = useMiniAppStore();
const isLiking = computed(() => mini.isLiking);
const friends = computed(() => mini.friends || []);

const { open } = useBottomSheet();

// Структура друга из API
interface Friend {
  id: string;
  name: string;
  username: string;
  profile_photo_url?: string | null;
}

// Union type для всех кто лайкнул ресторан
type UserOrFriend = User | Friend;

// Все пользователи которые лайкнули ресторан (друзья + сам пользователь если лайкнул)
const allUsersWhoLiked = computed<UserOrFriend[]>(() => {
  const friendsList = friends.value || [];
  const result: UserOrFriend[] = [...friendsList]; // все друзья (они все лайкнули этот ресторан)
  
  // Добавляем самого пользователя если он лайкнул
  if (mini.likedByMe && mini.me && !result.find(f => f.id === mini.me!.id)) {
    result.unshift(mini.me); // добавляем в начало
  }
  
  return result;
});

function handleToggleLike(): void {
  const restaurantId = props.restaurant?.id;
  if (!restaurantId) return;
  void mini.toggleRestaurantLike(restaurantId);
}

function handleFriendsClick(): void {
  open(
    FriendsBottomSheet,
    {
      friends: allUsersWhoLiked.value, // все кто лайкнул - у всех будет значок R
    },
    {
      showHandle: true,
      closableByBackdrop: true,
      closableBySwipe: true,
      height: 70,
      bottomGap: true,
    }
  );
}

const showMenu = () => {
  emit('showMenu');
};

const showBar = () => {
  emit('showBar');
};

const showPhotos = () => {
  emit('showPhotos');
};
</script>

<style scoped>
.restaurant-card__hero-carousel {
  position: relative;
}

.restaurant-card__like-button {
  position: absolute;
  bottom: 48px;
  right: 16px;
  z-index: 10;
}
</style>
