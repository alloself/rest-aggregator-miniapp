<template>
  <Teleport to="body">
    <transition name="bottom-sheet-backdrop" appear @after-leave="handleAfterLeave">
      <div
        class="bottom-sheet-backdrop"
        :style="{ zIndex: props.zIndex }"
        v-if="isVisible"
        @click="handleBackdropClick"
      >
        <transition name="bottom-sheet" appear>
          <div
            ref="sheetRef"
            class="bottom-sheet"
            :class="[sheetClasses, props.customClass]"
            :style="sheetStyles"
            @click.stop
          >
            <div
              v-if="props.showHandle"
              class="bottom-sheet__handle"
              @touchstart.stop.prevent="handleTouchStart"
              @touchmove.stop.prevent="handleTouchMove"
              @touchend.stop.prevent="handleTouchEnd"
              @touchcancel.stop.prevent="handleTouchEnd"
              @mousedown.stop.prevent="handleMouseDown"
            >
              <div class="bottom-sheet__handle-line"></div>
            </div>

            <div
              ref="contentRef"
              class="bottom-sheet__content"
              @touchstart.passive="handleContentTouchStart"
              @touchmove="handleContentTouchMove"
              @touchend="handleContentTouchEnd"
              @touchcancel="handleContentTouchEnd"
              @mousedown="handleContentMouseDown"
            >
              <slot />
            </div>
          </div>
        </transition>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, type CSSProperties } from 'vue';

interface BottomSheetProps {
  showHandle?: boolean;
  closableByBackdrop?: boolean;
  closableBySwipe?: boolean;
  contentSwipeToClose?: boolean;
  height?: number;
  customClass?: string;
  zIndex?: number;
  bottomGap?: boolean;
}

const props = withDefaults(defineProps<BottomSheetProps>(), {
  showHandle: true,
  closableByBackdrop: true,
  closableBySwipe: true,
  contentSwipeToClose: true,
  height: 60,
  customClass: '',
  zIndex: 1000,
  bottomGap: false,
});

const emit = defineEmits<{
  close: [];
}>();

const isVisible = ref(true);

// overlay height не нужен: backdrop всегда фиксирован и растянут на весь экран

const sheetStyles = computed<CSSProperties>(() => {
  const tg = window.Telegram?.WebApp;
  const vh = tg?.viewportHeight;
  const maxH = vh ? Math.min(vh, (props.height ?? 60) * window.innerHeight / 100) : undefined;
  // При наличии tg.viewportHeight ограничиваем шит высотой видимой области, иначе оставляем dvh
  return vh
    ? { maxHeight: `${Math.floor(maxH ?? vh)}px` }
    : { maxHeight: `${props.height}dvh` };
});

const sheetClasses = computed(() => ['bottom-sheet--default', { 'bottom-sheet--gap': props.bottomGap }]);

const sheetRef = ref<HTMLElement>();
const contentRef = ref<HTMLElement>();
const isDragging = ref(false);
const isDraggingFromContent = ref(false);
const startY = ref(0);
const currentY = ref(0);
const sheetHeight = ref(0);
const contentAtTopOnStart = ref(false);
const lastMoveY = ref(0);
const lastMoveTime = ref(0);
const velocityY = ref(0);
const rafId = ref(0);
const hasScheduledFrame = ref(false);
const pendingTranslateY = ref(0);
const dragOrigin = ref<null | 'handle' | 'content'>(null);
const prevUserSelect = ref('');

const handleBackdropClick = () => {
  if (props.closableByBackdrop) {
    startClose();
  }
};

/**
 * Начать жест перетаскивания с хэндла
 */
const handleTouchStart = (event: TouchEvent) => {
  if (!props.closableBySwipe) return;

  if (event.touches.length > 1) return;
  isDragging.value = true;
  startY.value = event.touches[0].clientY;
  lastMoveY.value = startY.value;
  lastMoveTime.value = performance.now();
  velocityY.value = 0;

  if (sheetRef.value) {
    sheetHeight.value = sheetRef.value.offsetHeight;
  }
};

/**
 * Обновить позицию шита во время перетаскивания (хэндл)
 */
