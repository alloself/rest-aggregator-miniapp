<template>
  <div class="media-viewer">
    <!-- Основной контейнер с медиа файлом -->
    <div class="media-viewer__main" ref="mainRef">
      <!-- Изображение -->
      <div v-if="currentMedia && isImage(currentMedia)" class="media-viewer__image-container">
        <img :src="currentMedia.url" :alt="currentMedia.name" class="media-viewer__image" loading="lazy" />
      </div>

      <div v-else-if="currentMedia && isIframeCompatible(currentMedia)" class="media-viewer__iframe-container">
        <iframe
          :src="getIframeUrl(currentMedia)"
          class="media-viewer__iframe"
          :title="currentMedia.name"
          loading="lazy"
          :style="{ height: iframeHeight + 'px' }"
        />
      </div>
      <button
        v-if="hasPrevious"
        class="media-viewer__nav-btn media-viewer__nav-btn--prev"
        @click="goToPrevious"
        :title="'Предыдущий файл'"
      >
        <Icon name="mdi:chevron-left" size="16" />
      </button>

      <button
        v-if="hasNext"
        class="media-viewer__nav-btn media-viewer__nav-btn--next"
        @click="goToNext"
        :title="'Следующий файл'"
      >
        <Icon name="mdi:chevron-right" size="16" />
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import Icon from './Icon.vue';
import type { File } from '@/shared';
import { isImage, isPdf, isIframeCompatible, getFileIcon } from '@shared/utils/media';

interface Props {
  /** Массив медиа файлов для просмотра */
  mediaFiles: File[];
  /** Начальный индекс для отображения */
  initialIndex?: number;
}

const props = withDefaults(defineProps<Props>(), {
  initialIndex: 0,
});

// Эмиты
const emit = defineEmits<{
  /** Когда изменился текущий файл */
  change: [media: File, index: number];
}>();

// Состояние
const currentIndex = ref(props.initialIndex);
const mainRef = ref<HTMLElement | null>(null);
const iframeHeight = ref<number>(600);


// Вычисляемые свойства
const currentMedia = computed(() => {
  return props.mediaFiles[currentIndex.value] || null;
});

const hasPrevious = computed(() => {
  return currentIndex.value > 0;
});

const hasNext = computed(() => {
  return currentIndex.value < props.mediaFiles.length - 1;
});

// Методы
const setCurrentIndex = (index: number) => {
  if (index >= 0 && index < props.mediaFiles.length) {
    currentIndex.value = index;
  }
};

/**
 * Получает URL для iframe с необходимыми параметрами
 * @param file - файл для отображения
 */
const getIframeUrl = (file: File): string => {
  const baseUrl = file.url;

  // Для PDF файлов добавляем параметры масштабирования
  if (isPdf(file)) {
    const separator = baseUrl.includes('?') ? '&' : '#';
    return `${baseUrl}${separator}view=FitH&toolbar=1&navpanes=0&scrollbar=1&page=1&zoom=page-width`;
  }

  // Для других типов файлов возвращаем оригинальный URL
  return baseUrl;
};

const goToPrevious = () => {
  if (hasPrevious.value) {
    setCurrentIndex(currentIndex.value - 1);
  }
};

const goToNext = () => {
  if (hasNext.value) {
    setCurrentIndex(currentIndex.value + 1);
  }
};

// Watchers
watch(currentIndex, (newIndex) => {
  const media = props.mediaFiles[newIndex];
  if (media) {
    emit('change', media, newIndex);
  }
});

watch(
  () => props.initialIndex,
  (newIndex) => {
    setCurrentIndex(newIndex);
  },
);

defineExpose({
  setCurrentIndex,
  goToPrevious,
  goToNext,
  currentIndex: () => currentIndex.value,
  currentMedia: () => currentMedia.value,
});
</script>

<style>
.media-viewer {
  display: flex;
  flex-direction: column;
  gap: 16px;
  width: 100%;
  height: 100%;
  min-width: 0;
}

.media-viewer__main {
  position: relative;
  width: 100%;
  flex: 1;
  border-radius: 15px;
  overflow: hidden;
  background-color: var(--color-secondary);
  display: flex;
  align-items: center;
  justify-content: center;
}

.media-viewer__image-container {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
}

.media-viewer__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

/* Iframe контейнер для PDF, HTML и других файлов */
.media-viewer__iframe-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
  width: 100%;
  height: 100%;
}

