<template>
  <div class="infinite-scroll">
    <div v-if="loading" class="infinite-scroll__state">Загрузка…</div>
    <div v-else-if="error" class="infinite-scroll__state">{{ error }}</div>
    <div v-else class="infinite-scroll__content">
      <slot />
      <div v-if="hasMore" ref="sentinel" class="infinite-scroll__sentinel" aria-hidden="true" />
      <div v-if="isLoadingMore" class="infinite-scroll__state">Загрузка…</div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, useTemplateRef, watch } from 'vue';

/**
 * Компонент-обертка для реализации бесконечной прокрутки
 * Автоматически загружает новые данные при достижении конца списка
 */
interface Props {
  /** Состояние загрузки */
  loading: boolean;
  /** Сообщение об ошибке */
  error: string | null;
  /** Есть ли еще данные для загрузки */
  hasMore: boolean;
  /** Состояние загрузки дополнительных данных */
  isLoadingMore: boolean;
  /** Функция для загрузки дополнительных данных */
  onLoadMore: () => void;
}

const { loading, error, hasMore, isLoadingMore, onLoadMore } = defineProps<Props>();

const sentinel = useTemplateRef<HTMLDivElement>('sentinel');

let observer: IntersectionObserver | null = null;

onMounted(() => {
  observer = new IntersectionObserver(
    (entries) => {
      const [entry] = entries;
      if (!entry?.isIntersecting) return;
      if (loading || isLoadingMore || !hasMore) return;
      onLoadMore();
    },
    { root: null, rootMargin: '200px 0px', threshold: 0 },
  );

  if (sentinel.value) observer.observe(sentinel.value);
});

watch(
  sentinel,
  (el, prevEl) => {
    if (!observer) return;
    if (prevEl) observer.unobserve(prevEl);
    if (el && hasMore) observer.observe(el);
  },
  { flush: 'post' },
);

watch(
  () => hasMore,
  (val) => {
    if (!observer || !sentinel.value) return;
    if (val) observer.observe(sentinel.value);
    else observer.unobserve(sentinel.value);
  },
);

onUnmounted(() => {
  if (observer) observer.disconnect();
  observer = null;
});
</script>

<style scoped>
.infinite-scroll {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.infinite-scroll__content {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.infinite-scroll__state {
  color: rgba(0, 0, 0, 0.6);
  font-size: 14px;
}

.infinite-scroll__sentinel {
  height: 1px;
}
</style>
