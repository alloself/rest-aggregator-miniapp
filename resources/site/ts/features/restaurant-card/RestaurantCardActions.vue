<template>
  <div class="restaurant-card__actions">
    <AppButton label="Меню" variant="secondary" size="medium" @click="handleShowMenu" fullWidth />
    <AppButton label="Бар" variant="secondary" size="medium" @click="handleShowBar" fullWidth />
    <AppButton label="Фото" variant="secondary" size="medium" @click="handleShowPhotos" fullWidth />

    <ContactDropdown
      v-if="contactInfo.phone || contactInfo.telegram"
      :phone="contactInfo.phone"
      :telegram-username="contactInfo.telegram"
    />
  </div>
</template>

<script setup lang="ts">
import { AppButton } from '../../shared/ui';
import ContactDropdown from '../contact-dropdown/ContactDropdown.vue';

interface ContactInfo {
  phone?: string;
  telegram?: string;
}

interface Props {
  contactInfo: ContactInfo;
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
</script>
