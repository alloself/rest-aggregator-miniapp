<template>
  <LegalDocumentPage
    :back-link="backLink"
    :back-label="backLabel"
    :eyebrow="personalDataDocumentEyebrow"
    :title="personalDataDocumentTitle"
    :lead="personalDataDocumentLead"
    :sections="personalDataDocumentSections"
  />
</template>

<script setup lang="ts">
import { computed } from 'vue';
import LegalDocumentPage from '@/shared/components/LegalDocumentPage.vue';
import {
  personalDataDocumentEyebrow,
  personalDataDocumentLead,
  personalDataDocumentSections,
  personalDataDocumentTitle,
} from '@/shared/helpers/personalDataDocument';
import { useAuthStore } from '@/shared';

const authStore = useAuthStore();

const backLink = computed(() => (authStore.user ? '/restaurant' : '/login'));
const backLabel = computed(() =>
  authStore.user ? 'Назад в личный кабинет' : 'Назад к входу',
);
</script>
