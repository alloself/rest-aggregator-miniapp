# Active Context - Текущий фокус работы

## Текущее состояние проекта

### Архитектурная основа ✅ ГОТОВО
- **BaseModel pattern** реализован с UUID поддержкой
- **Three-interface strategy** настроена (site, account, admin)
- **Vite configuration** с multiple entry points
- **Docker environment** полностью настроен
- **TypeScript integration** с FumeApp/ModelTyper

### Backend Foundation ✅ ГОТОВО
- **Laravel 12** с современными пакетами
- **Database schema** с UUID primary keys
- **Authentication** через Laravel Sanctum SPA
- **Role-based access** с Spatie/Laravel-Permission
- **API routes** структурированы по ролям

### Frontend Infrastructure ✅ ГОТОВО
- **Vue 3 + TypeScript** настроен для всех интерфейсов
- **PrimeVue UI library** интегрирована
- **Shared resources** структура создана
- **Type generation** автоматизирована

### 🆕 UI/UX Design System ✅ ГОТОВО
- **Figma макет проанализирован** - страница ресторана для Telegram Mini App
- **SCSS дизайн-система настроена** - CSS variables, БЭМ компоненты, типографика
- **Tailwind удален** - заменен на кастомную SCSS архитектуру
- **Inter шрифт интегрирован** - Google Fonts с variable weight поддержкой
- **Telegram Mini App стили** - viewport 440px, touch-friendly элементы
- **Компонентная архитектура** - готовые CSS классы для всех Figma компонентов

## Текущий фокус разработки

### 1. Frontend Applications Development 🔄 В ПРОЦЕССЕ

#### Site Application (Публичный сайт)
**Статус**: Дизайн-система настроена, готова к компонентам
**Приоритет**: Высокий (готов к реализации Figma макета)
**Задачи**:
- [x] Дизайн-система SCSS настроена (resources/site/css/app.scss)
- [x] CSS variables для всех Figma токенов
- [x] БЭМ структура для restaurant page компонентов
- [ ] **RestaurantPage Vue компонент** - реализация с новыми стилями
- [ ] Каталог ресторанов с фильтрацией
- [ ] Страницы ресторанов с меню и событиями
- [ ] Регистрация новых ресторанов

#### Account Application (ЛК ресторанов) 
**Статус**: Базовая аутентификация реализована
**Приоритет**: Высокий
**Задачи**:
- [x] Аутентификация и базовая структура
- [ ] Dashboard с аналитикой ресторана
- [ ] Menu Management с drag-and-drop категориями
- [ ] Event Planning с календарем
- [ ] News Publishing система
- [ ] Image upload и optimization

#### Admin Application (Админ панель)
**Статус**: Базовая структура создана  
**Приоритет**: Высокий
**Задачи**:
- [ ] Dashboard с общей аналитикой
- [ ] Restaurant Management (CRUD)
- [ ] Content Moderation система
- [ ] User Management с ролями
- [ ] Curated Collections builder

### 2. Core Features Implementation 🔄 В ПРОЦЕССЕ

#### UI Components Library (основано на Figma анализе)
**Приоритет**: Критический
**Компоненты из Figma макета**:
- [ ] **RestaurantHero** - основное фото с индикаторами слайдов
- [ ] **RestaurantCard** - информационная карточка с деталями
- [ ] **RestaurantDescription** - блок описания ресторана
- [ ] **ChefRecommendations** - рекомендации с фотогалереей
- [ ] **CategoryFilter** - кнопки фильтрации (Новости/События/Предложения)
- [ ] **NewsCard** - карточка новости с изображением
- [ ] **ActionButtons** - кнопки меню/бар/фото/бронирование

#### Content Management System
**Приоритет**: Критический
**Компоненты**:
- [ ] **SmartForm Generator** - универсальные формы на VeeValidate + Zod
- [ ] **Image Upload Component** - с preview и cropping
- [ ] **Category Manager** - drag-and-drop для many-to-many отношений
- [ ] **Rich Text Editor** - для описаний блюд и событий

