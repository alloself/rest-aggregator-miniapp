<template>
  <div class="pdf-bottom-sheet">
    <div class="pdf-bottom-sheet__header">
      <div class="pdf-bottom-sheet__title">{{ title }}</div>
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

<style scoped>
.pdf-bottom-sheet {
  width: 100% !important;
  height: 100% !important;
  margin: 0 !important;
  padding: 0 !important;
  box-sizing: border-box;
}

.pdf-bottom-sheet__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  border-bottom: 1px solid #e0e0e0;
  background: white;
  position: sticky;
  top: 0;
  z-index: 1;
}

.pdf-bottom-sheet__title {
  font-weight: 600;
  font-size: 16px;
  color: #333;
}

.pdf-bottom-sheet__download {
  padding: 8px 16px;
  background: #007bff;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  transition: background 0.2s ease;
}

.pdf-bottom-sheet__download:hover {
  background: #0056b3;
}

.pdf-bottom-sheet__viewer {
  width: 100% !important;
  height: calc(100vh - 120px);
  border: none !important;
  margin: 0 !important;
  padding: 0 !important;
  display: block;
  box-sizing: border-box;
}

.pdf-bottom-sheet__empty {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 200px;
  color: #666;
  font-size: 16px;
}
</style>
