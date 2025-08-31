<template>
  <div class="restaurant-card__actions">
    <AppButton label="Меню" variant="secondary" size="small" @click="handleShowMenu" fullWidth />
    <AppButton label="Бар" variant="secondary" size="small" @click="handleShowBar" fullWidth />
    <AppButton label="Фото" variant="secondary" size="small" @click="handleShowPhotos" fullWidth />

    <ContactDropdown
      v-if="contactInfo.phone || contactInfo.telegram"
      :phone="contactInfo.phone"
      :telegram-username="contactInfo.telegram"
    />

    <div class="restaurant-card__like-row">
      <AppButton
        :label="likedLabel"
        variant="primary"
        size="small"
        :disabled="isLiking"
        @click="handleToggleLike(restaurantId)"
        fullWidth
      />

      <div v-if="friends.length" class="restaurant-card__friends">
        <div v-for="f in friends" :key="f.id" class="friend-avatar" :title="friendTitle(f)">
          <img v-if="f.avatar_url" :src="f.avatar_url" alt="" />
          <div v-else class="friend-avatar__placeholder">{{ initials(f) }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

import { useMiniAppStore } from '../../shared/stores/miniapp';
import type { User } from '../../../../../shared/types/models';
import { AppButton } from '../../shared/ui';
import ContactDropdown from '../contact-dropdown/ContactDropdown.vue';

interface ContactInfo {
  phone?: string;
  telegram?: string;
}

interface Props {
  contactInfo: ContactInfo;
  restaurantId?: string;
}

const props = withDefaults(defineProps<Props>(), {
  contactInfo: () => ({}),
});

const emit = defineEmits<{
  showMenu: [];
  showBar: [];
  showPhotos: [];
}>();

function handleShowMenu() {
  emit('showMenu');
}

function handleShowBar() {
  emit('showBar');
}

function handleShowPhotos() {
  emit('showPhotos');
}

const mini = useMiniAppStore();
const likedLabel = computed(() => (mini.likedByMe ? 'Repeat ✓' : 'Repeat'));
const isLiking = computed(() => mini.isLiking);
const friends = computed<User[]>(() => mini.friends);

function friendTitle(f: User): string {
  return [f.first_name, f.last_name].filter(Boolean).join(' ');
}

function initials(f: User): string {
  const a = (f.first_name || '').trim();
  const b = (f.last_name || '').trim();
  return `${a.charAt(0)}${b.charAt(0)}`.toUpperCase();
}

function handleToggleLike(id?: string): void {
  const targetId = id ?? props.restaurantId;
  if (!targetId) return;
  void mini.toggleRestaurantLike(targetId);
}
</script>