#### Social Features
**Приоритет**: Высокий
**Компоненты**:
- [ ] **Like System** - polymorphic лайки с real-time счетчиками
- [ ] **Favorites System** - избранные рестораны и блюда
- [ ] **Recommendation Engine** - персональные подборки

#### Event Management
**Приоритет**: Высокий
**Компоненты**:
- [ ] **Custom Calendar** - Vue 3 календарь для событий
- [ ] **Event Registration** - система записи на события
- [ ] **Time-based Collections** - автоматические подборки по времени

## Архитектурные решения в работе

### 1. Custom UI Components Strategy (обновлено после Figma анализа)
**Принцип**: Все custom компоненты на основе PrimeVue базовых элементов + Figma дизайн-система
**Дизайн-токены из Figma**:
```scss
// Цветовая палитра
$text-primary: #000000;
$text-secondary: #1C1C1E;
$text-muted: rgba(0, 0, 0, 0.35);
$bg-cards: #FFFFFF;
$bg-buttons: #F4F3F3;
$bg-accent: rgba(200, 228, 155, 0.35);
$bg-overlay: rgba(244, 243, 243, 0.68);

// Типографика
$font-family: 'Inter';
$font-sizes: (
  'restaurant-title': 20px,
  'section-title': 18px,
  'button': 15px,
  'body': 15px,
  'description': 14px,
  'news-title': 16px
);

// Скругления
$border-radius: (
  'cards': 20px,
  'buttons': 20px,
  'photos': 20px,
  'hero-bottom': 0px 0px 40px 40px
);
```

### 2. Telegram Mini App Constraints
**Принцип**: Соответствие стандартам Telegram Mini Apps
**Ограничения**:
```typescript
// Размеры экрана
const TELEGRAM_VIEWPORT = {
  width: 440, // фиксированная ширина
  maxHeight: 'viewport', // вертикальная прокрутка
  touchTarget: 44 // минимальный размер touch элементов
};

// Telegram-specific поведение
const TELEGRAM_BEHAVIOR = {
  navigation: 'spa', // одностраничное приложение
  gestures: ['swipe', 'tap'], // поддерживаемые жесты
  loading: 'progressive' // прогрессивная загрузка контента
};
```

### 3. Shared Composables Pattern
**Принцип**: Переиспользуемая логика в shared/composables
**Структура**:
```typescript
// shared/composables/
├── useAuth.ts          // Аутентификация
├── useApi.ts           // API запросы
├── useRestaurant.ts    // Логика ресторана
├── useMenu.ts          // Управление меню
├── useEvents.ts        // События
├── useLikes.ts         // Система лайков
└── useUI.ts            // UI состояния и взаимодействия (новый)
```

### 4. Type-Safe API Pattern
**Принцип**: Все API запросы с generic типами
**Реализация**:
```typescript
// Типизированные API клиенты
const restaurants = await apiClient.get<Restaurant[]>('/api/v1/restaurants');
const dish = await apiClient.post<Dish>('/api/v1/restaurant/dishes', data);

// Error handling с типизацией
interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
}
```

## Ближайшие приоритеты (обновлено)

### Неделя 1: Telegram Mini App UI Foundation
1. **RestaurantPage Implementation**
   - RestaurantHero компонент с слайдер функциональностью
   - RestaurantCard с полной информацией
   - News система с карточками
   - CategoryFilter для фильтрации контента

2. **Design System Setup**
   - CSS variables из Figma токенов
   - PrimeVue theme customization
   - Responsive utilities для 440px ширины
   - Typography система (Inter font)

### Неделя 2: Core Components Library
1. **Image & Media Components**
   - ChefRecommendations галерея
   - Image upload с preview
   - Progressive loading для изображений
   - Placeholder states

2. **Navigation & Interaction**
   - ActionButtons компонент
   - CategoryFilter с состояниями
   - Page Control (слайдер индикаторы)
   - Touch gesture support