const handleTouchMove = (event: TouchEvent) => {
  if (!isDragging.value || !props.closableBySwipe || !sheetRef.value) return;

  currentY.value = event.touches[0].clientY;
  const deltaY = currentY.value - startY.value;

  // Обновляем скорость
  const now = performance.now();
  const dy = currentY.value - lastMoveY.value;
  const dt = Math.max(1, now - lastMoveTime.value);
  velocityY.value = dy / dt; // px/ms
  lastMoveY.value = currentY.value;
  lastMoveTime.value = now;

  if (deltaY > 0) {
    const translateY = applyDragFriction(deltaY, sheetHeight.value);
    scheduleTransform(translateY);
  }
};

/**
 * Завершить перетаскивание: закрыть или откатить позицию
 */
const handleTouchEnd = () => {
  if (!isDragging.value || !props.closableBySwipe || !sheetRef.value) return;

  const deltaY = currentY.value - startY.value;
  const distanceThreshold = sheetHeight.value * 0.25;
  const velocityThreshold = 0.6; // px/ms ~= 600px/s

  if (deltaY > distanceThreshold || velocityY.value > velocityThreshold) {
    startClose();
  } else {
    animateSheetToZero();
  }

  isDragging.value = false;
  isDraggingFromContent.value = false;
  startY.value = 0;
  currentY.value = 0;
  velocityY.value = 0;
  dragOrigin.value = null;
};

/**
 * Начало жеста на контенте: если контент вверху и жест вниз — начинаем тянуть шит
 */
const handleContentTouchStart = (event: TouchEvent) => {
  if (!props.closableBySwipe || !props.contentSwipeToClose) return;

  if (!contentRef.value) return;
  contentAtTopOnStart.value = contentRef.value.scrollTop <= 0;
  isDraggingFromContent.value = false;
  isDragging.value = false;
  startY.value = event.touches[0].clientY;
  lastMoveY.value = startY.value;
  lastMoveTime.value = performance.now();
  velocityY.value = 0;
  if (sheetRef.value) {
    sheetHeight.value = sheetRef.value.offsetHeight;
  }
};

/**
 * Движение по контенту: начинаем тянуть шит ТОЛЬКО если контент у верхней границы и жест вниз.
 * В остальных случаях — позволяем нативный скролл контента.
 */
const handleContentTouchMove = (event: TouchEvent) => {
  if (!props.closableBySwipe || !props.contentSwipeToClose || !sheetRef.value || !contentRef.value) return;

  currentY.value = event.touches[0].clientY;
  const deltaY = currentY.value - startY.value;

  // Текущие позиции скролла
  const contentEl = contentRef.value;
  const atTop = contentEl.scrollTop <= 0;
  const atBottom = contentEl.scrollTop + contentEl.clientHeight >= contentEl.scrollHeight - 1;

  // Если ещё не начали тянуть шит с контента — решаем, нужно ли начать
  if (!isDraggingFromContent.value) {
    // Тянем шит только когда контент у верха и жест вниз (deltaY > 0)
    if (deltaY > 0 && atTop) {
      isDraggingFromContent.value = true;
      isDragging.value = true;
    } else {
      // В остальных случаях — обычный скролл контента
      return;
    }
  }

  // Если уже тянем шит, но пользователь повёл вверх — отпускаем шит и возвращаем скролл
  if (isDraggingFromContent.value && deltaY <= 0) {
    isDraggingFromContent.value = false;
    isDragging.value = false;
    scheduleTransform(0);
    return;
  }

  // Когда тянем шит — блокируем дефолт и всплытие, обновляем трансформ
  if (event.cancelable) event.preventDefault();
  event.stopPropagation();

  // Обновляем скорость
  const now = performance.now();
  const dy = currentY.value - lastMoveY.value;
  const dt = Math.max(1, now - lastMoveTime.value);
  velocityY.value = dy / dt; // px/ms
  lastMoveY.value = currentY.value;
  lastMoveTime.value = now;

  if (deltaY > 0) {
    const translateY = applyDragFriction(deltaY, sheetHeight.value);
    scheduleTransform(translateY);
  } else {
    scheduleTransform(0);
  }
};

/**
 * Завершение жеста на контенте: если тянули шит — финализируем, иначе ничего
 */
