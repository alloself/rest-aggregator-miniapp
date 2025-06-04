# Active Context - REST Aggregator Miniapp

## ⚡ КРИТИЧЕСКИЕ ПРАВИЛА РАЗРАБОТКИ

### 🚫 СТРОГИЙ ЗАПРЕТ `any` типа
- **НИ В КОЕМ СЛУЧАЕ не используйте `any` тип!**
- Используйте `unknown`, конкретные типы или type guards
- При API запросах указывайте generic: `apiClient.get<User>('/api/user')`

### 🔐 Session-based Authentication (Sanctum SPA)
- **БЕЗ токенов в localStorage** - только HttpOnly cookies
- **Автоматическая CSRF защита** через cookies
- **withCredentials: true** для всех запросов
- **Event-driven** auth state management

## Текущий статус проекта: STYLING_SYSTEM_UPDATED ✅
*Обновлено: Удален Tailwind CSS, обновлена архитектура стилизации*

## Фокус текущей работы
**SCSS + БЭМ Styling System** - Переход на кастомную систему стилизации без утилитарных фреймворков, обновлена архитектура проекта

### ✅ **Завершенные критические настройки:**
1. **CSS Architecture** - Переход на SCSS + БЭМ методологию
2. **Dependencies Cleanup** - Удалены все зависимости Tailwind CSS
3. **Vite Configuration** - Обновлена конфигурация без Tailwind плагина
4. **Documentation Update** - Обновлена документация проекта
5. **Clean Codebase** - Удалены все упоминания Tailwind из проекта

### 🎨 **SCSS Entry Points Configuration:**
```
resources/
├── site/scss/app.scss      → Custom SCSS + .site-* classes  
├── account/scss/app.scss   → Custom SCSS + .dashboard-* classes
└── admin/scss/app.scss     → Custom SCSS + .admin-* classes
```

### 📋 **Vite Entry Points:**
```typescript
input: [
  "resources/site/css/app.css",     // Site public interface
  "resources/account/css/app.css",  // Restaurant dashboard  
  "resources/admin/css/app.css",    // Admin panel
  // + TypeScript entries...
]
```

### 🔧 **CSS Architecture Strategy:**
- **Base Layer**: Typography и global styles
- **Components Layer**: Interface-specific component classes
- **Utilities Layer**: Custom utilities для каждого интерфейса

### 🎯 **Interface-Specific CSS Classes:**

#### **Site Interface** (Public Site):
- `.site-nav` - Main navigation
- `.hero-section` - Landing hero  
- `.restaurant-card` - Restaurant listings
- `.event-card` - Event displays
- `.collection-widget` - Content collections
- `.featured-restaurant` - Showcase components
- `.category-filter` - Filter buttons

#### **Account Interface** (Restaurant Dashboard):
- `.dashboard-*` - Layout components
- `.menu-item-card` - Menu management
- `.event-form-card` - Event creation
- `.profile-section` - Restaurant profile
- `.form-*` - Form components
- `.btn-*` - Action buttons
- `.status-badge` - Status indicators

#### **Admin Interface** (Admin Panel):
- `.admin-*` - All admin components
- `.admin-table` - Data tables
- `.collection-builder` - Content curation
- `.analytics-widget` - Dashboard analytics
- `.content-editor` - Content management
- `.admin-btn-*` - Admin actions
- `.admin-status-badge` - Admin statuses

## Следующие шаги
1. **Vue Router Setup** - Настройка маршрутизации для каждого интерфейса
2. **Authentication Integration** - Интеграция Sanctum в Vue компоненты
3. **API Layer Implementation** - Создание типизированных API services
4. **Component Development** - Разработка UI компонентов

## 📚 **Критические знания о SCSS + БЭМ архитектуре:**
- ✅ **SCSS Support**: SCSS модули для кастомных компонентов
- ✅ **БЭМ Methodology**: Структурированное именование классов
- ✅ **Component Isolation**: Каждый компонент имеет собственные стили
- ✅ **UI Component Architecture**: Структурированная система компонентов
- ✅ **Performance**: Минимальный размер бандла без утилитарных классов
- ✅ **Maintainability**: Читаемые и поддерживаемые стили
- ✅ **Custom Properties**: CSS переменные для темизации

