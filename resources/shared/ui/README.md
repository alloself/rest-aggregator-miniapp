# Shared UI Components

Коллекция переиспользуемых UI компонентов для всех интерфейсов проекта.

## MdiIcon

Универсальный компонент для отрисовки иконок Material Design Icons.

### Использование

```vue
<template>
  <!-- Базовое использование -->
  <MdiIcon name="heart" />
  
  <!-- С размером -->
  <MdiIcon name="star" size="lg" />
  
  <!-- С цветом -->
  <MdiIcon name="user" color="primary" />
  
  <!-- Кастомный размер -->
  <MdiIcon name="settings" size="custom" :custom-size="48" />
  
  <!-- С дополнительными стилями -->
  <MdiIcon 
    name="menu" 
    size="md" 
    color="gray" 
    :style="{ marginRight: '8px' }"
  />
</template>

<script setup>
import { MdiIcon } from '@shared/ui'
</script>
```

### Props

- `name` (string, required) - Название иконки из MDI без префикса "mdi-"
- `size` (string, optional) - Размер иконки:
  - `xs` - 12px
  - `sm` - 16px 
  - `md` - 20px (по умолчанию)
  - `lg` - 24px
  - `xl` - 32px
  - `custom` - кастомный размер
- `customSize` (number, optional) - Размер в пикселях для size="custom"
- `color` (string, optional) - Цвет иконки:
  - `black` (по умолчанию)
  - `white`
  - `primary`
  - `gray`
  - `custom` - для использования с CSS
- `alt` (string, optional) - Alt текст для accessibility
- `style` (object, optional) - Дополнительные CSS стили

### Поиск иконок

Найти иконки можно на официальном сайте: https://materialdesignicons.com/

Используйте название иконки без префикса `mdi-`. Например:
- `mdi-home` → `name="home"`
- `mdi-menu-down` → `name="menu-down"`
- `mdi-account-circle` → `name="account-circle"`

### Особенности

- Автоматический fallback при отсутствии иконки
- Поддержка accessibility через alt атрибут
- CSS фильтры для изменения цвета
- Оптимизированная загрузка через динамический импорт
- Полная типизация TypeScript

## RestaurantHeroCarousel

Карусель фотографий ресторана на основе Swiper.js с пагинацией и автопрокруткой.

### Использование

```vue
<template>
  <!-- Базовое использование -->
  <RestaurantHeroCarousel :images="heroImages" />
  
  <!-- С настройками -->
  <RestaurantHeroCarousel 
    :images="heroImages"
    :autoplay="5000"
    height="400px"
    :show-pagination="true"
  />
  
  <!-- Без автопрокрутки -->
  <RestaurantHeroCarousel 
    :images="heroImages"
    :autoplay="false"
  />
</template>

<script setup>
import { RestaurantHeroCarousel } from '@shared/ui'

const heroImages = ref([
  {
    url: 'https://example.com/image1.jpg',
    alt: 'Интерьер ресторана'
  },
  {
    url: 'https://example.com/image2.jpg', 
    alt: 'Зал ресторана'
  }
])
</script>
```

### Props

- `images` (Array, required) - Массив изображений:
  - `url` (string) - URL изображения
  - `alt` (string, optional) - Alt текст
- `autoplay` (number | false, optional) - Автопрокрутка в мс (default: 3000)
- `showPagination` (boolean, optional) - Показывать пагинацию (default: true)
- `height` (string, optional) - Высота карусели (default: '298px')

### Методы

Компонент предоставляет методы для программного управления:

```vue
<template>
  <RestaurantHeroCarousel ref="carousel" :images="images" />
  <button @click="nextSlide">Следующий</button>
</template>

<script setup>
const carousel = ref()

const nextSlide = () => {
  carousel.value?.slideNext()
}

// Другие методы:
// carousel.value?.slidePrev() - предыдущий слайд
// carousel.value?.slideTo(index) - к конкретному слайду
</script>
```

### Особенности

- **Swiper.js интеграция** - производительная карусель с touch поддержкой
- **Кастомная пагинация** - стилизованные точки как в дизайне Figma
- **Автопрокрутка** - с паузой при наведении
- **Responsive** - адаптивная под мобильные устройства
- **Lazy loading** - оптимизированная загрузка изображений
- **Accessibility** - поддержка screen readers через alt атрибуты 