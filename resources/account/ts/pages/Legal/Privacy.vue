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
import LegalDocumentPage from '@/shared/components/LegalDocumentPage.vue';
import {
  privacyPolicyEyebrow,
  privacyPolicyLead,
  privacyPolicySections,
  privacyPolicyTitle,
} from '@/shared/helpers/privacyPolicyDocument';
import { useAuthStore } from '@/shared';

const authStore = useAuthStore();

const backLink = computed(() => (authStore.user ? '/restaurant' : '/login'));
const backLabel = computed(() => (authStore.user ? 'Назад в личный кабинет' : 'Назад к входу'));
</script>
