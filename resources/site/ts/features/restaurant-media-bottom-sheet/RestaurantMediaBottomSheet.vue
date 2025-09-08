<template>
  <div class="restaurant-media-bottom-sheet">
    <!-- Заголовок -->
    <div class="restaurant-media-bottom-sheet__header">
      <h2 class="restaurant-media-bottom-sheet__title">{{ mediaTitle }}</h2>
      <div class="restaurant-media-bottom-sheet__counter" v-if="mediaFiles.length > 1">
        {{ currentIndex + 1 }} из {{ mediaFiles.length }}
      </div>
    </div>

    <!-- Контейнер для MediaViewer -->
    <div class="restaurant-media-bottom-sheet__content">
      <MediaViewer
        v-if="mediaFiles.length > 0"
        :media-files="mediaFiles"
        :initial-index="initialIndex"
        @change="handleMediaChange"
      />

      <!-- Пустое состояние -->
      <div v-else class="restaurant-media-bottom-sheet__empty">
        <div class="restaurant-media-bottom-sheet__empty-icon">
          <Icon name="mdi:image-off" size="48" />
        </div>
        <div class="restaurant-media-bottom-sheet__empty-text">
          {{ emptyStateText }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { MediaViewer, Icon } from '@shared/ui';
import { getMediaType, isPdf } from '@shared/utils/media';
import type { File } from '@/shared';

export interface RestaurantMediaType {
  /** Тип медиа контента */
  type: 'photos' | 'menu' | 'bar';
  /** Название ресторана */
  restaurantName: string;
  /** Массив медиа файлов */
  files: File[];
  /** Начальный индекс для просмотра */
  initialIndex?: number;
  /** Показывать ли информацию о файле */
  showFileInfo?: boolean;
}

const props = withDefaults(defineProps<RestaurantMediaType>(), {
  initialIndex: 0,
  showFileInfo: false,
});

// Состояние
const currentIndex = ref(props.initialIndex);
const currentMedia = ref<File | null>(null);

// Вычисляемые свойства
const mediaFiles = computed(() => props.files);

const mediaTitle = computed(() => {
  const baseName = props.restaurantName;

  switch (props.type) {
    case 'photos':
      return `Фотографии ${baseName}`;
    case 'menu':
      return `Меню ${baseName}`;
    case 'bar':
      return `Карта бара ${baseName}`;
    default:
      return `Медиа ${baseName}`;
  }
});

const emptyStateText = computed(() => {
  switch (props.type) {
    case 'photos':
      return 'Фотографии ресторана пока не добавлены';
    case 'menu':
      return 'Меню ресторана пока не добавлено';
    case 'bar':
      return 'Карта бара пока не добавлена';
    default:
      return 'Медиа файлы не найдены';
  }
});

// Методы
const handleMediaChange = (media: File, index: number) => {
  currentMedia.value = media;
  currentIndex.value = index;
};

// Инициализация
if (mediaFiles.value.length > 0) {
  currentMedia.value = mediaFiles.value[props.initialIndex] || null;
}
</script>
