<template>
  <div class="photo-reels">
    <div class="swiper photo-reels__swiper" ref="swiperContainer">
      <div class="swiper-wrapper">
        <div
          v-for="(image, index) in images"
          :key="index"
          class="swiper-slide photo-reels__slide"
        >
          <img
            :src="image.url"
            :alt="image.alt || `Фото ${index + 1}`"
            class="photo-reels__image"
            loading="lazy"
          />

          <div class="photo-reels__overlay">
            <slot name="overlay" :image="image" :index="index" />
          </div>
        </div>
      </div>

      <div v-if="showPagination" class="swiper-pagination photo-reels__pagination"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, nextTick, watch } from 'vue';
import { Swiper } from 'swiper';
import { Pagination, Mousewheel, Keyboard } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';

interface PhotoReelsImage {
  url: string;
  alt?: string;
}

interface Props {
  /** Список изображений для показа */
  images: PhotoReelsImage[];
  /** Показывать ли пагинацию-точки (вертикально справа) */
  showPagination?: boolean;
  /** Начальный слайд */
  initialSlide?: number;
  /** Зацикливание */
  loop?: boolean;
  /** Навигация с клавиатуры */
  keyboard?: boolean;
  /** Скроллом мыши по оси Y */
  mousewheel?: boolean;
  /** Отступ между слайдами */
  spaceBetween?: number;
  /** Скорость анимации */
  speed?: number;
}

const props = withDefaults(defineProps<Props>(), {
  showPagination: false,
  initialSlide: 0,
  loop: true,
  keyboard: true,
  mousewheel: true,
  spaceBetween: 0,
  speed: 350,
});

const emit = defineEmits<{
  (e: 'slideChange', activeIndex: number): void;
  (e: 'reachEnd'): void;
  (e: 'reachBeginning'): void;
}>();

const swiperContainer = ref<HTMLElement>();
let swiperInstance: Swiper | null = null;

/**
 * Инициализирует вертикальный Swiper под формат Reels.
 */
const initSwiper = async () => {
  if (!swiperContainer.value || props.images.length === 0) return;

  await nextTick();
  if (!swiperContainer.value) return;

  const modules = [Pagination, Mousewheel, Keyboard];

  swiperInstance = new Swiper(swiperContainer.value, {
    modules,
    direction: 'vertical',
    slidesPerView: 1,
    spaceBetween: props.spaceBetween,
    loop: props.loop && props.images.length > 1,
    initialSlide: props.initialSlide,
    speed: props.speed,
    mousewheel: props.mousewheel
      ? { forceToAxis: true, releaseOnEdges: true, sensitivity: 1 }
      : false,
    keyboard: props.keyboard ? { enabled: true, onlyInViewport: true } : false,
    pagination: props.showPagination
      ? {
          el: '.photo-reels__pagination',
          clickable: true,
          bulletClass: 'photo-reels__bullet',
          bulletActiveClass: 'photo-reels__bullet--active',
        }
      : false,
    grabCursor: true,
    watchOverflow: true,
    observer: true,
    observeParents: true,
    allowTouchMove: true,
    touchRatio: 1,
    touchAngle: 45,
    on: {
      slideChange: () => {
        if (!swiperInstance) return;
        emit('slideChange', swiperInstance.realIndex);
      },
      reachEnd: () => emit('reachEnd'),
      reachBeginning: () => emit('reachBeginning'),
    },
  });
};

/**
 * Уничтожает экземпляр Swiper и очищает ссылки.
 */
const destroySwiper = () => {
  if (swiperInstance) {
    swiperInstance.destroy(true, true);
    swiperInstance = null;
  }
};

onMounted(() => {
  initSwiper();
});

onBeforeUnmount(() => {
  destroySwiper();
});

watch(
  () => props.images,
  () => {
    destroySwiper();
    initSwiper();
  },
  { deep: true }
);

defineExpose({
  swiper: () => swiperInstance,
  slideTo: (index: number) => swiperInstance?.slideTo(index),
  slideNext: () => swiperInstance?.slideNext(),
  slidePrev: () => swiperInstance?.slidePrev(),
});
</script>

<style>
.photo-reels {
  position: relative;
  width: 100%;
  height: 100%;

  &__swiper {
    width: 100%;
    height: 100%;
  }

  &__slide {
    position: relative;
    width: 100%;
    height: 100%;
    background: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  &__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    user-select: none;
    -webkit-user-drag: none;
    pointer-events: none;
  }

  &__overlay {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    padding: 16px;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.35) 40%, rgba(0, 0, 0, 0.6) 100%);
    color: #fff;
    display: flex;
    gap: 12px;
    align-items: flex-end;
    pointer-events: auto;
  }

  &__pagination {
    position: absolute !important;
    right: 8px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 6px !important;
    z-index: 10 !important;
  }

  &__bullet {
    width: 6px !important;
    height: 6px !important;
    border-radius: 50% !important;
    background: rgba(255, 255, 255, 0.45) !important;
    transition: transform 0.2s ease, background 0.2s ease !important;

    &--active {
      background: #fff !important;
      transform: scale(1.2) !important;
    }
  }
}

@media (max-width: 480px) {
  .photo-reels {
    &__overlay {
      padding: 12px;
    }
  }
}
</style>


