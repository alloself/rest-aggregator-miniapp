<template>
  <div class="contact-dropdown" ref="dropdownRef">
    <!-- Trigger Button -->
    <button 
      @click="toggleDropdown"
      class="contact-dropdown__trigger"
    >
      <Icon 
        name="mdi:phone" 
        size="19"
        color="black"
        class="contact-dropdown__icon"
      />
    </button>

    <!-- Dropdown Menu -->
    <div 
      v-if="isOpen"
      class="contact-dropdown__menu"
    >
      <!-- Phone Option -->
      <div class="contact-dropdown__item" @click="handlePhoneCall">
        <div class="contact-dropdown__item-icon contact-dropdown__item-icon--phone">
          <SvgIcon 
            name="phone-icon" 
            size="custom"
            :custom-size="22"
            color="black"
          />
        </div>
        <span class="contact-dropdown__item-text contact-dropdown__item-text--phone">Позвонить</span>
      </div>

      <!-- Telegram Option -->
      <div class="contact-dropdown__item" @click="handleTelegramMessage">
        <div class="contact-dropdown__item-icon contact-dropdown__item-icon--telegram">
          <SvgIcon 
            name="telegram-icon" 
            size="custom"
            :custom-size="18"
            color="telegram"
          />
        </div>
        <span class="contact-dropdown__item-text contact-dropdown__item-text--telegram">Написать</span>
      </div>

      <!-- Booking Option -->
      <div class="contact-dropdown__item" @click="handleBooking">
        <div class="contact-dropdown__item-icon contact-dropdown__item-icon--booking">
          <SvgIcon 
            name="booking-icon" 
            size="custom"
            :custom-size="18"
            color="black"
          />
        </div>
        <span class="contact-dropdown__item-text contact-dropdown__item-text--booking">Забронировать</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { Icon } from '@shared/ui'
import SvgIcon from './SvgIcon.vue'

// Props
interface Props {
  phone?: string
  telegramUsername?: string
}

const props = withDefaults(defineProps<Props>(), {
  phone: '+7 (495) 123-45-67',
  telegramUsername: 'restaurant_bot'
})

// Reactive data
const isOpen = ref(false)
const dropdownRef = ref<HTMLElement>()

// Methods
const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const closeDropdown = () => {
  isOpen.value = false
}

const handlePhoneCall = () => {
  if (props.phone) {
    // Remove all non-numeric characters for tel: link
    const cleanPhone = props.phone.replace(/[^\d+]/g, '')
    window.location.href = `tel:${cleanPhone}`
  }
  closeDropdown()
}

const handleTelegramMessage = () => {
  if (props.telegramUsername) {
    const telegramUrl = `https://t.me/${props.telegramUsername}`
    window.open(telegramUrl, '_blank')
  }
  closeDropdown()
}

const handleBooking = () => {
  // TODO: Integrate with booking system
  closeDropdown()
}

// Click outside handler
const handleClickOutside = (event: Event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    closeDropdown()
  }
}

// Lifecycle
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
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
    background-color: rgba(200, 228, 155, 0.35);
    border: none;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    justify-content: center;

    &:hover {
      background-color: rgba(200, 228, 155, 0.5);
    }

    &:focus {
      outline: none;
    }
  }

  &__icon {
    width: 19px;
    height: 18px;
    color: #000000;
  }

  &__menu {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    background-color: #FFFFFF;
    border-radius: 15px;
    box-shadow: 0px 0px 4px 0px rgba(0, 0, 0, 0.2);
    width: 177px;
    height: 134px;
    z-index: 100;
    animation: dropdown-appear 0.2s ease-out;
  }

  &__item {
    position: absolute;
    width: 100%;
    cursor: pointer;
    transition: background-color 0.2s ease;

    &:hover {
      background-color: rgba(244, 243, 243, 0.5);
      border-radius: 8px;
      margin: 0 4px;
      width: calc(100% - 8px);
    }
  }

  &__item-icon {
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #000000;

    &--phone {
      left: 18px;
      top: 15px;
      width: 22px;
      height: 22px;
    }

    &--telegram {
      left: 21px;
      top: 57px;
      width: 18px;
      height: 18px;
      color: #2AABEE;
    }

    &--booking {
      left: 23px;
      top: 97px;
      width: 18px;
      height: 18px;
    }
  }

  &__item-text {
    position: absolute;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    font-size: 15px;
    font-weight: 400;
    color: #000000;
    line-height: 1em;
    letter-spacing: -0.03em;

    &--phone {
      left: 51px;
      top: 17px;
      width: 77px;
      height: 18px;
    }

    &--telegram {
      left: 51px;
      top: 57px;
      width: 70px;
      height: 18px;
    }

    &--booking {
      left: 51px;
      top: 99px;
      width: 111px;
      height: 18px;
    }
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