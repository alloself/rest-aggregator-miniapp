<template>
  <div class="restaurant-hero-carousel">
    <div class="swiper restaurant-hero-carousel__swiper" ref="swiperContainer">
      <div class="swiper-wrapper">
        <div v-for="(image, index) in images" :key="index" class="swiper-slide restaurant-hero-carousel__slide">
          <img
            :src="image.url"
            :alt="image.alt || `Фото ресторана ${index + 1}`"
            class="restaurant-hero-carousel__image"
            loading="lazy"
          />
        </div>
      </div>

      <!-- Pagination dots -->
      <div class="swiper-pagination restaurant-hero-carousel__pagination" v-if="showPagination"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { Swiper } from 'swiper';
import { Pagination, Autoplay } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';

interface RestaurantImage {
  url: string;
  alt?: string;
}

interface Props {
  /** Массив изображений для карусели */
  images: RestaurantImage[];
  /** Автоматическая прокрутка (мс) */
  autoplay?: number | false;
  /** Показывать ли пагинацию */
  showPagination?: boolean;
  /** Высота карусели */
  height?: string;
  /** Радиус скругления контейнера */
  borderRadius?: string;
  /** Отступ снизу для пагинации */
  paginationBottom?: string;
}

const props = withDefaults(defineProps<Props>(), {
  autoplay: 3000,
  showPagination: true,
  height: '298px',
  borderRadius: '0 0 40px 40px',
  paginationBottom: '48px',
});

// Refs
const swiperContainer = ref<HTMLElement>();
let swiperInstance: Swiper | null = null;

// Initialize Swiper
const initSwiper = async () => {
  if (!swiperContainer.value || props.images.length === 0) return;

  await nextTick();

  if (!swiperContainer.value) return;

  // Initialize Swiper instance with explicit configuration
  swiperInstance = new Swiper(swiperContainer.value, {
    modules: [Pagination, Autoplay],
    slidesPerView: 1,
    spaceBetween: 0,
    loop: props.images.length > 1,

    // Pagination configuration
    pagination: props.showPagination
      ? {
          el: '.restaurant-hero-carousel__pagination',
          clickable: true,
          bulletClass: 'restaurant-hero-carousel__bullet',
          bulletActiveClass: 'restaurant-hero-carousel__bullet--active',
          type: 'bullets' as const,
          dynamicBullets: false,
          renderBullet: (index: number, className: string) => {
            return `<span class="${className}" data-index="${index}"></span>`;
          },
        }
      : false,

    // Autoplay
    autoplay: props.autoplay
      ? {
          delay: props.autoplay,
          disableOnInteraction: false,
          pauseOnMouseEnter: true,
        }
      : false,

    // Touch settings
    touchRatio: 1,
    touchAngle: 45,
    grabCursor: true,

    // Speed
    speed: 300,
  });
};

// Cleanup Swiper
const destroySwiper = () => {
  if (swiperInstance) {
    swiperInstance.destroy(true, true);
    swiperInstance = null;
  }
};

// Lifecycle
onMounted(() => {
  initSwiper();
});

onBeforeUnmount(() => {
  destroySwiper();
});

// Expose swiper instance for parent component
defineExpose({
  swiper: () => swiperInstance,
  slideTo: (index: number) => swiperInstance?.slideTo(index),
  slideNext: () => swiperInstance?.slideNext(),
  slidePrev: () => swiperInstance?.slidePrev(),
});
</script>

<style>
/* Restaurant Hero Carousel Component - PostCSS Nested */
.restaurant-hero-carousel {
  position: relative;
  width: 100%;
  border-radius: v-bind(borderRadius);
  overflow: hidden;
  background-color: #f4f3f3;

  &__swiper {
    width: 100%;
    height: v-bind(height);
  }

  &__slide {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;

    /* Loading state */
    &::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(
        90deg,
        rgba(244, 243, 243, 0.8) 0%,
        rgba(244, 243, 243, 0.4) 50%,
        rgba(244, 243, 243, 0.8) 100%
      );
      opacity: 0;
      transition: opacity 0.3s ease;
      pointer-events: none;
    }
  }

  &__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;

    &[loading] ~ .restaurant-hero-carousel__slide::before {
      opacity: 1;
    }
  }

  /* Custom Pagination Styles */
  &__pagination {
    position: absolute !important;
    bottom: v-bind(paginationBottom) !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    display: flex !important;
    gap: 8px !important;
    padding: 8px 12px !important;
    background: rgba(0, 0, 0, 0.2) !important;
    border-radius: 50px !important;
    backdrop-filter: blur(4px) !important;
    z-index: 1000 !important;
    width: auto !important;
    height: auto !important;
    justify-content: center !important;
    align-items: center !important;

    /* Fallback если Swiper не инициализируется */
    &:empty::after {
      content: '● ● ●';
      color: rgba(255, 255, 255, 0.6);
      font-size: 8px;
      letter-spacing: 8px;
    }
  }

  &__bullet {
    width: 8px !important;
    height: 8px !important;
    border-radius: 50% !important;
    background: rgba(245, 245, 245, 0.6) !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    border: none !important;
    outline: none !important;
    display: inline-block !important;
    margin: 0 !important;
    opacity: 1 !important;

    &:hover {
      background: rgba(255, 255, 255, 0.8) !important;
      transform: scale(1.1) !important;
    }

    &--active {
      background: #ffffff !important;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
    }
  }

  &__fallback-bullet {
    width: 8px !important;
    height: 8px !important;
    border-radius: 50% !important;
    background: rgba(245, 245, 245, 0.6) !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    display: inline-block !important;

    &--active {
      background: #ffffff !important;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
    }
  }
}

/* Responsive */
@media (max-width: 480px) {
  .restaurant-hero-carousel {
    border-radius: 0 0 20px 20px;

    &__pagination {
      bottom: 12px;
      padding: 6px 10px;
    }

    &__bullet {
      width: 6px;
      height: 6px;
    }
  }
}
</style>
