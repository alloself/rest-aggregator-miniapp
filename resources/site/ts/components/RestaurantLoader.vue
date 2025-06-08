<template>
  <div class="restaurant-loader">
    <!-- Background Image -->
    <div class="restaurant-loader__background">
      <img 
        src="/images/loader/restaurant-loader-bg.png"
        alt="Restaurant background"
        class="restaurant-loader__bg-image"
      />
    </div>

    <!-- Dark Overlay -->
    <div class="restaurant-loader__overlay"></div>

    <!-- Content -->
    <div class="restaurant-loader__content">
      <!-- Restaurant Logo -->
      <div class="restaurant-loader__logo">
        <img 
          src="/images/loader/restaurant-loader-logo.png"
          alt="Restaurant logo"
          class="restaurant-loader__logo-image"
        />
      </div>

      <!-- Restaurant Name -->
      <h1 class="restaurant-loader__title">{{ restaurantName }}</h1>

      <!-- Restaurant Cuisine Type -->
      <p class="restaurant-loader__subtitle">{{ cuisineType }}</p>
    </div>

    <!-- Loading Animation -->
    <div class="restaurant-loader__spinner">
      <div class="restaurant-loader__spinner-circle"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  restaurantName?: string
  cuisineType?: string
  isVisible?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  restaurantName: 'GRETTO',
  cuisineType: 'Средиземноморская кухня',
  isVisible: true
})
</script>

<style>
/* =============================================================================
   RESTAURANT LOADER - БЭМ METHODOLOGY STYLES (PostCSS Nested)
   ============================================================================= */

.restaurant-loader {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  z-index: 9999;
  overflow: hidden;
  
  /* Centering for mobile */
  display: flex;
  align-items: center;
  justify-content: center;

  &__background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
  }

  &__bg-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    
    /* Изображение на весь экран без выхода за границы */
    transform: none;
  }

  &__overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.49);
    z-index: 1;
  }

  &__content {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 440px;
    width: 100%;
    padding: 0 16px;
    
    /* Анимация появления контента */
    animation: fadeInUp 1.2s ease-out;
  }

  &__logo {
    margin-bottom: 46px;
    display: flex;
    justify-content: center;
    
    /* Согласно макету: 136px left, 328px top, 167x116px */
    width: 167px;
    height: 116px;
    margin: 0 auto 46px auto;
    
    /* Анимация логотипа */
    animation: logoFloat 2.5s ease-in-out infinite;
  }

  &__logo-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 500px;
  }

  &__title {
    /* Согласно макету: Inter 600, 40px, center */
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    font-weight: 600;
    font-size: 40px;
    line-height: 0.55;
    text-align: center;
    color: #FFFFFF;
    margin: 0 0 10px 0;
    letter-spacing: -0.02em;
    
    /* Анимация заголовка */
    animation: slideInFromLeft 1s ease-out 0.3s both;
  }

  &__subtitle {
    /* Согласно макету: Inter 600, 24px, center */
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    font-weight: 600;
    font-size: 24px;
    line-height: 0.92;
    text-align: center;
    color: #FFFFFF;
    margin: 0;
    letter-spacing: -0.01em;
    
    /* Анимация подзаголовка */
    animation: slideInFromRight 1s ease-out 0.6s both;
  }

  &__spinner {
    position: absolute;
    bottom: 80px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 3;
  }

  &__spinner-circle {
    width: 32px;
    height: 32px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-top: 3px solid #FFFFFF;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }
}

/* =============================================================================
   ANIMATIONS
   ============================================================================= */

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes logoFloat {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

@keyframes slideInFromLeft {
  from {
    opacity: 0;
    transform: translateX(-50px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slideInFromRight {
  from {
    opacity: 0;
    transform: translateX(50px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

/* =============================================================================
   RESPONSIVE
   ============================================================================= */

/* Адаптация под размер макета 440x932 */
@media (max-width: 440px) {
  .restaurant-loader {
    &__content {
      padding: 0 12px;
    }

    &__title {
      font-size: 36px;
    }

    &__subtitle {
      font-size: 22px;
    }
  }
}

/* Скрытие лоадера */
.restaurant-loader.fade-out {
  animation: fadeOut 0.5s ease-out forwards;
}

@keyframes fadeOut {
  from {
    opacity: 1;
  }
  to {
    opacity: 0;
    visibility: hidden;
  }
}
</style> 