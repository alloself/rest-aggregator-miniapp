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
            >
              <div class="bottom-sheet__handle-line"></div>
            </div>

            <div
              ref="contentRef"
              class="bottom-sheet__content"
              @touchstart="handleContentTouchStart"
              @touchmove="handleContentTouchMove"
              @touchend="handleContentTouchEnd"
              @touchcancel="handleContentTouchEnd"
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
  height?: number;
  customClass?: string;
  zIndex?: number;
  bottomGap?: boolean;
}

const props = withDefaults(defineProps<BottomSheetProps>(), {
  showHandle: true,
  closableByBackdrop: true,
  closableBySwipe: true,
  height: 60,
  customClass: '',
  zIndex: 1000,
  bottomGap: false,
});

const emit = defineEmits<{
  close: [];
}>();

const isVisible = ref(true);

const sheetStyles = computed<CSSProperties>(() => ({
  maxHeight: `${props.height}dvh`,
}));

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
// CSS-only позиционирование через dvh: JS не нужен
const rafId = ref(0);
const hasScheduledFrame = ref(false);
const pendingTranslateY = ref(0);

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
};

/**
 * Начало жеста на контенте: если контент вверху и жест вниз — начинаем тянуть шит
 */
const handleContentTouchStart = (event: TouchEvent) => {
  if (!props.closableBySwipe) return;

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
 * Движение по контенту: если вверху и тянут вниз — тянем шит, иначе даём скроллиться
 */
const handleContentTouchMove = (event: TouchEvent) => {
  if (!props.closableBySwipe || !sheetRef.value) return;

  currentY.value = event.touches[0].clientY;
  const deltaY = currentY.value - startY.value;

  // Если ещё не начали тянуть шит с контента
  if (!isDraggingFromContent.value) {
    // Начать перетаскивание только когда контент был вверху и жест вниз
    if (contentAtTopOnStart.value && deltaY > 0) {
      isDraggingFromContent.value = true;
      isDragging.value = true;
    } else {
      return; // Обычный скролл контента
    }
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
  if (!isDraggingFromContent.value) return;
  handleTouchEnd();
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

onMounted(() => {
  document.addEventListener('keydown', handleEscapeKey);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscapeKey);

  if (sheetRef.value) {
    sheetRef.value.style.transform = '';
  }
});
</script>

<style scoped>
.bottom-sheet__content {
  display: flex;
  justify-content: center;
}
</style>
