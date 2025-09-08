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
      
      <!-- Кнопка репоста в сторис -->
      <button
        v-if="canShareToStory"
        class="news-card__share-story-btn"
        @click.stop="handleShareToStory"
        :title="shareButtonTitle"
      >
        <Icon 
          name="mdi:send" 
          :size="20" 
          class="shrink-0 text-white"
          color="#fff"
          aria-label="Поделиться в сторис"
        />
      </button>
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
import { useTelegramStoryShare } from '../../shared/composables/useTelegramStoryShare';
import type { News } from '@/shared';
import Icon from '@shared/ui/Icon.vue';

interface Props {
  item: News;
  slug: string;
}

const { item, slug } = defineProps<Props>();

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

// Логика для репоста в сторис
const { isAvailable, isVersionSupported, shareNewsToStory } = useTelegramStoryShare();

const canShareToStory = computed(() => {
  return (
    isAvailable.value && 
    isVersionSupported.value && 
    imageItems.value.length > 0
  );
});

const shareButtonTitle = computed(() => {
  if (!isAvailable.value) return 'Функция недоступна в этом браузере';
  if (!isVersionSupported.value) return 'Требуется обновление Telegram';
  if (imageItems.value.length === 0) return 'Нет изображений для публикации';
  return 'Поделиться в сторис Telegram';
});

const handleShareToStory = () => {
  if (!canShareToStory.value || imageItems.value.length === 0) return;
  
  const firstImage = imageItems.value[0];
  const success = shareNewsToStory(
    firstImage.url,
    title.value,
    slug,
    item.id
  );
  
  if (!success) {
    console.warn('Не удалось поделиться в сторис');
  }
};
</script>
