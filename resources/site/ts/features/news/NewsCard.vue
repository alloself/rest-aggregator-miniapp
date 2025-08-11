<template>
  <article class="news-card">
    <div class="news-card__media" v-if="imageItems.length > 0">
      <RestaurantHeroCarousel :images="imageItems" :autoplay="4000" :show-pagination="true" height="220px" />
    </div>

    <div class="news-card__body">
      <h3 class="news-card__title">{{ title }}</h3>

      <CollapsibleText
        v-if="description"
        class="news-card__description"
        :text="description"
        :character-limit="150"
        expand-button-text="еще"
        collapse-button-text="свернуть"
        :animation-duration="250"
      />

      <div class="news-card__date" v-if="createdAt">{{ formattedDate }}</div>
    </div>
  </article>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import CollapsibleText from '@site/ts/components/CollapsibleText.vue';
import { RestaurantHeroCarousel } from '@/shared/ui';
import type { News } from '@/shared';

interface Props {
  item: News;
}

const { item } = defineProps<Props>();

const title = computed(() => item.title);
const description = computed(() => item.description);
const createdAt = computed(() => item.created_at ?? undefined);

const imageItems = computed(() => {
  const files = item.images ?? [];
  return files
    .map((file) => {
      const url = typeof file.url === 'string' ? file.url : String(file.url);
      return { url, alt: item.title };
    })
    .filter((file) => Boolean(file.url));
});

const formattedDate = computed(() => {
  if (!createdAt.value) return '';

  const date = new Date(createdAt.value);
  return date.toLocaleDateString('ru-RU');
});
</script>

<style scoped>
.news-card {
  display: flex;
  flex-direction: column;
  background: #ffffff;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
}

.news-card__media {
  width: 100%;
}

.news-card__body {
  padding: 16px;
}

.news-card__title {
  margin: 0 0 8px 0;
  font-size: 18px;
  font-weight: 600;
  line-height: 1.3;
}

.news-card__description {
  margin: 0 0 12px 0;
}

.news-card__date {
  margin-top: 4px;
  text-align: right;
  font-size: 12px;
  color: rgba(0, 0, 0, 0.45);
}
</style>
