<template>
  <div class="collapsible-text">
    <div
      class="collapsible-text__content"
      :class="{ 'collapsible-text__content--expanded': isExpanded }"
      ref="contentRef"
    >
      <div class="collapsible-text__text">
        <span v-if="!isExpanded && shouldShowToggle" class="collapsible-text__text-truncated">
          <span v-html="truncatedHtml"></span>...
          <button @click="expand" class="collapsible-text__toggle-inline">
            {{ expandButtonText }}
          </button>
        </span>
        <span v-else v-html="sanitizedHtml"></span>
      </div>
      <div v-if="isExpanded && shouldShowToggle" class="collapsible-text__collapse-container">
        <button @click="collapse" class="collapsible-text__toggle-collapse">
          {{ collapseButtonText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, nextTick } from 'vue';
import DOMPurify from 'dompurify';

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

const shouldShowToggle = computed(() => {
  return props.text.length > props.characterLimit;
});

const sanitizedHtml = computed(() => {
  const allowedTags = [
    'b',
    'strong',
    'i',
    'em',
    'u',
    's',
    'p',
    'br',
    'ul',
    'ol',
    'li',
    'h1',
    'h2',
    'h3',
    'h4',
    'h5',
    'h6',
    'span',
  ];
  const sanitized = DOMPurify.sanitize(props.text, {
    ALLOWED_TAGS: allowedTags,
    ALLOWED_ATTR: [],
    USE_PROFILES: { html: true },
  });
  return sanitized.replace(/&nbsp;|&#160;/g, ' ').replace(/\u00A0/g, ' ');
});

const truncatedHtml = computed(() => {
  const text = sanitizedHtml.value;
  if (text.length <= props.characterLimit) return text;
  const truncated = text.substring(0, props.characterLimit);
  const lastSpace = truncated.lastIndexOf(' ');
  return lastSpace > props.characterLimit * 0.8 ? truncated.substring(0, lastSpace) : truncated;
});

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

const collapse = async () => {
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
}
</style>
