<template>
  <div class="pdf-bottom-sheet">
    <div class="pdf-bottom-sheet__header">
      <div class="pdf-bottom-sheet__title">{{ title }}</div>
      <a v-if="url" class="pdf-bottom-sheet__download" :href="url" target="_blank" rel="noopener noreferrer">
        Скачать PDF
      </a>
    </div>

    <div v-if="!url" class="pdf-bottom-sheet__empty">Файл не найден</div>
    <iframe v-else class="pdf-bottom-sheet__viewer" :src="iframeSrc" title="PDF" />
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
