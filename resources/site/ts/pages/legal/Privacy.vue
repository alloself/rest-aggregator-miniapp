<template>
  <LegalDocumentPage
    :back-link="backLink"
    :back-label="backLabel"
    :eyebrow="privacyPolicyEyebrow"
    :title="privacyPolicyTitle"
    :lead="privacyPolicyLead"
    :sections="privacyPolicySections"
  />
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import LegalDocumentPage from '@/shared/components/LegalDocumentPage.vue';
import {
  privacyPolicyEyebrow,
  privacyPolicyLead,
  privacyPolicySections,
  privacyPolicyTitle,
} from '@/shared/helpers/privacyPolicyDocument';

const route = useRoute();

const restaurantSlug = computed(() => {
  const slug = route.query.restaurant;

  return typeof slug === 'string' ? slug : null;
});

const backLink = computed(() => {
  if (restaurantSlug.value) {
    return { path: `/restaurant/${restaurantSlug.value}` };
  }

  return { path: '/' };
});

const backLabel = computed(() => (restaurantSlug.value ? 'Назад в приложение' : 'На главную'));
</script>