.media-viewer__iframe {
  width: 100%;
  height: 600px;
  border: none;
  border-radius: 8px;
  background: var(--color-white-pure);
  overflow: hidden;
  pointer-events: auto;
  touch-action: auto;
  /* Обеспечиваем возможность зума и взаимодействия с содержимым */
  user-select: auto;
  -webkit-user-select: auto;
  -moz-user-select: auto;
  -ms-user-select: auto;
}

.media-viewer__file-controls {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 12px 16px;
  background: var(--color-white-soft);
  border-radius: 8px;
  border: 1px solid var(--color-surface-default);
}

.media-viewer__file-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex: 1;
  min-width: 0;
}

.media-viewer__file-name {
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-accent);
  word-break: break-word;
  line-height: 1.3;
}

.media-viewer__file-type {
  font-size: 12px;
  color: var(--color-text-tertiary);
}

.media-viewer__download-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background: var(--color-secondary);
  color: var(--color-white-pure);
  text-decoration: none;
  border-radius: 6px;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.media-viewer__download-btn:hover {
  background: var(--color-secondary);
  opacity: 0.8;
  transform: scale(1.05);
}

/* Контейнер для других файлов (не iframe-совместимых) */
.media-viewer__file-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 24px;
  padding: 40px;
  text-align: center;
}

.media-viewer__file-preview {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}

.media-viewer__file-icon {
  color: var(--color-text-tertiary);
}

.media-viewer__file-download {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  background-color: var(--color-secondary);
  color: var(--color-white-pure);
  text-decoration: none;
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.media-viewer__file-download:hover {
  background-color: var(--color-secondary);
  opacity: 0.9;
  transform: translateY(-1px);
}

.media-viewer__pdf-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 24px;
  padding: 40px;
  text-align: center;
}

.media-viewer__pdf-preview {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}

.media-viewer__pdf-icon {
  color: #e53e3e;
}

.media-viewer__pdf-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.media-viewer__pdf-name {
  font-size: 16px;
  font-weight: 600;
  color: #1c1c1e;
  word-break: break-word;
}

.media-viewer__pdf-type {
  font-size: 14px;
  color: #8e8e93;
}

.media-viewer__pdf-download {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background-color: #007aff;
  color: white;
  text-decoration: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 500;
  transition: background-color 0.2s ease;
  cursor: pointer;
}

.media-viewer__pdf-download:hover {
  background-color: #0056cc;
}

.media-viewer__nav-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(4px);
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  z-index: 10;
}

.media-viewer__nav-btn:hover {
  background-color: rgba(255, 255, 255, 0.95);
  transform: translateY(-50%) scale(1.1);
}

.media-viewer__nav-btn--prev {
  left: 16px;
}

.media-viewer__nav-btn--next {
  right: 16px;
}

.media-viewer__thumbnails {
  display: flex;
  align-items: center;
  gap: clamp(8px, 2vw, 12px);
  overflow-x: auto;
  overflow-y: hidden;
  padding: 8px 0;
  min-width: 0; /* Позволяет контейнеру сжиматься */
  /* Скрыть скроллбар в webkit браузерах */
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE 10+ */
}

.media-viewer__thumbnail {
  width: clamp(60px, 12vw, 90px);
  height: clamp(60px, 12vw, 90px);
  flex-shrink: 0;
  border-radius: 8px;
  overflow: hidden;
  border: 2px solid transparent;
  cursor: pointer;
  transition: all 0.2s ease;
  background-color: var(--color-white-soft);
  display: flex;
  align-items: center;
  justify-content: center;
}

.media-viewer__thumbnail--active {
  border-color: #007aff;
  box-shadow: 0 4px 12px rgba(0, 122, 255, 0.3);
}

.media-viewer__thumbnail-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.media-viewer__thumbnail-file {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  background-color: var(--color-white-soft);
}

.media-viewer__thumbnail-icon {
  color: var(--color-text-tertiary);
}

/* Responsive */
@media (max-width: 768px) {
  .media-viewer__iframe {
    height: 500px;
  }
}

@media (max-width: 480px) {
  .media-viewer {
    gap: 16px;
  }

  .media-viewer__main {
    min-height: 400px;
  }

  .media-viewer__iframe {
    height: 450px;
  }

  .media-viewer__nav-btn {
    width: 32px;
    height: 32px;
  }

  .media-viewer__nav-btn--prev {
    left: 12px;
  }

  .media-viewer__nav-btn--next {
    right: 12px;
  }
}
</style>
