<template>
  <Teleport to="body">
    <transition name="bottom-sheet-backdrop" appear @after-leave="handleAfterLeave">
      <div
        class="bottom-sheet-backdrop"
        :style="{ zIndex: props.zIndex }"
        v-if="isVisible"
        @click="handleBackdropClick"
        @wheel.prevent.self
        @touchmove.prevent.self
      >
        <transition name="bottom-sheet" appear>
          <div
            ref="sheetRef"
            class="bottom-sheet"
            :class="[sheetClasses, props.customClass]"
            :style="sheetStyles"
            @click.stop
            @touchstart="handleTouchStart"
            @touchmove="handleTouchMove"
            @touchend="handleTouchEnd"
          >
            <div v-if="props.showHandle" class="bottom-sheet__handle">
              <div class="bottom-sheet__handle-line"></div>
            </div>

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

const sheetStyles = computed(() => ({
  maxHeight: `${props.height}vh`,
}));

const sheetClasses = computed(() => ['bottom-sheet--default', { 'pb-4': props.bottomGap }]);

const sheetRef = ref<HTMLElement>();
const isDragging = ref(false);
const startY = ref(0);
const currentY = ref(0);
const sheetHeight = ref(0);

const handleBackdropClick = () => {
  if (props.closableByBackdrop) {
    startClose();
  }
};

const handleTouchStart = (event: TouchEvent) => {
  if (!props.closableBySwipe) return;

  isDragging.value = true;
  startY.value = event.touches[0].clientY;

  if (sheetRef.value) {
    sheetHeight.value = sheetRef.value.offsetHeight;
  }
};

const handleTouchMove = (event: TouchEvent) => {
  if (!isDragging.value || !props.closableBySwipe || !sheetRef.value) return;

  currentY.value = event.touches[0].clientY;
  const deltaY = currentY.value - startY.value;

  if (deltaY > 0) {
    const transform = `translateY(${deltaY}px)`;
    sheetRef.value.style.transform = transform;
  }
};

const handleTouchEnd = () => {
  if (!isDragging.value || !props.closableBySwipe || !sheetRef.value) return;

  const deltaY = currentY.value - startY.value;
  const threshold = sheetHeight.value * 0.3;

  if (deltaY > threshold) {
    startClose();
  } else {
    sheetRef.value.style.transform = 'translateY(0)';
  }

  isDragging.value = false;
  startY.value = 0;
  currentY.value = 0;
};

const handleEscapeKey = (event: KeyboardEvent) => {
  if (event.key === 'Escape') {
    startClose();
  }
}

const startClose = () => {
  if (!isVisible.value) return;
  isVisible.value = false;
}

const handleAfterLeave = () => {
  emit('close');
}

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