const handleContentTouchEnd = () => {
  if (!props.contentSwipeToClose) return;
  if (!isDraggingFromContent.value) return;
  handleTouchEnd();
};

/**
 * Мышь: начать перетаскивание за хэндл
 */
const handleMouseDown = (event: MouseEvent) => {
  if (!props.closableBySwipe) return;
  if (event.button !== 0) return; // только ЛКМ

  isDragging.value = true;
  dragOrigin.value = 'handle';
  startY.value = event.clientY;
  lastMoveY.value = startY.value;
  lastMoveTime.value = performance.now();
  velocityY.value = 0;
  if (sheetRef.value) sheetHeight.value = sheetRef.value.offsetHeight;

  // выключаем выделение текста во время перетаскивания
  prevUserSelect.value = document.body.style.userSelect;
  document.body.style.userSelect = 'none';
  document.body.style.cursor = 'grabbing';

  window.addEventListener('mousemove', handleMouseMove);
  window.addEventListener('mouseup', handleMouseUp, { once: true });
};

/**
 * Мышь: начало потенциального перетаскивания за контент
 */
const handleContentMouseDown = (event: MouseEvent) => {
  if (!props.closableBySwipe || !props.contentSwipeToClose) return;
  if (event.button !== 0) return; // только ЛКМ
  if (!contentRef.value) return;

  contentAtTopOnStart.value = contentRef.value.scrollTop <= 0;
  isDraggingFromContent.value = false;
  isDragging.value = false;
  dragOrigin.value = 'content';

  startY.value = event.clientY;
  lastMoveY.value = startY.value;
  lastMoveTime.value = performance.now();
  velocityY.value = 0;
  if (sheetRef.value) sheetHeight.value = sheetRef.value.offsetHeight;

  window.addEventListener('mousemove', handleMouseMove);
  window.addEventListener('mouseup', handleMouseUp, { once: true });
};

/**
 * Мышь: движение
 */
const handleMouseMove = (event: MouseEvent) => {
  if (!props.closableBySwipe || !sheetRef.value) return;

  currentY.value = event.clientY;
  const deltaY = currentY.value - startY.value;

  // скорость
  const now = performance.now();
  const dy = currentY.value - lastMoveY.value;
  const dt = Math.max(1, now - lastMoveTime.value);
  velocityY.value = dy / dt;
  lastMoveY.value = currentY.value;
  lastMoveTime.value = now;

  if (dragOrigin.value === 'handle') {
    if (!isDragging.value) return;
    if (deltaY > 0) {
      const translateY = applyDragFriction(deltaY, sheetHeight.value);
      scheduleTransform(translateY);
    }
    return;
  }

  if (dragOrigin.value === 'content') {
    if (!contentRef.value) return;
    const contentEl = contentRef.value;
    const atTop = contentEl.scrollTop <= 0;

    if (!isDraggingFromContent.value) {
      if (deltaY > 0 && (contentAtTopOnStart.value || atTop)) {
        isDraggingFromContent.value = true;
        isDragging.value = true;
        // как только начинаем тянуть шит — блокируем выделение/скролл
        prevUserSelect.value = document.body.style.userSelect;
        document.body.style.userSelect = 'none';
        document.body.style.cursor = 'grabbing';
        event.preventDefault();
      } else {
        return; // обычное поведение контента
      }
    }

    if (isDraggingFromContent.value && deltaY <= 0) {
      isDraggingFromContent.value = false;
      isDragging.value = false;
      scheduleTransform(0);
      document.body.style.userSelect = prevUserSelect.value;
      document.body.style.cursor = '';
      return;
    }

    event.preventDefault();
    event.stopPropagation();

    if (deltaY > 0) {
      const translateY = applyDragFriction(deltaY, sheetHeight.value);
      scheduleTransform(translateY);
    } else {
      scheduleTransform(0);
    }
  }
};

/**
 * Мышь: завершение
 */
const handleMouseUp = () => {
  if (!isDragging.value) {
    cleanupMouseHandlers();
    return;
  }
  handleTouchEnd();
  cleanupMouseHandlers();
};

const cleanupMouseHandlers = () => {
  window.removeEventListener('mousemove', handleMouseMove);
  document.body.style.userSelect = prevUserSelect.value;
  document.body.style.cursor = '';
};

