<template>
  <div v-if="documentFile" class="site-legal-pdf-page">
    <div class="site-legal-pdf-page__container">
      <RouterLink :to="backLink" class="site-legal-pdf-page__back">
        <Icon name="mdi:arrow-left" size="20px" />
        {{ backLabel }}
      </RouterLink>

      <article class="site-legal-pdf-viewer">
        <header class="site-legal-pdf-viewer__header">
          <p v-if="eyebrow" class="site-legal-pdf-viewer__eyebrow">
            {{ eyebrow }}
          </p>
          <div class="site-legal-pdf-viewer__header-row">
            <div>
              <h1 class="site-legal-pdf-viewer__title">
                {{ title }}
              </h1>
              <p v-if="lead" class="site-legal-pdf-viewer__lead">
                {{ lead }}
              </p>
            </div>

            <a
              :href="documentFile.url"
              class="site-legal-pdf-viewer__link"
              rel="noopener noreferrer"
              target="_blank"
            >
              Открыть PDF
            </a>
          </div>
        </header>

        <iframe
          :src="documentFile.url"
          class="site-legal-pdf-viewer__frame"
          :title="title"
        />
      </article>
    </div>
  </div>

  <div v-else-if="restaurantSlug && isLoading" class="site-legal-pdf-page">
    <div class="site-legal-pdf-page__container">
      <RouterLink :to="backLink" class="site-legal-pdf-page__back">
        <Icon name="mdi:arrow-left" size="20px" />
        {{ backLabel }}
      </RouterLink>

      <div class="site-legal-pdf-viewer site-legal-pdf-viewer--loading">
        Загрузка документа...
      </div>
    </div>
  </div>

  <LegalDocumentPage
    v-else
    :back-link="backLink"
    :back-label="backLabel"
    :eyebrow="eyebrow"
    :title="title"
    :lead="lead"
    :sections="sections"
  />
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import Icon from '@shared/ui/Icon.vue';
import LegalDocumentPage from '@/shared/components/LegalDocumentPage.vue';
import { client } from '../../shared/api/axios';
import type { FileResourceData, RestaurantResourceData } from '@/shared/types/resources';
import type { LegalDocumentSection } from '@/shared/helpers/legalDocument';
import type { LegalDocumentFileKey } from '@/shared/constants/legalDocuments';

const props = defineProps<{
  documentKey: LegalDocumentFileKey;
  eyebrow: string;
  title: string;
  lead: string;
  sections: LegalDocumentSection[];
}>();

const route = useRoute();

const documentFile = ref<FileResourceData | null>(null);
const isLoading = ref(false);

const restaurantSlug = computed(() => {
  const queryRestaurant = route.query.restaurant;

  return typeof queryRestaurant === 'string' ? queryRestaurant : null;
});

const backLink = computed(() => {
  if (restaurantSlug.value) {
    return { path: `/restaurant/${restaurantSlug.value}` };
  }

  return { path: '/' };
});

const backLabel = computed(() =>
  restaurantSlug.value ? 'Назад в приложение' : 'На главную',
);

async function loadDocument(): Promise<void> {
  if (!restaurantSlug.value) {
    documentFile.value = null;
    isLoading.value = false;

    return;
  }

  isLoading.value = true;

  try {
    const response = await client.get<RestaurantResourceData>(
      `/api/site/restaurants/${restaurantSlug.value}`,
    );

    documentFile.value = (
      response.data.files?.find((file) => file.pivot?.key === props.documentKey) ??
      null
    );
  } catch (error) {
    documentFile.value = null;
    console.error('Failed to load restaurant legal PDF', error);
  } finally {
    isLoading.value = false;
  }
}

watch(restaurantSlug, () => {
  void loadDocument();
});

onMounted(() => {
  void loadDocument();
});
</script>

<style scoped>
.site-legal-pdf-page {
  min-height: 100vh;
  padding: 2rem 1rem 3rem;
  background: linear-gradient(to bottom right, #eff6ff, #eef2ff, #faf5ff);
}

.site-legal-pdf-page__container {
  width: 100%;
  max-width: 72rem;
  margin: 0 auto;
}

.site-legal-pdf-page__back {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  color: #2563eb;
  font-weight: 500;
  text-decoration: none;
}

.site-legal-pdf-page__back:hover {
  color: #1d4ed8;
}

.site-legal-pdf-viewer {
  overflow: hidden;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 1rem;
  box-shadow: 0 24px 60px -30px rgb(15 23 42 / 0.35);
}

.site-legal-pdf-viewer--loading {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 18rem;
  color: #4b5563;
  font-size: 1rem;
}

.site-legal-pdf-viewer__header {
  padding: 2rem;
  background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
  border-bottom: 1px solid #e5e7eb;
}

.site-legal-pdf-viewer__eyebrow {
  margin: 0 0 0.75rem;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #4f46e5;
}

.site-legal-pdf-viewer__header-row {
  display: flex;
  gap: 1rem;
  justify-content: space-between;
  align-items: flex-start;
}

.site-legal-pdf-viewer__title {
  margin: 0;
  font-size: clamp(1.875rem, 3vw, 2.5rem);
  line-height: 1.1;
  color: #111827;
}

.site-legal-pdf-viewer__lead {
  max-width: 44rem;
  margin: 1rem 0 0;
  color: #4b5563;
  line-height: 1.7;
}

.site-legal-pdf-viewer__link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1rem;
  border-radius: 999px;
  background: #2563eb;
  color: #fff;
  text-decoration: none;
  white-space: nowrap;
}

.site-legal-pdf-viewer__link:hover {
  background: #1d4ed8;
}

.site-legal-pdf-viewer__frame {
  display: block;
  width: 100%;
  min-height: 78vh;
  border: 0;
  background: #f8fafc;
}

@media (max-width: 768px) {
  .site-legal-pdf-page {
    padding: 1.25rem 0.75rem 2rem;
  }

  .site-legal-pdf-viewer__header {
    padding: 1.25rem;
  }

  .site-legal-pdf-viewer__header-row {
    flex-direction: column;
  }

  .site-legal-pdf-viewer__frame {
    min-height: 68vh;
  }
}
</style>
