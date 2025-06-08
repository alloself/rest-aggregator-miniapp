# Technical Context - Технический стек и инфраструктура

## Backend Stack

### Core Framework
- **Laravel 12** - Основной PHP фреймворк
- **PHP 8.2+** - Современная версия PHP с новыми возможностями
- **MySQL 8.0** - Основная база данных
- **Redis 7** - Кеширование и session storage

### Key Laravel Packages
```json
{
  "defstudio/telegraph": "^1.59",     // Telegram Bot API integration
  "laravel/fortify": "^1.25",        // Authentication scaffolding
  "laravel/sanctum": "^4.1",         // API authentication
  "spatie/laravel-permission": "^6.19", // Role & permission management
  "spatie/laravel-query-builder": "^6.3", // API query building
  "spatie/laravel-sluggable": "^3.7", // URL slug generation
  "kalnoy/nestedset": "^6.0",        // Hierarchical data (categories)
  "telegram-bot/api": "^2.5"         // Additional Telegram API tools
}
```

### Development Tools
```json
{
  "fumeapp/modeltyper": "^3.1",      // TypeScript type generation
  "laravel/pail": "^1.2.2",         // Log monitoring
  "laravel/pint": "^1.13"           // Code formatting
}
```

## Frontend Stack

### Core Technologies
- **Vue 3.5.16** - Reactive frontend framework с Composition API
- **TypeScript 5.8.3** - Строгая типизация для всего frontend кода
- **Vite 6.3.5** - Современный build tool с HMR
- **Pinia 3.0.2** - State management для Vue 3

### UI & Styling
```json
{
  "primevue": "^4.3.5",             // UI component library
  "primeicons": "^7.0.0",           // Icon set
  "@primeuix/themes": "^1.1.1",     // Theming system
  "postcss": "^8.5.4",              // CSS processing
  "postcss-nested": "^7.0.2",       // Nested CSS support
  "autoprefixer": "^10.4.21"        // CSS vendor prefixes
}
```

### Form Handling & Validation
```json
{
  "vee-validate": "^4.15.0",        // Form validation
  "@vee-validate/zod": "^4.15.0",   // Zod integration for validation
  "zod": "^3.25.50"                 // Schema validation
}
```

### Utilities & Tools
```json
{
  "axios": "^1.9.0",                // HTTP client
  "lodash": "^4.17.21",             // Utility functions
  "chart.js": "^4.4.6",             // Data visualization
  "prettier": "^3.5.3"              // Code formatting
}
```

## Development Environment

### Docker Infrastructure
```yaml
services:
  app:        # PHP-FPM + Laravel
  nginx:      # Web server (port 8080)
  mysql:      # Database (port 3306)
  phpmyadmin: # DB management (port 8081)
  node:       # Frontend development (port 5173)
  redis:      # Caching & sessions (port 6379)
```

### Make Commands Automation
```bash
make up           # Запуск всех контейнеров
make shell        # Вход в PHP контейнер
make dev          # Запуск Vite dev серверов
make types        # Генерация TypeScript типов
make fresh        # Пересоздание БД с сидерами
make build        # Production build
```

### Vite Configuration
```typescript
// Multiple entry points для каждого интерфейса
input: [
  "resources/site/css/app.css",
  "resources/site/ts/main.ts",
  "resources/account/css/app.css", 
  "resources/account/ts/main.ts",
  "resources/admin/css/app.css",
  "resources/admin/ts/main.ts",
]

// Alias пути для удобного импорта
alias: {
  "@": "./resources",
  "@shared": "./resources/shared",
  "@types": "./resources/shared/types",
  "@site": "./resources/site",
  "@account": "./resources/account", 
  "@admin": "./resources/admin"
}
```

## Database Architecture

### UUID Strategy
```php
// BaseModel для всех сущностей
abstract class BaseModel extends Model
{
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
}
```

### Key Tables Structure
```sql
-- Основные сущности
restaurants (id: UUID, name, slug, telegram_bot_token, settings)
users (id: UUID, name, email, restaurant_id, roles)
menus (id: UUID, restaurant_id, name, is_active)
dishes (id: UUID, menu_id, name, price, image_path)
categories (id: UUID, name, type, parent_id) -- Nested Set
events (id: UUID, restaurant_id, title, start_date, end_date)

-- Связующие таблицы
dish_categories (dish_id: UUID, category_id: UUID) -- Many-to-Many
likes (id: UUID, telegram_user_id, likeable_type, likeable_id) -- Polymorphic
```