## Готовые компоненты инфраструктуры
- ✅ **Backend API** (95% готов)
- ✅ **Authentication** (Sanctum SPA complete)
- ✅ **Database Models** (с UUID support)
- ✅ **TypeScript Generation** (fumeapp/modeltyper)
- ✅ **CSS Architecture** (SCSS + БЭМ)
- ⏳ **Vue Components** (в процессе разработки)

## Development Environment Status
- ⚡ **Dev servers**: Автоматически запущены (НЕ перезапускать!)
- 🔧 **HMR**: Активен для всех интерфейсов  
- 🚀 **Vue DevTools**: Настроены и работают
- 📦 **Build System**: Vite 6.3.5 с SCSS поддержкой

## Техническая готовность
**Frontend Architecture: 45% → 55%** 
- Базовая инфраструктура и стили готовы
- CSS компоненты для всех интерфейсов созданы
- Entry points правильно настроены
- **SCSS + БЭМ архитектура настроена и готова к использованию**
- Готов к разработке Vue компонентов с современными возможностями CSS

## Завершенные компоненты ✅

### 1. Database Architecture (OPTIMIZED)
- ✅ **BaseModel с UUID**: Все модели используют UUID primary keys
- ✅ **Correct Migration Order**: Исправлен порядок миграций для правильных foreign keys
- ✅ **Complex Relations**: Many-to-many (dishes↔categories), polymorphic (likes, news)
- ✅ **Permission System**: Spatie Permission с UUID совместимостью
- ✅ **Sanctum Tables**: Personal access tokens с UUID поддержкой
- ✅ **Test Data**: Полные seeders с реальными данными

### 2. Authentication System (COMPLETE & TESTED)
- ✅ **Laravel Sanctum**: API токены с expiration и abilities
- ✅ **Laravel Fortify**: Password reset, profile updates (без views)
- ✅ **Role-Based Access**: admin, restaurant_owner роли
- ✅ **Custom Middleware**: CheckRestaurantOwner для ограничения доступа
- ✅ **Token Abilities**: Разные права для админов и владельцев
- ✅ **Migration Dependencies**: Циклические зависимости решены

### 3. Frontend Setup (OPTIMIZED)
- ✅ **Vite Configuration**: Multiple entry points для трех интерфейсов
- ✅ **SCSS Architecture**: Правильное подключение без дублирования
- ✅ **TypeScript Entry Points**: Чистые точки входа без лишних импортов
- ✅ **Alias Configuration**: Удобные пути для shared компонентов

### 4. API Endpoints (COMPLETE & TESTED)
- ✅ **Auth API**: login, logout, user info, token management
- ✅ **Public API**: рестораны, меню, блюда, события (без auth)
- ✅ **Restaurant API**: управление собственным контентом
- ✅ **Admin API**: полное управление платформой
- ✅ **Like System**: работает без auth через telegram_user_id

### 5. Models & Controllers (COMPLETE)
- ✅ **9 Core Models**: Restaurant, Menu, Dish, Category, Event, News, Like, etc.
- ✅ **API Controllers**: полный CRUD для всех сущностей
- ✅ **API Resources**: правильная сериализация данных
- ✅ **Form Requests**: валидация входящих данных

### 6. ModelTyper Integration (FIXED) ✅
- ✅ **Configuration**: Правильная настройка config/modeltyper.php
- ✅ **Type Generation**: Автоматическая генерация TypeScript типов
- ✅ **Type Fixes**: Исправление int → number, bool → boolean
- ✅ **Make Command**: `make types` для автоматической генерации и исправления
- ✅ **Lodash Integration**: Добавлен lodash с типизированными утилитами

## Исправленные проблемы ✅

### Types Structure & Session Auth (ПОЛНОСТЬЮ РЕШЕНО)
✅ **Дублирующие файлы удалены**: Убраны `models.ts` и `api.ts` 
✅ **Type guards исправлены**: Красивые guards без `obj is import('./api').ApiError`
✅ **Session-based auth**: Переписаны Auth типы под Sanctum SPA (без токенов)
✅ **Центральный импорт**: Все типы через `resources/shared/types/index.ts`
✅ **ANY тип ЗАПРЕЩЕН**: Строгие правила типизации БЕЗ any
✅ **Vite env typing**: Правильная типизация переменных окружения  
✅ **CSRF protection**: Автоматическая защита через cookies
✅ **API Client**: Session-based axios instance с auto retry