### Неделя 3-4: Account Application Core
1. **Menu Management Interface**
   - CRUD операции для блюд
   - Category assignment с drag-and-drop
   - Image upload с preview
   - Real-time validation

2. **Event Planning System**
   - Custom calendar компонент
   - Event creation форма
   - Participant management
   - Time-based filtering

### Неделя 5-6: Social Features
1. **Like System Implementation**
   - Polymorphic like API
   - Real-time UI updates
   - Like counters и analytics
   - User like history

2. **News & Recommendations**
   - News publishing система
   - Chef recommendations управление
   - Content discovery алгоритмы
   - Featured content curation

## Технические долги и улучшения

### Code Quality
- [ ] **ESLint configuration** для строгой типизации
- [ ] **Prettier setup** для консистентного форматирования
- [ ] **Vue DevTools** оптимизация для development
- [ ] **Error boundaries** для graceful error handling

### Performance Optimizations
- [ ] **Image optimization** pipeline (критично для Telegram Mini App)
- [ ] **Lazy loading** для больших списков и изображений
- [ ] **Bundle size optimization** - особенно важно для мобильного
- [ ] **Progressive loading** - постепенная загрузка контента

### Telegram Mini App Specific
- [ ] **Telegram SDK integration** - взаимодействие с Telegram API
- [ ] **Theme adaptation** - поддержка темной/светлой тем Telegram
- [ ] **Haptic feedback** - тактильная обратная связь
- [ ] **Telegram sharing** - кнопки поделиться контентом

### Testing Strategy
- [ ] **Unit tests** для composables
- [ ] **Component testing** с Vue Test Utils
- [ ] **Visual regression testing** для UI компонентов
- [ ] **Telegram Mini App testing** в реальной среде

## Блокеры и риски

### Текущие блокеры
- **Нет блокеров** - все зависимости настроены

### Потенциальные риски
1. **Telegram Mini App Constraints** - ограничения платформы
   - *Митигация*: Строгое следование Telegram guidelines

2. **Image Loading Performance** - медленная загрузка в мобильных сетях
   - *Митигация*: Progressive loading, WebP format, lazy loading

3. **UI Component Complexity** - custom компоненты согласно Figma
   - *Митигация*: Модульная архитектура на основе PrimeVue

4. **Type Safety Maintenance** - синхронизация типов между backend и frontend
   - *Митигация*: Автоматизированная генерация через ModelTyper

## Следующие шаги

### Immediate Actions (эта неделя)
1. **Анализ Figma компонентов** - детальная декомпозиция UI элементов
2. **RestaurantPage scaffold** - базовая структура страницы ресторана
3. **Design tokens setup** - CSS variables из Figma дизайн-системы
4. **PrimeVue theme customization** - адаптация под Figma макет

### Short-term Goals (2-4 недели)
1. **Полнофункциональная RestaurantPage** согласно Figma макету
2. **Complete UI components library** - все компоненты из Figma
3. **Account dashboard** с управлением контентом
4. **News и recommendations** система

### Medium-term Objectives (1-3 месяца)
1. **Полная реализация всех трех интерфейсов**
2. **Telegram Mini App deployment** - интеграция с Telegram
3. **Advanced analytics и reporting**
4. **Production deployment готовность**

## 🆕 Figma Integration Notes

### Ключевые инсайты из макета
1. **Контент-первый подход** - акцент на фото и описания
2. **Минималистичный дизайн** - чистые линии, много белого пространства
3. **Функциональная навигация** - четкие кнопки действий
4. **Социальные элементы** - система рекомендаций и новостей
5. **Mobile-first** - оптимизация для touch взаимодействий

### Приоритетные компоненты для реализации
1. **RestaurantHero** - первое впечатление пользователя
2. **RestaurantCard** - ключевая информация о ресторане
3. **NewsCard** - система контента и взаимодействия
4. **CategoryFilter** - навигация по контенту
5. **ActionButtons** - основные действия пользователя 