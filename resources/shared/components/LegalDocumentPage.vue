<template>
  <div class="legal-document-page">
    <div class="legal-document-page__container">
      <RouterLink :to="props.backLink" class="legal-document-page__back">
        <Icon name="mdi:arrow-left" size="20px" />
        {{ props.backLabel }}
      </RouterLink>

      <article class="legal-document">
        <header class="legal-document__header">
          <p v-if="props.eyebrow" class="legal-document__eyebrow">
            {{ props.eyebrow }}
          </p>
          <h1 class="legal-document__title">
            {{ props.title }}
          </h1>
          <p v-if="props.lead" class="legal-document__lead">
            {{ props.lead }}
          </p>
        </header>

        <nav v-if="hasContents" aria-labelledby="legal-document-contents-title" class="legal-document__contents">
          <h2 id="legal-document-contents-title" class="legal-document__contents-title">Содержание</h2>
          <ol class="legal-document__contents-list">
            <li v-for="section in props.sections" :key="section.id" class="legal-document__contents-item">
              <a :href="`#${section.id}`" class="legal-document__contents-link">
                {{ section.title }}
              </a>
            </li>
          </ol>
        </nav>

        <section v-for="section in props.sections" :id="section.id" :key="section.id" class="legal-document__section">
          <h2 class="legal-document__section-title">
            {{ section.title }}
          </h2>
          <div class="legal-document__section-text" v-html="formatLegalDocumentBody(section.body)" />
        </section>
      </article>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import type { RouteLocationRaw } from 'vue-router';
import Icon from '@shared/ui/Icon.vue';
import { formatLegalDocumentBody } from '@/shared/helpers/legalDocument';
import type { LegalDocumentSection } from '@/shared/helpers/legalDocument';

interface Props {
  backLink: RouteLocationRaw;
  backLabel: string;
  title: string;
  sections: LegalDocumentSection[];
  eyebrow?: string;
  lead?: string;
}

const props = defineProps<Props>();

const hasContents = computed(() => props.sections.length > 1);
</script>

<style scoped>
.legal-document-page {
  min-height: 100vh;
  padding: 2rem 1rem 3rem;
  background: linear-gradient(to bottom right, #eff6ff, #eef2ff, #faf5ff);
}

.legal-document-page__container {
  width: 100%;
  max-width: 72rem;
  margin: 0 auto;
}

.legal-document-page__back {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  color: #2563eb;
  font-weight: 500;
  text-decoration: none;
}

.legal-document-page__back:hover {
  color: #1d4ed8;
}

.legal-document {
  overflow: hidden;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 1rem;
  box-shadow: 0 24px 60px -30px rgb(15 23 42 / 0.35);
}

.legal-document__header {
  padding: 2rem;
  background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  border-bottom: 1px solid #e5e7eb;
}

.legal-document__eyebrow {
  margin: 0 0 0.75rem;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #4f46e5;
}

.legal-document__title {
  margin: 0;
  font-size: clamp(1.875rem, 3vw, 2.5rem);
  line-height: 1.1;
  color: #111827;
}

.legal-document__lead {
  max-width: 48rem;
  margin: 1rem 0 0;
  color: #4b5563;
  line-height: 1.7;
}

.legal-document__contents {
  padding: 1.5rem 2rem;
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.legal-document__contents-title {
  margin: 0 0 1rem;
  font-size: 0.875rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: #374151;
}

.legal-document__contents-list {
  margin: 0;
  padding-left: 1.25rem;
  columns: 2;
  column-gap: 2rem;
}

.legal-document__contents-item {
  break-inside: avoid;
  margin-bottom: 0.625rem;
  color: #4b5563;
}

.legal-document__contents-link {
  color: #1d4ed8;
  text-decoration: none;
}

.legal-document__contents-link:hover {
  color: #1e40af;
  text-decoration: underline;
}

.legal-document__section {
  padding: 1.75rem 2rem;
  scroll-margin-top: 1rem;
}

.legal-document__section + .legal-document__section {
  border-top: 1px solid #f3f4f6;
}

.legal-document__section-title {
  margin: 0 0 1rem;
  font-size: 1.125rem;
  font-weight: 700;
  color: #111827;
}

.legal-document__section-text {
  margin: 0;
  font-family: 'Georgia', 'Times New Roman', serif;
  font-size: 1rem;
  line-height: 1.9;
  color: #1f2937;
  overflow-wrap: anywhere;
}

.legal-document__section-text :deep(a) {
  color: #1d4ed8;
  text-decoration: underline;
  text-underline-offset: 0.14em;
}

.legal-document__section-text :deep(a:hover) {
  color: #1e40af;
}

@media (max-width: 768px) {
  .legal-document-page {
    padding: 1.25rem 0.75rem 2rem;
  }

  .legal-document__header,
  .legal-document__contents,
  .legal-document__section {
    padding-left: 1.25rem;
    padding-right: 1.25rem;
  }

  .legal-document__contents-list {
    columns: 1;
  }

  .legal-document__section-text {
    font-size: 0.95rem;
    line-height: 1.8;
  }
}
</style>
