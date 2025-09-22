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
          <div ref="sheetRef" class="bottom-sheet" :class="[sheetClasses, props.customClass]" @click.stop>
            <div
              v-if="props.showHandle"
              class="bottom-sheet__handle"
              @touchstart.stop="handleTouchStart"
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
              @touchstart.stop="handleContentTouchStart"
              @touchmove.stop="handleContentTouchMove"
              @touchend.stop="handleContentTouchEnd"
              @touchcancel.stop="handleContentTouchEnd"
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
import { ref, computed, onMounted, onUnmounted } from 'vue';

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

const sheetClasses = computed(() => ['bottom-sheet--default', { 'bottom-sheet--gap': props.bottomGap }]);

const sheetRef = ref<HTMLElement>();
const contentRef = ref<HTMLElement>();
const isDragging = ref(false);
const startY = ref(0);
const currentY = ref(0);
const sheetHeight = ref(0);
const rafId = ref(0);
const hasScheduledFrame = ref(false);
const pendingTranslateY = ref(0);
// Бэкдроп без fade на закрытие — флага не требуется

const handleBackdropClick = () => {
  if (props.closableByBackdrop) {
    startClose();
  }
};

/**
 * Найти ближайший скроллируемый контейнер внутри contentRef
 */
const findScrollableAncestor = (startNode: EventTarget | null): HTMLElement | null => {
  if (!startNode || !(startNode instanceof Node) || !contentRef.value) return null;
  let el: Node | null = startNode as Node;
  while (el && el !== contentRef.value) {
    if (el instanceof HTMLElement && isElementScrollable(el)) return el;
    el = el.parentNode;
  }
  // Сам contentRef может быть скроллируемым
  if (contentRef.value && isElementScrollable(contentRef.value)) return contentRef.value;
  return null;
};

/**
 * Проверить, может ли элемент скроллиться по вертикали
 */
const isElementScrollable = (el: HTMLElement): boolean => {
  const style = window.getComputedStyle(el);
  const overflowY = style.overflowY;
  const canOverflow = overflowY === 'auto' || overflowY === 'scroll' || overflowY === 'overlay';
  return canOverflow && el.scrollHeight > el.clientHeight + 1;
};

/**
 * На верхней кромке скролла?
 */
const isScrolledToTop = (el: HTMLElement): boolean => el.scrollTop <= 0;

const activeScrollerRef = ref<HTMLElement | null>(null);
const contentTouchStartY = ref(0);

/**
 * Начать жест перетаскивания с хэндла
 */
const handleTouchStart = (event: TouchEvent) => {
  if (!props.closableBySwipe) return;
  if (event.touches.length > 1) return;

  isDragging.value = true;
  startY.value = event.touches[0].clientY;

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

  // Только вниз
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

  if (deltaY > distanceThreshold) {
    animateSheetToBottomThenClose();
    // оставляем isDragging=true до момента фактического закрытия,
    // чтобы startClose не сбрасывал inline-transform
    startY.value = 0;
    currentY.value = 0;
    return;
  } else {
    animateSheetToZero();
  }

  isDragging.value = false;
  startY.value = 0;
  currentY.value = 0;
};

/**
 * Контент: начало жеста. Решаем, кому отдать жест — скроллу или шиту
 */
const handleContentTouchStart = (event: TouchEvent) => {
  if (!props.closableBySwipe) return;
  if (event.touches.length > 1) return;
  contentTouchStartY.value = event.touches[0].clientY;
  activeScrollerRef.value = findScrollableAncestor(event.target);
};

/**
 * Контент: движение. Если свайп вниз и скролл на верхней кромке/нет скролла — перехватываем и двигаем шит
 */
const handleContentTouchMove = (event: TouchEvent) => {
  if (!props.closableBySwipe || !sheetRef.value) return;
  const y = event.touches[0].clientY;
  const deltaY = y - contentTouchStartY.value;

  // Свайп вверх — отдаем жест контенту
  if (deltaY <= 0) {
    // Если мы уже начали тянуть шит, откатим его
    if (isDragging.value) {
      animateSheetToZero();
      isDragging.value = false;
      startY.value = 0;
      currentY.value = 0;
    }
    return;
  }

  // Свайп вниз. Проверяем, можно ли закрывать (скролл сверху или нет скролла)
  const scroller = activeScrollerRef.value;
  const allowIntercept = !scroller || isScrolledToTop(scroller);
  if (!allowIntercept) return; // даем контенту скроллиться вверх до верха

  // Перехватываем: тянем шит
  if (event.cancelable) event.preventDefault();
  if (!isDragging.value) {
    isDragging.value = true;
    startY.value = y;
    currentY.value = y;
    sheetHeight.value = sheetRef.value.offsetHeight;
  } else {
    currentY.value = y;
  }

  const translateY = applyDragFriction(currentY.value - startY.value, sheetHeight.value);
  if (translateY > 0) scheduleTransform(translateY);
};

/**
 * Контент: завершение. Если тянули шит — завершаем как обычный свайп-закрытие
 */
