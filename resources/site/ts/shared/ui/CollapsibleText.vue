<template>
  <div class="collapsible-text">
    <div 
      class="collapsible-text__content"
      :class="{ 'collapsible-text__content--expanded': isExpanded }"
      ref="contentRef"
    >
      <div class="collapsible-text__text">
        <span 
          v-if="!isExpanded && shouldShowToggle"
          class="collapsible-text__text-truncated"
        >
          <span v-html="truncatedHtml"></span>... 
          <button 
            @click="expand"
            class="collapsible-text__toggle-inline"
          >
            {{ expandButtonText }}
          </button>
        </span>
        <span v-else v-html="sanitizedHtml"></span>
      </div>
      
      <!-- Кнопка свернуть отдельно снизу по центру -->
      <div 
        v-if="isExpanded && shouldShowToggle"
        class="collapsible-text__collapse-container"
      >
        <button 
          @click="collapse"
          class="collapsible-text__toggle-collapse"
        >
          {{ collapseButtonText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, nextTick } from 'vue'
import DOMPurify from 'dompurify'

interface Props {
  text: string
  characterLimit?: number
  expandButtonText?: string
  collapseButtonText?: string
  animationDuration?: number
}

const props = withDefaults(defineProps<Props>(), {
  characterLimit: 120,
  expandButtonText: 'еще',
  collapseButtonText: 'свернуть',
  animationDuration: 300
})

// Reactive data
const isExpanded = ref(false)
const contentRef = ref<HTMLElement>()

// Computed
const shouldShowToggle = computed(() => {
  return props.text.length > props.characterLimit
})

// Санация HTML через dompurify с белым списком тегов
const sanitizedHtml = computed(() => {
  const allowedTags = ['b','strong','i','em','u','s','p','br','ul','ol','li','h1','h2','h3','h4','h5','h6','span']
  return DOMPurify.sanitize(props.text, {
    ALLOWED_TAGS: allowedTags,
    ALLOWED_ATTR: [],
    USE_PROFILES: { html: true }
  }) as string
})

const truncatedHtml = computed(() => {
  const text = sanitizedHtml.value
  if (text.length <= props.characterLimit) return text
  const truncated = text.substring(0, props.characterLimit)
  const lastSpace = truncated.lastIndexOf(' ')
  return lastSpace > props.characterLimit * 0.8 ? truncated.substring(0, lastSpace) : truncated
})

// Methods
const expand = async () => {
  if (!contentRef.value) return
  
  const element = contentRef.value
  
  // Измеряем текущую высоту (свернутое состояние)
  const startHeight = element.scrollHeight
  
  // Предварительно измеряем полную высоту
  const originalExpanded = isExpanded.value
  isExpanded.value = true
  await nextTick()
  const endHeight = element.scrollHeight
  
  // Возвращаем свернутое состояние
  isExpanded.value = originalExpanded
  
  // Разворачиваем для отображения и начинаем анимацию
  isExpanded.value = true
  element.classList.add('expanding')
  element.style.height = `${startHeight}px`
  element.style.overflow = 'hidden'
  element.style.transition = `height ${props.animationDuration}ms cubic-bezier(0.4, 0, 0.2, 1)`
  
  // Запускаем анимацию расширения сразу
  requestAnimationFrame(() => {
    element.style.height = `${endHeight}px`
  })
  
  // Убираем стили и классы после анимации
  setTimeout(() => {
    element.classList.remove('expanding')
    element.style.height = ''
    element.style.overflow = ''
    element.style.transition = ''
  }, props.animationDuration)
}

const collapse = async () => {
  if (!contentRef.value) return
  
  const element = contentRef.value
  
  // Измеряем текущую высоту (развернутое состояние)
  const startHeight = element.scrollHeight
  
  // Предварительно измеряем высоту свернутого состояния
  // Временно сворачиваем только для измерения
  const originalExpanded = isExpanded.value
  isExpanded.value = false
  await nextTick()
  const endHeight = element.scrollHeight
  
  // Возвращаем развернутое состояние
  isExpanded.value = originalExpanded
  
  // Добавляем класс для анимации и начинаем анимацию немедленно
  element.classList.add('collapsing')
  element.style.height = `${startHeight}px`
  element.style.overflow = 'hidden'
  element.style.transition = `height ${props.animationDuration}ms cubic-bezier(0.4, 0, 0.2, 1)`
  
  // Запускаем анимацию сворачивания сразу
  requestAnimationFrame(() => {
    element.style.height = `${endHeight}px`
  })
  
  // После анимации окончательно сворачиваем
  setTimeout(() => {
    element.classList.remove('collapsing')
    isExpanded.value = false
    element.style.height = ''
    element.style.overflow = ''
    element.style.transition = ''
  }, props.animationDuration)
}
</script>

<style>
.collapsible-text {
  &__content {
    transition: all 0.3s ease-out;
  }

  &__text {
    margin: 0;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    font-size: 16px;
    font-weight: 400;
    color: #000000;
    line-height: 1.5;
  }

  &__text-truncated {
    display: inline;
  }

  &__toggle-inline {
    background: none;
    border: none;
    color: #949493;
    cursor: pointer;
    font-family: inherit;
    font-size: inherit;
    font-weight: inherit;
    padding: 0;
    margin-left: 2px;
    transition: color 0.2s ease;

    &:hover {
      color: #6a6a6a;
    }

    &:focus {
      outline: none;
    }
  }

  &__collapse-container {
    display: flex;
    justify-content: center;
    margin-top: 12px;
    opacity: 1;
    transform: translateY(0);
    transition: all 0.3s ease-out;
  }

  &__toggle-collapse {
    background: none;
    border: none;
    color: #949493;
    cursor: pointer;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    font-size: 16px;
    font-weight: 400;
    padding: 8px 16px;
    transition: color 0.2s ease;

    &:hover {
      color: #6a6a6a;
    }

    &:focus {
      outline: none;
    }
  }

  /* Анимация появления кнопки "свернуть" */
  &__content--expanded &__collapse-container {
    animation: fade-slide-in 0.3s ease-out;
  }
}

/* Анимации */
@keyframes fade-slide-in {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Дополнительные стили для плавной анимации текста */
.collapsible-text__content {
  &.expanding {
    overflow: hidden;
    transition: height 350ms cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  &.collapsing {
    overflow: hidden;
    transition: height 350ms cubic-bezier(0.4, 0, 0.2, 1);
  }
}
</style> 