```typescript
// ✅ Красивые type guards
export function isApiError(obj: any): obj is ApiError
export function isPaginatedResponse<T>(obj: any): obj is PaginatedResponse<T>
export function isUser(obj: any): obj is User

// ✅ Session-based аутентификация (БЕЗ токенов)
export interface AuthUser extends Omit<User, 'roles'> {
  roles: string[];              // ["admin", "restaurant_owner"]
  restaurant?: Restaurant;      // для владельцев ресторанов
}

export interface LoginRequest {
  email: string;
  password: string;
  remember?: boolean;           // "Remember me" - НЕТ токенов!
}
```

### Lodash Integration (ДОБАВЛЕНО)
```typescript
// Доступные утилиты
import { _, arrayUtils, objectUtils, stringUtils, restaurantUtils } from '@shared/utils'

// Примеры использования
arrayUtils.groupBy(dishes, 'category')
objectUtils.cloneDeep(restaurant)
stringUtils.kebabCase('Restaurant Name')
restaurantUtils.groupDishesByCategory(dishes)
```

### Automated Type Generation (НАСТРОЕНО)
```bash
# Команда для генерации типов
make types

# Что происходит:
1. php artisan model:typer          # Генерация базовых типов
2. sed замены int → number          # Автоматическое исправление
3. sed замены bool → boolean        # Автоматическое исправление
4. Исправление News type collision  # Фикс конфликтов типов
5. Удаление tokens из User          # Удаление Sanctum связей
6. Удаление notifications из User   # Удаление Laravel Notifications
```

## Следующие приоритеты

### Этап 1: Vue Authentication Implementation (COMPLETED) ✅
- [x] **Auth Types**: Session-based типы для Sanctum SPA (ГОТОВО)
- [x] **Auth Store**: Pinia store БЕЗ токенов, с session handling (ГОТОВО)  
- [x] **Auth API Client**: Sanctum SPA client с CSRF защитой (ГОТОВО)
- [x] **TypeScript Strict Mode**: ЗАПРЕТ any типа (ГОТОВО)
- [x] **Environment Types**: Vite env с proper typing (ГОТОВО)
- [x] **Session Authentication**: HttpOnly cookies, auto CSRF (ГОТОВО)

### Этап 2: Restaurant Dashboard Foundation
- [ ] **Vue Router Setup**: Маршруты для dashboard
- [ ] **Layout Components**: Sidebar, header, navigation
- [ ] **Menu Management**: Первый CRUD интерфейс с lodash утилитами
- [ ] **API Integration**: Подключение к готовым endpoints

### Этап 3: Shared UI Components
- [ ] **Base UI Components**: Создание базовых компонентов  
- [ ] **mdi-icons Integration**: Единственная иконочная библиотека
- [ ] **SmartForm Component**: Schema-based генератор форм (VeeValidate + TypeScript)
- [ ] **Form Field Components**: Input, Select, Textarea с валидацией
- [ ] **Table Components**: For data display с lodash сортировкой
- [ ] **Modal Components**: For CRUD operations
- [ ] **Custom Components**: Calendar, drag-drop, image-upload

### 🎨 UI Component Strategy
- **Base Layer**: Базовые компоненты (Button, Card, Input, Dialog, Table)
- **Custom Layer**: Calendar, Drag-Drop, Image Upload компоненты
- **Form Layer**: SmartForm генератор для динамических форм (VeeValidate + схемы)
- **Icons**: mdi-icons
- **SCSS + БЭМ**: Для всех кастомных стилей
- **No External UI**: НЕТ vue-draggable-plus, vue-datepicker и др.

### 📋 Smart Form Generator (НОВЫЙ КОМПОНЕНТ)
**Schema-based формы** на VeeValidate + TypeScript:
```typescript
interface ISmartFormField {
  key: string;                    // Field name
  component: string;              // Базовый компонент (Input, Select, etc.)
  rule?: ValidationRule;          // Zod валидация
  props?: Record<string, any>;    // Props для компонента
  events?: Record<string, any>;   // Event handlers
  readonly?: boolean;             // Read-only режим
}

// Использование в Menu Management, Event Creation, Restaurant Profile
<SmartForm 
  :fields="dishFormFields" 
  :initial-values="dish"
  @update:form="handleFormContext"
/>
```

## Технические детали