const handleContentTouchEnd = () => {
  if (isDragging.value) {
    handleTouchEnd();
  }
  activeScrollerRef.value = null;
};

/**
 * Мышь: начать перетаскивание за хэндл
 */
const handleMouseDown = (event: MouseEvent) => {
  if (!props.closableBySwipe) return;
  if (event.button !== 0) return; // только ЛКМ

  isDragging.value = true;
  startY.value = event.clientY;
  if (sheetRef.value) sheetHeight.value = sheetRef.value.offsetHeight;

  // выключаем выделение текста во время перетаскивания
  document.body.style.userSelect = 'none';
  document.body.style.cursor = 'grabbing';

  window.addEventListener('mousemove', handleMouseMove);
  window.addEventListener('mouseup', handleMouseUp, { once: true });
};

/**
 * Мышь: движение
 */
const handleMouseMove = (event: MouseEvent) => {
  if (!isDragging.value || !props.closableBySwipe || !sheetRef.value) return;

  currentY.value = event.clientY;
  const deltaY = currentY.value - startY.value;

  // Только вниз
  if (deltaY > 0) {
    const translateY = applyDragFriction(deltaY, sheetHeight.value);
    scheduleTransform(translateY);
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
  document.body.style.userSelect = '';
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
  sheetRef.value.style.transition = 'transform 280ms cubic-bezier(0,0,0.2,1)';
  sheetRef.value.style.transform = 'translate3d(0, 0, 0)';
  const handleTransitionEnd = () => {
    if (!sheetRef.value) return;
    sheetRef.value.style.transition = '';
    sheetRef.value.removeEventListener('transitionend', handleTransitionEnd);
  };
  sheetRef.value.addEventListener('transitionend', handleTransitionEnd);
};

/**
 * Плавно докатить шит до низа и затем закрыть (для свайп-закрытия)
 */
const animateSheetToBottomThenClose = () => {
  if (!sheetRef.value) {
    isVisible.value = false;
    return;
  }
  cancelScheduledTransform();
  sheetRef.value.style.transition = 'transform 200ms cubic-bezier(0.4,0,1,1)';
  sheetRef.value.style.transform = 'translate3d(0, 100%, 0)';
  const handleTransitionEnd = () => {
    if (!sheetRef.value) return;
    sheetRef.value.style.transition = '';
    sheetRef.value.removeEventListener('transitionend', handleTransitionEnd);
    // После докатывания скрываем оверлей (fade бэкдропа)
    isVisible.value = false;
    isDragging.value = false;
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
  // Если закрываем не жестом — сначала докатываем вниз, затем скрываем бэкдроп
  if (!isDragging.value && sheetRef.value) {
    animateSheetToBottomThenClose();
    return;
  }
  isVisible.value = false;
};

const handleAfterLeave = () => {
  emit('close');
};

onMounted(() => {
  document.addEventListener('keydown', handleEscapeKey);

  const tg = window.Telegram?.WebApp;
  tg?.expand?.();
  // Пока открыт шит, выключаем вертикальный свайп (чтобы не уезжало вниз)
  tg?.setupSwipeBehavior?.({ allow_vertical_swipe: false });
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscapeKey);

  if (sheetRef.value) {
    sheetRef.value.style.transform = '';
  }

  const tg = window.Telegram?.WebApp;
  tg?.setupSwipeBehavior?.({ allow_vertical_swipe: true });
  cleanupMouseHandlers();
});
</script>

<style scoped>
.bottom-sheet__content {
  display: flex;
  justify-content: center;
  /* Изолируем скролл контента от родительского элемента */
  overscroll-behavior: contain;
  /* Только вертикальный панорамный жест */
  touch-action: pan-y;
}

/* Скруглённые верхние углы контейнера шита */
:deep(.bottom-sheet) {
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
  /* Предотвращаем случайные touch события на самом sheet */
  touch-action: none;
}

/* Handle zone - полный контроль */
:deep(.bottom-sheet__handle) {
  touch-action: none;
}

/* Content zone - только скролл */
:deep(.bottom-sheet__content) {
  touch-action: pan-y;
  overscroll-behavior: contain;
}

/* Transitions */
/* Backdrop: fade только на enter, без fade на leave */
.bottom-sheet-backdrop-enter-active {
  transition: opacity 200ms cubic-bezier(0, 0, 0.2, 1);
}
.bottom-sheet-backdrop-enter-from {
  opacity: 0;
}
.bottom-sheet-backdrop-enter-to {
  opacity: 1;
}

/* Sheet: slide-up/slide-down с зеркальными длительностями */
.bottom-sheet-enter-active {
  transition: transform 280ms cubic-bezier(0, 0, 0.2, 1);
}
.bottom-sheet-leave-active {
  transition: transform 200ms cubic-bezier(0.4, 0, 1, 1);
}
/* Убрали класс принудительного fadeout на закрытие */
.bottom-sheet-enter-from,
.bottom-sheet-leave-to {
  transform: translate3d(0, 100%, 0);
}
.bottom-sheet-enter-to,
.bottom-sheet-leave-from {
  transform: translate3d(0, 0, 0);
}
</style>
