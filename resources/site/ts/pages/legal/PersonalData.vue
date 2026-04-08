<template>
  <RestaurantLegalPdfPage
    :document-key="LEGAL_PERSONAL_DATA_PDF_KEY"
    :eyebrow="personalDataDocumentEyebrow"
    :title="personalDataDocumentTitle"
    :lead="personalDataDocumentLead"
    :sections="personalDataDocumentSections"
  />
</template>

<script setup lang="ts">
import RestaurantLegalPdfPage from './RestaurantLegalPdfPage.vue';
import { LEGAL_PERSONAL_DATA_PDF_KEY } from '@/shared/constants/legalDocuments';
import {
  personalDataDocumentEyebrow,
  personalDataDocumentLead,
  personalDataDocumentSections,
  personalDataDocumentTitle,
} from '@/shared/helpers/personalDataDocument';
</script>
