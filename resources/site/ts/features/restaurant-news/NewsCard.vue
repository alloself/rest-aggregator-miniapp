<template>
  <article class="news-card">
    <div class="news-card__media" v-if="imageItems.length > 0">
      <HeroCarousel
        :images="imageItems"
        :autoplay="4000"
        :show-pagination="imageItems.length > 1"
        height="220px"
        border-radius="20px"
        pagination-bottom="24px"
      />
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
import { dayjs } from '@site/ts/shared/lib/dayjs';
import HeroCarousel from '@shared/ui/HeroCarousel.vue';
import CollapsibleText from '../../shared/ui/CollapsibleText.vue';
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
  return dayjs(createdAt.value).fromNow();
});
</script>