/**
 * Применить «трение» при большом перетаскивании вниз, чтобы ощущалось как нативный резистивный скролл
 */
const applyDragFriction = (dragDistancePx: number, containerHeightPx: number): number => {
  if (dragDistancePx <= 0 || containerHeightPx <= 0) return 0;
  const ratio = Math.min(1, dragDistancePx / containerHeightPx);
  const frictionFactor = 0.35; // чем меньше, тем сильнее сопротивление
  return dragDistancePx * (1 - frictionFactor * ratio);
};

/**
 * Плавно вернуть шит на место после неуспешной попытки закрыть
 */
const animateSheetToZero = () => {
  if (!sheetRef.value) return;
  cancelScheduledTransform();
  sheetRef.value.style.transition = 'transform 250ms cubic-bezier(0.32,0.72,0,1)';
  sheetRef.value.style.transform = 'translate3d(0, 0, 0)';
  const handleTransitionEnd = () => {
    if (!sheetRef.value) return;
    sheetRef.value.style.transition = '';
    sheetRef.value.removeEventListener('transitionend', handleTransitionEnd);
  };
  sheetRef.value.addEventListener('transitionend', handleTransitionEnd);
};

/**
 * Запланировать обновление transform через requestAnimationFrame
 */
const scheduleTransform = (translateY: number) => {
  pendingTranslateY.value = Math.max(0, translateY);
  if (hasScheduledFrame.value) return;
  hasScheduledFrame.value = true;
  rafId.value = requestAnimationFrame(() => {
    hasScheduledFrame.value = false;
    if (!sheetRef.value) return;
    sheetRef.value.style.transform = `translate3d(0, ${pendingTranslateY.value}px, 0)`;
  });
};

/**
 * Отменить запланированный кадр, если есть
 */
const cancelScheduledTransform = () => {
  if (rafId.value) {
    cancelAnimationFrame(rafId.value);
    rafId.value = 0;
  }
  hasScheduledFrame.value = false;
};

const handleEscapeKey = (event: KeyboardEvent) => {
  if (event.key === 'Escape') {
    startClose();
  }
};

const startClose = () => {
  if (!isVisible.value) return;
  isVisible.value = false;
};

const handleAfterLeave = () => {
  emit('close');
};

// Типы Telegram доступны глобально из resources/site/ts/shared/types/telegram-webapp.d.ts

const handleViewportChange = () => {
  // триггерим обновление вычисляемых стилей
  // Vue сам пересчитает computed, т.к. обращение идёт к window.Telegram.WebApp внутри них
  // Ничего не делаем здесь – важно просто подписаться/отписаться корректно
};

onMounted(() => {
  document.addEventListener('keydown', handleEscapeKey);

  const tg = window.Telegram?.WebApp;
  try {
    tg?.expand?.();
    // Пока открыт шит, выключаем вертикальный свайп (чтобы не уезжало вниз)
    tg?.setupSwipeBehavior?.({ allow_vertical_swipe: false });
    tg?.onEvent?.('viewportChanged', handleViewportChange);
    tg?.onEvent?.('safeAreaChanged', handleViewportChange);
    tg?.onEvent?.('contentSafeAreaChanged', handleViewportChange);
  } catch {
    // ignore
  }
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscapeKey);

  if (sheetRef.value) {
    sheetRef.value.style.transform = '';
  }

  const tg = window.Telegram?.WebApp;
  try {
    tg?.setupSwipeBehavior?.({ allow_vertical_swipe: true });
    tg?.offEvent?.('viewportChanged', handleViewportChange);
    tg?.offEvent?.('safeAreaChanged', handleViewportChange);
    tg?.offEvent?.('contentSafeAreaChanged', handleViewportChange);
  } catch {
    // ignore
  }
  cleanupMouseHandlers();
});
</script>

<style scoped>
.bottom-sheet__content {
  display: flex;
  justify-content: center;
  /* предотвращает «протекание» скролла к вебвью Telegram и случайное сворачивание */
  overscroll-behavior-y: contain;
}

/* Скруглённые верхние углы контейнера шита */
:deep(.bottom-sheet) {
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
}
</style>