### Relationships Pattern
```php
// Many-to-Many
Dish::class -> categories() -> Category::class

// Polymorphic
Like::class -> likeable() -> Dish|Event|News
News::class -> author() -> Restaurant|User

// Hierarchical
Category::class -> parent() -> Category::class
Category::class -> children() -> Category::class
```

## Authentication & Security

### Laravel Sanctum SPA Configuration
```php
// Session-based authentication
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost')),
'guard' => ['web'],
'expiration' => null, // Session-based, no token expiration
```

### Role-Based Access Control
```php
// Spatie/Laravel-Permission
roles: ['admin', 'restaurant_owner']
permissions: ['manage-restaurants', 'manage-content', 'moderate-content']

// Middleware защита
Route::middleware(['auth:sanctum', 'role:admin'])->group(...);
```

### CSRF Protection
```javascript
// Frontend CSRF handling
await apiClient.get('/sanctum/csrf-cookie');
axios.defaults.withCredentials = true;
```

## API Architecture

### RESTful API Design
```php
// Публичные маршруты (Telegram Mini Apps)
GET /api/v1/restaurants
GET /api/v1/restaurants/{restaurant}/menu
POST /api/v1/likes/toggle

// Защищенные маршруты (Restaurant owners)
PATCH /api/v1/restaurant/profile
POST /api/v1/restaurant/dishes
PUT /api/v1/restaurant/dishes/{dish}

// Админские маршруты
GET /api/v1/admin/analytics/restaurants
POST /api/v1/admin/restaurants
PATCH /api/v1/admin/moderation/dishes/{dish}/approve
```

### API Response Format
```typescript
// Стандартизированные ответы
interface ApiResponse<T> {
  data: T;
  message?: string;
  errors?: Record<string, string[]>;
}

// Пагинация
interface PaginatedResponse<T> {
  data: T[];
  meta: {
    current_page: number;
    total: number;
    per_page: number;
  };
}
```

## Type Safety System

### Automated Type Generation
```bash
# FumeApp/ModelTyper configuration
php artisan model:typer
# Генерирует resources/shared/types/models.d.ts
```

### TypeScript Configuration
```json
{
  "compilerOptions": {
    "target": "ES2022",
    "module": "ESNext",
    "strict": true,
    "noImplicitAny": true,
    "paths": {
      "@/*": ["./resources/*"],
      "@shared/*": ["./resources/shared/*"],
      "@types/*": ["./resources/shared/types/*"]
    }
  }
}
```

### Frontend Type Usage
```typescript
// Импорт сгенерированных типов
import type { Restaurant, Dish, Event } from '@types/models';

// Типизированные API запросы
const restaurants = await apiClient.get<Restaurant[]>('/api/v1/restaurants');
const dish = await apiClient.post<Dish>('/api/v1/restaurant/dishes', data);
```

## Build & Deployment

### Development Workflow
1. **Docker containers** - Изолированная среда разработки
2. **Hot Module Replacement** - Мгновенные обновления Vue компонентов
3. **Type generation** - Автоматическая синхронизация типов
4. **Code formatting** - Laravel Pint + Prettier

### Production Considerations
- **Non-Docker deployment** - Стандартный хостинг без контейнеров
- **Asset optimization** - Vite production build с минификацией
- **Database migrations** - Автоматическое применение при развертывании
- **Environment configuration** - Отдельные настройки для production

## Performance Optimizations

### Backend Optimizations
- **Eager loading** - Предотвращение N+1 запросов
- **Database indexing** - Стратегические индексы для частых запросов
- **Redis caching** - Кеширование API ответов и сессий
- **Query optimization** - Использование Spatie/Laravel-Query-Builder

### Frontend Optimizations
- **Code splitting** - Отдельные бандлы для каждого интерфейса
- **Lazy loading** - Динамическая загрузка компонентов
- **Tree shaking** - Удаление неиспользуемого кода
- **Asset optimization** - Сжатие изображений и CSS/JS

## Monitoring & Debugging

### Development Tools
- **Laravel Pail** - Real-time log monitoring
- **Vue DevTools** - Vue component debugging
- **phpMyAdmin** - Database management interface
- **Vite HMR** - Hot module replacement для быстрой разработки

### Error Handling
```typescript
// Централизованная обработка ошибок
interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
  status: number;
}

// Error boundaries в Vue компонентах
const { handleError } = useErrorHandler();
``` 