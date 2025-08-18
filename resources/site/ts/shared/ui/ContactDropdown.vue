<template>
  <div class="contact-dropdown" ref="dropdownRef">
    <!-- Trigger Button -->
    <AppButton @click="toggleDropdown" variant="help" size="small" class="contact-dropdown__trigger">
      <template #icon>
        <Icon name="phone" size="12" class="contact-dropdown__icon" />
      </template>
    </AppButton>

    <!-- Dropdown Menu -->
    <div v-if="isOpen" class="contact-dropdown__menu">
      <!-- Phone Option -->
      <div class="contact-dropdown__item" @click="handlePhoneCall">
        <div class="contact-dropdown__item-icon contact-dropdown__item-icon--phone">
          <Icon name="call" :size="22" />
        </div>
        <span class="contact-dropdown__item-text">Позвонить</span>
      </div>

      <!-- Divider -->
      <div class="contact-dropdown__divider"></div>

      <!-- Telegram Option -->
      <div class="contact-dropdown__item" @click="handleTelegramMessage">
        <div class="contact-dropdown__item-icon contact-dropdown__item-icon--telegram">
          <Icon name="telegram" :size="18" />
        </div>
        <span class="contact-dropdown__item-text">Написать</span>
      </div>

      <!-- Divider -->
      <div class="contact-dropdown__divider"></div>

      <!-- Booking Option -->
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

import { AppButton } from './index';
import { Icon } from '@/shared';

// Props
interface Props {
  phone?: string;
  telegramUsername?: string;
}

const props = withDefaults(defineProps<Props>(), {
  phone: '+7 (495) 123-45-67',
  telegramUsername: 'restaurant_bot',
});

// Reactive data
const isOpen = ref(false);
const dropdownRef = ref<HTMLElement>();

// Methods
const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

const closeDropdown = () => {
  isOpen.value = false;
};

const handlePhoneCall = () => {
  if (props.phone) {
    // Remove all non-numeric characters for tel: link
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
  // TODO: Integrate with booking system
  closeDropdown();
};

// Click outside handler
const handleClickOutside = (event: Event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    closeDropdown();
  }
};

// Lifecycle
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<style>
/* Contact Dropdown Component */
.contact-dropdown {
  position: relative;
  display: inline-block;

  /* Reset any PrimeVue styles */
  * {
    box-sizing: border-box;
  }

  &__trigger {
    width: 59px;
    height: 39px;
  }

  &__icon {
    width: 19px;
    height: 18px;
    color: var(--color-text-primary);
  }

  &__menu {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    background-color: var(--color-white-pure);
    border-radius: var(--radius-small);
    box-shadow: var(--shadow-button);
    width: 177px;
    min-height: 134px;
    z-index: 100;
    animation: dropdown-appear 0.2s ease-out;
    padding: var(--spacing-xs) 0;
  }

  &__item {
    display: flex;
    align-items: center;
    width: 100%;
    padding: var(--spacing-xs) var(--spacing-lg);
    gap: var(--gap-xlarge);
    cursor: pointer;
    transition: background-color var(--transition-default);
    min-height: 44px;

    &:hover {
      background-color: var(--color-bg-secondary);
    }
  }

  &__item-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-text-primary);
    flex-shrink: 0;

    &--phone {
      width: 22px;
      height: 22px;
    }

    &--telegram {
      width: 18px;
      height: 18px;
    }

    &--booking {
      width: 18px;
      height: 18px;
    }
  }

  &__item-text {
    font-size: var(--font-size-body-default);
    font-weight: var(--font-weight-body-default);
    line-height: var(--line-height-body-default);
    letter-spacing: var(--letter-spacing-body-default);
    color: var(--color-text-primary);
  }

  &__divider {
    height: 1px;
    background-color: var(--color-border-light);
    margin: 0 var(--spacing-lg);
  }
}

/* Animations */
@keyframes dropdown-appear {
  from {
    opacity: 0;
    transform: translateY(-8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
