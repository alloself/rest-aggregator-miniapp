<template>
  <Teleport to="body">
    <transition name="bottom-sheet-backdrop" appear>
      <div
        v-if="visible"
        class="bottom-sheet-backdrop"
        :style="{ zIndex: props.zIndex }"
        @click="handleBackdropClick"
        @wheel.prevent.self
        @touchmove.prevent.self
      >
        <transition name="bottom-sheet" appear>
          <div
            v-if="visible"
            ref="sheetRef"
            class="bottom-sheet"
            :class="[sheetClasses, props.customClass]"
            :style="sheetStyles"
            @click.stop
            @touchstart="handleTouchStart"
            @touchmove="handleTouchMove"
            @touchend="handleTouchEnd"
          >
            <!-- Handle для свайпа -->
            <div v-if="props.showHandle" class="bottom-sheet__handle">
              <div class="bottom-sheet__handle-line"></div>
            </div>

            <!-- Контент -->
            <div class="bottom-sheet__content">
              <slot />
            </div>
          </div>
        </transition>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

interface BottomSheetProps {
  visible: boolean;
  showHandle?: boolean;
  closableByBackdrop?: boolean;
  closableBySwipe?: boolean;
  height?: number;
  customClass?: string;
  zIndex?: number;
}

const props = withDefaults(defineProps<BottomSheetProps>(), {
  showHandle: true,
  closableByBackdrop: true,
  closableBySwipe: true,
  height: 60,
  customClass: '',
  zIndex: 1000,
});

const emit = defineEmits<{
  close: [];
}>();

// Стили для sheet
const sheetStyles = computed(() => ({
  maxHeight: `${props.height}vh`,
}));

const sheetClasses = computed(() => ['bottom-sheet--default']);

// Реактивные переменные для drag&drop
const sheetRef = ref<HTMLElement>();
const isDragging = ref(false);
const startY = ref(0);
const currentY = ref(0);
const sheetHeight = ref(0);

/**
 * Обработка клика по backdrop
 */
function handleBackdropClick() {
  if (props.closableByBackdrop) {
    emit('close');
  }
}

/**
 * Начало touch события
 */
function handleTouchStart(event: TouchEvent) {
  if (!props.closableBySwipe) return;

  isDragging.value = true;
  startY.value = event.touches[0].clientY;

  if (sheetRef.value) {
    sheetHeight.value = sheetRef.value.offsetHeight;
  }
}

/**
 * Движение touch события
 */
function handleTouchMove(event: TouchEvent) {
  if (!isDragging.value || !props.closableBySwipe || !sheetRef.value) return;

  currentY.value = event.touches[0].clientY;
  const deltaY = currentY.value - startY.value;

  // Только если свайпаем вниз
  if (deltaY > 0) {
    try {
      const transform = `translateY(${deltaY}px)`;
      sheetRef.value.style.transform = transform;
    } catch (error) {
      // Игнорируем ошибки DOM манипуляций
    }
  }
}

/**
 * Окончание touch события
 */
function handleTouchEnd() {
  if (!isDragging.value || !props.closableBySwipe || !sheetRef.value) return;

  const deltaY = currentY.value - startY.value;
  const threshold = sheetHeight.value * 0.3; // 30% от высоты

  if (deltaY > threshold) {
    emit('close');
  } else {
    try {
      // Возвращаем на место
      sheetRef.value.style.transform = 'translateY(0)';
    } catch (error) {
      // Игнорируем ошибки DOM манипуляций
    }
  }

  isDragging.value = false;
  startY.value = 0;
  currentY.value = 0;
}

/**
 * Обработка клавиши Escape
 */
function handleEscapeKey(event: KeyboardEvent) {
  if (event.key === 'Escape' && props.visible) {
    emit('close');
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscapeKey);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscapeKey);

  // Очищаем стили при размонтировании
  try {
    if (sheetRef.value) {
      sheetRef.value.style.transform = '';
    }
  } catch (error) {
    // Игнорируем ошибки DOM манипуляций при размонтировании
  }
  // Разблокируем скролл, если он был заблокирован этим компонентом
  try {
    unlockBodyScroll();
  } catch (error) {}
});

/**
 * Блокирует прокрутку body, фиксируя текущую позицию прокрутки.
 */
function lockBodyScroll() {
  const body = document.body;
  const currentCount = Number(body.getAttribute('data-scroll-lock-count') || '0');
  body.setAttribute('data-scroll-lock-count', String(currentCount + 1));
  if (currentCount > 0) return;

  const scrollY = window.scrollY || window.pageYOffset || 0;
  body.setAttribute('data-scroll-lock-y', String(scrollY));
  body.style.position = 'fixed';
  body.style.top = `-${scrollY}px`;
  body.style.left = '0';
  body.style.right = '0';
  body.style.width = '100%';
  body.style.overflow = 'hidden';
}

/**
 * Снимает блокировку прокрутки body и восстанавливает позицию прокрутки.
 */
function unlockBodyScroll() {
  const body = document.body;
  const currentCount = Number(body.getAttribute('data-scroll-lock-count') || '0');
  const next = Math.max(0, currentCount - 1);
  body.setAttribute('data-scroll-lock-count', String(next));
  if (next > 0) return;

  const yAttr = body.getAttribute('data-scroll-lock-y') || '0';
  const y = Number(yAttr) || 0;

  body.style.position = '';
  body.style.top = '';
  body.style.left = '';
  body.style.right = '';
  body.style.width = '';
  body.style.overflow = '';

  body.removeAttribute('data-scroll-lock-y');
  window.scrollTo(0, y);
}

/**
 * Следим за видимостью шита и блокируем/разблокируем прокрутку страницы.
 */
watch(
  () => props.visible,
  (isVisible) => {
    if (isVisible) {
      lockBodyScroll();
      return;
    }
    unlockBodyScroll();
  },
  { immediate: true },
);
</script>

<style>
/* Bottom Sheet Component - PostCSS Nested */
.bottom-sheet-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: flex-end;
  backdrop-filter: blur(2px);
}

.bottom-sheet {
  width: 100%;
  background-color: #fffefd;
  border-radius: 20px 20px 0 0;
  box-shadow: 4px 4px 16px 8px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  position: relative;

  &__handle {
    display: flex;
    justify-content: center;
    padding: 10px;
    cursor: grab;

    &:active {
      cursor: grabbing;
    }
  }

  &__handle-line {
    width: 48px;
    height: 5px;
    background-color: rgba(0, 0, 0, 0.15);
    border-radius: 2.5px;
  }

  &__content {
    flex: 1;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    overscroll-behavior: contain;
  }
}

/* Анимации */
.bottom-sheet-backdrop-enter-active,
.bottom-sheet-backdrop-leave-active {
  transition: opacity 0.3s ease;
}

.bottom-sheet-backdrop-enter-from,
.bottom-sheet-backdrop-leave-to {
  opacity: 0;
}

.bottom-sheet-enter-active {
  transition: transform 0.3s cubic-bezier(0.32, 0.72, 0, 1);
}

.bottom-sheet-leave-active {
  transition: transform 0.25s cubic-bezier(0.32, 0.72, 0, 1);
}

.bottom-sheet-enter-from,
.bottom-sheet-leave-to {
  transform: translateY(100%);
}

/* Адаптивность */
@media (max-width: 768px) {
  .bottom-sheet {
    border-radius: 16px 16px 0 0;
  }
}

/* Стили для iOS */
@supports (-webkit-touch-callout: none) {
  .bottom-sheet {
    /* Убираем подсветку при тапе на iOS */
    -webkit-tap-highlight-color: transparent;
  }
}
</style>
