<template>
  <div class="collapsible-text">
    <div
      class="collapsible-text__content"
      :class="{ 'collapsible-text__content--expanded': isExpanded }"
      ref="contentRef"
    >
      <div class="collapsible-text__text">
        <template v-if="!isExpanded && shouldShowToggle">
          <span class="collapsible-text__collapsed">{{ collapsed_text }}</span
          ><span class="collapsible-text__ellipsis">… </span>
          <span
            class="collapsible-text__more"
            role="button"
            tabindex="0"
            @click="expand"
            @keydown.enter.prevent="expand"
            @keydown.space.prevent="expand"
          >
            {{ expandButtonText }}
          </span>
        </template>
        <span v-else v-html="sanitizedHtml"></span>
      </div>

      <!-- Скрытый элемент для измерения количества строк полного текста -->
      <div ref="measureRef" class="collapsible-text__measure" aria-hidden="true" v-html="sanitizedHtml"></div>

      <div v-if="isExpanded && shouldShowToggle" class="collapsible-text__collapse-container">
        <button @click="collapse" class="collapsible-text__toggle-collapse">
          {{ collapseButtonText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, nextTick, onMounted, onUnmounted, watch } from 'vue';
import { sanitizeHtml } from '../lib/html';
import { useHapticFeedback } from '../lib/composables';

interface Props {
  text: string;
  characterLimit?: number;
  expandButtonText?: string;
  collapseButtonText?: string;
  animationDuration?: number;
}

const props = withDefaults(defineProps<Props>(), {
  characterLimit: 120,
  expandButtonText: 'еще',
  collapseButtonText: 'свернуть',
  animationDuration: 300,
});

const isExpanded = ref(false);
const contentRef = ref<HTMLElement>();
const measureRef = ref<HTMLElement>();
const shouldShowToggle = ref(false);

const sanitizedHtml = computed(() => sanitizeHtml(props.text));
const plain_text = computed(() => {
  const div = document.createElement('div');
  div.innerHTML = sanitizedHtml.value;
  return div.textContent || div.innerText || '';
});

/**
 * Вернуть числовое значение line-height для элемента.
 */
const getNumericLineHeight = (el: HTMLElement): number => {
  const style = window.getComputedStyle(el);
  const font_size = parseFloat(style.fontSize) || 16;
  const lh = style.lineHeight;
  const parsed = parseFloat(lh);
  if (Number.isFinite(parsed)) return parsed;
  return font_size * 1.2;
};

/**
 * Пересчитать, превышает ли текст 3 строки, чтобы решить, показывать ли кнопки.
 */
const updateToggleVisibility = async () => {
  await nextTick();
  const el = measureRef.value || contentRef.value;
  if (!el) return;
  // Ширину задаем явно для стабильных измерений, если есть контейнер
  if (measureRef.value && contentRef.value) {
    measureRef.value.style.width = `${contentRef.value.clientWidth}px`;
  }
  const line_height = getNumericLineHeight(el);
  if (line_height <= 0) {
    shouldShowToggle.value = false;
    return;
  }
  const lines = Math.round(el.scrollHeight / line_height);
  shouldShowToggle.value = lines > 3;
};
/**
 * Текст, обрезанный ровно до 3 строк для показа с инлайн-"ещё".
 */
const collapsed_text = computed(() => {
  if (!contentRef.value) return plain_text.value;
  if (!shouldShowToggle.value) return plain_text.value;
  const container = contentRef.value;
  const temp = document.createElement('span');
  temp.style.visibility = 'hidden';
  temp.style.position = 'fixed';
  temp.style.left = '-99999px';
  temp.style.whiteSpace = 'normal';
  temp.style.wordBreak = 'break-word';
  temp.style.overflowWrap = 'anywhere';
  temp.style.hyphens = 'auto';
  temp.style.width = `${container.clientWidth}px`;
  const style = window.getComputedStyle(container);
  temp.style.font = style.font;
  temp.style.lineHeight = style.lineHeight;
  document.body.appendChild(temp);

  const text = plain_text.value;
  let lo = 0;
  let hi = text.length;
  const target_lines = 3;
  const line_height = getNumericLineHeight(container);
  const max_height = target_lines * line_height;

  // бинпоиск по длине строки, чтобы уложиться в 3 строки с запасом под эллипсис и слово "еще"
  while (lo < hi) {
    const mid = Math.ceil((lo + hi) / 2);
    temp.textContent = text.slice(0, mid) + '… ' + props.expandButtonText;
    if (temp.scrollHeight <= max_height) {
      lo = mid;
    } else {
      hi = mid - 1;
    }
  }

  // безопасно обрезаем по последнему пробелу, чтобы не резать слово
  let clipped = text.slice(0, lo);
  const last_space = clipped.lastIndexOf(' ');
  if (last_space > clipped.length * 0.6) {
    clipped = clipped.slice(0, last_space);
  }

  document.body.removeChild(temp);
  return clipped;
});

onMounted(() => {
  updateToggleVisibility();
  window.addEventListener('resize', updateToggleVisibility);
});

onUnmounted(() => {
  window.removeEventListener('resize', updateToggleVisibility);
});

watch(
  () => props.text,
  () => {
    updateToggleVisibility();
  },
);

const expand = async () => {
  if (!contentRef.value) return;

  const element = contentRef.value;

  const startHeight = element.scrollHeight;

  const originalExpanded = isExpanded.value;
  isExpanded.value = true;
  await nextTick();
  const endHeight = element.scrollHeight;

  isExpanded.value = originalExpanded;

  isExpanded.value = true;
  element.classList.add('expanding');
  element.style.height = `${startHeight}px`;
  element.style.overflow = 'hidden';
  element.style.transition = `height ${props.animationDuration}ms cubic-bezier(0.4, 0, 0.2, 1)`;

  requestAnimationFrame(() => {
    element.style.height = `${endHeight}px`;
  });

  setTimeout(() => {
    element.classList.remove('expanding');
    element.style.height = '';
    element.style.overflow = '';
    element.style.transition = '';
  }, props.animationDuration);
};

const { trigger } = useHapticFeedback();

const collapse = async () => {
  trigger();
  if (!contentRef.value) return;

  const element = contentRef.value;

  const startHeight = element.scrollHeight;

  const originalExpanded = isExpanded.value;
  isExpanded.value = false;
  await nextTick();
  const endHeight = element.scrollHeight;

  isExpanded.value = originalExpanded;

  element.classList.add('collapsing');
  element.style.height = `${startHeight}px`;
  element.style.overflow = 'hidden';
  element.style.transition = `height ${props.animationDuration}ms cubic-bezier(0.4, 0, 0.2, 1)`;

  requestAnimationFrame(() => {
    element.style.height = `${endHeight}px`;
  });

  setTimeout(() => {
    element.classList.remove('collapsing');
    isExpanded.value = false;
    element.style.height = '';
    element.style.overflow = '';
    element.style.transition = '';
  }, props.animationDuration);
};
</script>

<style scoped>
.collapsible-text__text {
  overflow-wrap: anywhere;
  word-break: break-word;
  white-space: normal;
  hyphens: auto;
  position: relative;
}

.collapsible-text__text--clamped {
  display: block;
  overflow: hidden;
  max-height: calc(3 * 1em * 1.2);
}

.collapsible-text__collapse-container {
  margin-top: 8px;
}
.collapsible-text__toggle-collapse {
  color: var(--color-text-secondary);
}
.collapsible-text__toggle-collapse:hover {
  color: var(--color-text-muted);
}

.collapsible-text__measure {
  position: fixed;
  left: -99999px;
  top: 0;
  visibility: hidden;
  pointer-events: none;
  white-space: normal;
  overflow-wrap: anywhere;
  word-break: break-word;
  hyphens: auto;
}

.collapsible-text__ellipsis {
  opacity: 0.7;
}
.collapsible-text__more {
  cursor: pointer;
  user-select: none;
  color: var(--color-text-secondary);
}
.collapsible-text__more:hover {
  color: var(--color-text-muted);
}
</style>
