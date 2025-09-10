<template>
  <div class="contact-dropdown" ref="dropdownRef">
    <AppButton @click="toggleDropdown" variant="help" size="medium" class="contact-dropdown__trigger">
      <template #icon>
        <Icon name="phone" size="12" class="contact-dropdown__icon" />
      </template>
    </AppButton>

    <div v-if="isOpen" class="contact-dropdown__menu">
      <div class="contact-dropdown__item" @click="handlePhoneCall">
        <div class="contact-dropdown__item-icon contact-dropdown__item-icon--phone">
          <Icon name="call" :size="22" />
        </div>
        <span class="contact-dropdown__item-text">Позвонить</span>
      </div>

      <div class="contact-dropdown__divider"></div>

      <div class="contact-dropdown__item" @click="handleTelegramMessage">
        <div class="contact-dropdown__item-icon contact-dropdown__item-icon--telegram">
          <Icon name="telegram" :size="18" />
        </div>
        <span class="contact-dropdown__item-text">Написать</span>
      </div>

      <div class="contact-dropdown__divider"></div>

      <div class="contact-dropdown__item" @click="handleBooking">
        <div class="contact-dropdown__item-icon contact-dropdown__item-icon--booking">
          <Icon name="booking" :size="18" />
        </div>
        <span class="contact-dropdown__item-text">Забронировать</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

import { AppButton } from '../../shared/ui';
import { Icon } from '@/shared';

interface Props {
  phone?: string;
  telegramUsername?: string;
}

const props = withDefaults(defineProps<Props>(), {
  phone: '+7 (495) 123-45-67',
  telegramUsername: 'restaurant_bot',
});

const isOpen = ref(false);
const dropdownRef = ref<HTMLElement>();

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

const closeDropdown = () => {
  isOpen.value = false;
};

const handlePhoneCall = () => {
  if (props.phone) {
    const cleanPhone = props.phone.replace(/[^\d+]/g, '');
    window.location.href = `tel:${cleanPhone}`;
  }
  closeDropdown();
};

const handleTelegramMessage = () => {
  if (props.telegramUsername) {
    const telegramUrl = `https://t.me/${props.telegramUsername}`;
    window.open(telegramUrl, '_blank');
  }
  closeDropdown();
};

const handleBooking = () => {
  // При клике на "Забронировать" звоним в ресторан
  if (props.phone) {
    const cleanPhone = props.phone.replace(/[^\d+]/g, '');
    window.location.href = `tel:${cleanPhone}`;
  }
  closeDropdown();
};

const handleClickOutside = (event: Event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    closeDropdown();
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
