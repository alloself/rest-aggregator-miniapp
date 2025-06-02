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

## Текущий статус проекта: SANCTUM_SPA_COMPLETE ✅
*Обновлено: Аутентификация полностью переписана на Sanctum SPA, ЗАПРЕЩЕН any тип, TypeScript правильно настроен*

## Фокус текущей работы
**Session Authentication Complete** - Полностью настроена session-based аутентификация, CSRF защита, строгая типизация БЕЗ any, готов к Vue Router и компонентам.

## ⚡ ВАЖНО: Dev серверы уже запущены
**НЕ запускать `make dev` повторно!** Dev серверы работают в контейнерах автоматически:
- ✅ Vite HMR активен для всех интерфейсов 
- ✅ Vue DevTools включены и готовы
- ✅ Порты доступны: http://localhost:8080/ (site/account/admin)
- ⚠️ Повторный запуск может вызвать конфликты портов

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

### Этап 3: Shared UI Components (UPDATED STRATEGY)
- [ ] **shadcn-vue Installation**: UI library setup  
- [ ] **mdi-icons Integration**: Единственная иконочная библиотека
- [ ] **SmartForm Component**: Schema-based генератор форм (VeeValidate + TypeScript)
- [ ] **Form Field Components**: Input, Select, Textarea на основе shadcn с валидацией
- [ ] **Table Components**: For data display с lodash сортировкой на основе shadcn Table
- [ ] **Modal Components**: For CRUD operations на основе shadcn Dialog
- [ ] **Custom Components**: Calendar, drag-drop, image-upload НА ОСНОВЕ shadcn компонентов

### 🎨 UI Component Strategy
**Принцип**: Все custom компоненты строятся на базе shadcn-vue для консистентности
- **Base Layer**: shadcn-vue (Button, Card, Input, Dialog, Table)
- **Custom Layer**: Calendar, Drag-Drop, Image Upload на основе shadcn компонентов
- **Form Layer**: SmartForm генератор для динамических форм (VeeValidate + схемы)
- **Icons**: mdi-icons (НЕ lucide-vue-next)
- **No External UI**: НЕТ vue-draggable-plus, vue-datepicker и др.

### 📋 Smart Form Generator (НОВЫЙ КОМПОНЕНТ)
**Schema-based формы** на VeeValidate + TypeScript:
```typescript
interface ISmartFormField {
  key: string;                    // Field name
  component: string;              // shadcn-vue компонент (Input, Select, etc.)
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
├── shadcn/                     // Базовые shadcn-vue компоненты
│   ├── button/                 // Button, ButtonGroup
│   ├── card/                   // Card, CardHeader, CardContent
│   ├── input/                  // Input, Textarea, Select
│   ├── dialog/                 // Modal, Sheet, Popover
│   └── table/                  // Table, DataTable
├── forms/                      // Form components (НОВЫЙ СЛОЙ)
│   ├── smart-form/             // SmartForm генератор
│   ├── field-wrapper/          // Wrapper для field validation
│   └── form-schemas/           // Переиспользуемые схемы
│       ├── dish-form.ts        // Схема для создания блюд
│       ├── event-form.ts       // Схема для событий
│       └── restaurant-form.ts  // Схема профиля ресторана
├── custom/                     // Custom компоненты НА ОСНОВЕ shadcn
│   ├── calendar/               // Event calendar (shadcn Card + Button)
│   ├── image-upload/           // Image handling (shadcn Input + Card)
│   ├── drag-drop/              // Category management (shadcn Card + List)
│   └── like-button/            // Social features (shadcn Button)
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
