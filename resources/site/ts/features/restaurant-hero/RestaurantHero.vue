<template>
  <div class="restaurant-hero">
    <!-- Main image -->
    <AppImage
      :src="currentImage.src"
      :alt="currentImage.alt"
      class="restaurant-hero__image"
      object-fit="cover"
      border-radius="xl"
      @load="handleImageLoad"
    />
    
    <!-- Slide indicators -->
    <div v-if="images.length > 1" class="restaurant-hero__indicators">
      <button
        v-for="(image, index) in images"
        :key="index"
        :class="[
          'restaurant-hero__indicator',
          { 'restaurant-hero__indicator--active': index === currentIndex }
        ]"
        @click="setCurrentImage(index)"
        :aria-label="`Перейти к изображению ${index + 1}`"
      />
    </div>
    
    <!-- Navigation arrows for touch swipe (optional) -->
    <button
      v-if="images.length > 1 && showArrows"
      class="restaurant-hero__nav restaurant-hero__nav--prev"
      @click="previousImage"
      aria-label="Предыдущее изображение"
    >
      <i class="pi pi-chevron-left"></i>
    </button>
    
    <button
      v-if="images.length > 1 && showArrows"
      class="restaurant-hero__nav restaurant-hero__nav--next"
      @click="nextImage"
      aria-label="Следующее изображение"
    >
      <i class="pi pi-chevron-right"></i>
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { AppImage } from '../../shared/ui'

interface RestaurantImage {
  src: string
  alt: string
}

interface RestaurantHeroProps {
  images: RestaurantImage[]
  autoSlide?: boolean
  autoSlideInterval?: number
  showArrows?: boolean
}

const props = withDefaults(defineProps<RestaurantHeroProps>(), {
  autoSlide: false,
  autoSlideInterval: 5000,
  showArrows: false
})

const emit = defineEmits<{
  imageChange: [index: number, image: RestaurantImage]
}>()

const currentIndex = ref(0)
let autoSlideTimer: NodeJS.Timeout | null = null

const currentImage = computed(() => {
  return props.images[currentIndex.value] || { src: '', alt: '' }
})

const setCurrentImage = (index: number) => {
  if (index >= 0 && index < props.images.length) {
    currentIndex.value = index
    emit('imageChange', index, props.images[index])
    
    // Reset auto slide timer
    if (props.autoSlide) {
      resetAutoSlideTimer()
    }
  }
}

const nextImage = () => {
  const nextIndex = (currentIndex.value + 1) % props.images.length
  setCurrentImage(nextIndex)
}

const previousImage = () => {
  const prevIndex = currentIndex.value === 0 
    ? props.images.length - 1 
    : currentIndex.value - 1
  setCurrentImage(prevIndex)
}

const handleImageLoad = () => {
  // Можно добавить логику для preloading следующих изображений
}

const startAutoSlide = () => {
  if (props.autoSlide && props.images.length > 1) {
    autoSlideTimer = setInterval(() => {
      nextImage()
    }, props.autoSlideInterval)
  }
}

const stopAutoSlide = () => {
  if (autoSlideTimer) {
    clearInterval(autoSlideTimer)
    autoSlideTimer = null
  }
}

const resetAutoSlideTimer = () => {
  stopAutoSlide()
  startAutoSlide()
}

// Touch events for swipe
const handleTouchStart = (event: TouchEvent) => {
  const touch = event.touches[0]
  touchStartX.value = touch.clientX
}

const handleTouchEnd = (event: TouchEvent) => {
  const touch = event.changedTouches[0]
  const diff = touchStartX.value - touch.clientX
  const threshold = 50
  
  if (Math.abs(diff) > threshold) {
    if (diff > 0) {
      nextImage()
    } else {
      previousImage()
    }
  }
}

const touchStartX = ref(0)

onMounted(() => {
  if (props.autoSlide) {
    startAutoSlide()
  }
})

onUnmounted(() => {
  stopAutoSlide()
})
</script>

<style>
/* Restaurant Hero Component - PostCSS Nested */
.restaurant-hero {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
  border-radius: var(--border-radius-xl);
  margin-bottom: var(--spacing-xl);
  background-color: var(--color-surface);
  user-select: none;

  &__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: var(--border-radius-xl);
  }

  &__indicators {
    position: absolute;
    bottom: var(--spacing-md);
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: var(--spacing-xs);
    z-index: 2;
  }

  &__indicator {
    width: 6px;
    height: 6px;
    border-radius: var(--border-radius-full);
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
    cursor: pointer;
    transition: all var(--transition-base);
    
    &:hover {
      background-color: rgba(255, 255, 255, 0.7);
    }
    
    &--active {
      background-color: var(--color-white);
      width: 20px;
    }
  }

  &__nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    background-color: rgba(0, 0, 0, 0.5);
    color: var(--color-white);
    border: none;
    border-radius: var(--border-radius-full);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition-base);
    z-index: 2;
    
    &:hover {
      background-color: rgba(0, 0, 0, 0.7);
    }
    
    &--prev {
      left: var(--spacing-md);
    }
    
    &--next {
      right: var(--spacing-md);
    }
  }
}

/* Touch-friendly design для Telegram Mini App */
@media (max-width: 375px) {
  .restaurant-hero {
    height: 180px;

    &__nav {
      width: 36px;
      height: 36px;
    }
  }
}
</style> 