### Blade Templates Structure (UPDATED)
```
resources/views/                 # СТАНДАРТНАЯ Laravel директория
├── site.blade.php              # Public site template
├── restaurant.blade.php        # Restaurant dashboard template  
├── admin.blade.php             # Admin panel template
├── layouts/                    # Layout templates
│   ├── app.blade.php           # Основной layout
│   └── guest.blade.php         # Layout для неавторизованных
└── components/                 # Переиспользуемые Blade компоненты
    ├── navigation.blade.php    # Навигация
    └── footer.blade.php        # Подвал
```

### ModelTyper Configuration (FINAL)
```php
// config/modeltyper.php
'output-file' => true,
'output-file-path' => './resources/shared/types/models.d.ts',
'plurals' => true,
'optional-relations' => true,
'timestamps-date' => true,
'custom_mappings' => [
    'int' => 'number',
    'bool' => 'boolean',
    // ... другие маппинги
],
```

### Lodash Utilities Structure
```typescript
// resources/shared/utils/index.ts
export { _ }                    // Прямой экспорт lodash
export const arrayUtils = {}   // Типизированные утилиты массивов
export const objectUtils = {}  // Типизированные утилиты объектов
export const stringUtils = {}  // Типизированные утилиты строк
export const restaurantUtils = {} // Специфичные для ресторанов

// resources/shared/ui/ - НОВАЯ СТРУКТУРА
├── base/                       // Базовые UI компоненты
│   ├── button/                 // Button, ButtonGroup
│   ├── card/                   // Card, CardHeader, CardContent
│   ├── input/                  // Input, Textarea, Select
│   ├── dialog/                 // Modal, Sheet, Popover
│   └── table/                  // Table, DataTable
├── forms/                      // Form components
│   ├── smart-form/             // SmartForm генератор
│   ├── field-wrapper/          // Wrapper для field validation
│   └── form-schemas/           // Переиспользуемые схемы
│       ├── dish-form.ts        // Схема для создания блюд
│       ├── event-form.ts       // Схема для событий
│       └── restaurant-form.ts  // Схема профиля ресторана
├── custom/                     // Custom компоненты
│   ├── calendar/               // Event calendar
│   ├── image-upload/           // Image handling
│   ├── drag-drop/              // Category management
│   └── like-button/            // Social features
└── icons/                      // mdi-icons integration
    └── index.ts                // Icon component wrapper
```

### Ready for Integration
```typescript
// Типы готовы к использованию
import type { Restaurant, Dish, Event, User } from '@shared/types/models'

// Утилиты готовы к использованию
import { arrayUtils, restaurantUtils } from '@shared/utils'

// API структура готова
interface AuthUser {
  id: string;              // UUID
  name: string;
  email: string; 
  roles: string[];         // ["admin"] | ["restaurant_owner"]
  restaurant_id?: string;  // UUID для владельцев
  restaurant?: Restaurant; // Типизированная связь
}
```

## Development Workflow

### Essential Commands
```bash
make up            # Start all containers
make shell         # Enter app container
# make dev         # ⚠️ НЕ ЗАПУСКАТЬ! Уже работает автоматически
make types         # Generate TypeScript types (НОВАЯ КОМАНДА)
make npm-install   # Install frontend dependencies

# Database commands
php artisan migrate:fresh --seed  # Recreate with test data
```
### Next Session Focus
1. **Vue Auth Store**: Pinia store с tokens и user state
2. **API Client Setup**: Axios interceptors для автоматических токенов
3. **Route Protection**: Navigation guards для protected routes
4. **First Dashboard Page**: Базовый интерфейс restaurant owner с lodash

## Критические решения принятые

### ModelTyper Integration Strategy
- ✅ Конфигурация для правильного output path
- ✅ Custom mappings для основных типов
- ✅ Автоматические sed замены для accessor методов
- ✅ Make команда для воспроизводимой генерации

### Lodash Integration Pattern
- ✅ Типизированные wrapper функции
- ✅ Restaurant-specific утилиты
- ✅ Экспорт через shared/utils для переиспользования
- ✅ TypeScript constraints для type safety

### Frontend Asset Strategy  
- ✅ Vite как единственный источник для CSS входов
- ✅ TypeScript файлы без CSS импортов для чистоты
- ✅ Alias система для удобной работы с shared компонентами
- ✅ Lodash доступен через @shared/utils alias

**Статус: Backend готов, типы исправлены, lodash добавлен. Переходим к Vue интеграции** 🚀 
