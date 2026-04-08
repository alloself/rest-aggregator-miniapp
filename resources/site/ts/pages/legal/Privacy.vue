<template>
  <RestaurantLegalPdfPage
    :document-key="LEGAL_PRIVACY_PDF_KEY"
    :eyebrow="privacyPolicyEyebrow"
    :title="privacyPolicyTitle"
    :lead="privacyPolicyLead"
    :sections="privacyPolicySections"
  />
</template>

<script setup lang="ts">
import RestaurantLegalPdfPage from './RestaurantLegalPdfPage.vue';
import { LEGAL_PRIVACY_PDF_KEY } from '@/shared/constants/legalDocuments';
import {
  privacyPolicyEyebrow,
  privacyPolicyLead,
  privacyPolicySections,
  privacyPolicyTitle,
} from '@/shared/helpers/privacyPolicyDocument';
</script>
