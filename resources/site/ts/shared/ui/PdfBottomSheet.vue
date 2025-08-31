<template>
  <div class="pdf-bottom-sheet">
    <div class="pdf-bottom-sheet__header">
      <div class="pdf-bottom-sheet__title">{{ title }}</div>
      <a
        v-if="url"
        class="pdf-bottom-sheet__download"
        :href="url"
        target="_blank"
        rel="noopener noreferrer"
      >Скачать PDF</a>
    </div>

    <div v-if="!url" class="pdf-bottom-sheet__empty">Файл не найден</div>
    <iframe
      v-else
      class="pdf-bottom-sheet__viewer"
      :src="iframeSrc"
      title="PDF"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface PdfBottomSheetProps {
  url: string;
  title?: string;
}

const props = withDefaults(defineProps<PdfBottomSheetProps>(), {
  title: 'Документ',
});

// Некоторые браузеры рендерят PDF лучше с #view=fitH
const iframeSrc = computed(() => `${props.url}#view=fitH`);
</script>

<style>
.pdf-bottom-sheet {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.pdf-bottom-sheet__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 14px;
}

.pdf-bottom-sheet__title {
  font-size: 14px;
  font-weight: 600;
}

.pdf-bottom-sheet__download {
  font-size: 12px;
  color: var(--primary-600);
  text-decoration: none;
  cursor: pointer;
}

.pdf-bottom-sheet__empty {
  padding: 16px;
  font-size: 13px;
}

.pdf-bottom-sheet__viewer {
  flex: 1;
  width: 100%;
  border: none;
}
</style>


