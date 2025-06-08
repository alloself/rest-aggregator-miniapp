# System Patterns - Архитектурные решения

## Архитектурная стратегия

### Three-Interface Strategy
Проект использует уникальную архитектуру с тремя отдельными интерфейсами:

- **Public Site (`/`)** - Презентация сервиса, каталог ресторанов, регистрация
- **Restaurant Dashboard (`/account`)** - Личный кабинет для владельцев ресторанов  
- **Admin Panel (`/admin`)** - Административное управление платформой

### Hybrid Architecture Pattern
- **Backend**: Laravel 12 с полным API
- **Frontend**: Blade templates как shell + Vue 3 SPA applications
- **Build System**: Vite с multiple entry points для каждого интерфейса

## Ключевые архитектурные решения

### 1. BaseModel Pattern
```php
abstract class BaseModel extends Model
{
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
}
```

**Принципы:**
- Все модели наследуются от BaseModel
- UUID primary keys для всех сущностей
- Единообразные casts для дат
- Общие model events

### 2. Multi-Tenant Architecture
- **Restaurant-centric**: Все данные привязаны к ресторанам
- **Role-based access**: admin, restaurant_owner роли
- **Data isolation**: Владельцы видят только свои данные
- **Centralized management**: Админы управляют всей платформой

### 3. Polymorphic Relations Pattern
```php
// Лайки для любых сущностей
$dish->likes()->create(['telegram_user_id' => $userId]);
$event->likes()->create(['telegram_user_id' => $userId]);

// Новости от разных авторов
$restaurant->news()->create([...]);
$admin->news()->create([...]);
```

### 4. Frontend Architecture Patterns

#### Feature-Sliced Design (FSD)
```
resources/
├── site/          # Public site Vue app
├── account/       # Restaurant dashboard Vue app  
├── admin/         # Admin panel Vue app
└── shared/        # Common components & utilities
    ├── api/       # API clients
    ├── composables/ # Reusable logic
    ├── stores/    # Pinia stores
    ├── types/     # TypeScript definitions
    └── utils/     # Helper functions
```

#### Component Isolation Strategy
- Каждый интерфейс имеет независимое Vue приложение
- Shared ресурсы в `resources/shared/`
- Alias пути для удобного импорта
- Отдельные entry points в Vite

### 5. Type Safety Pattern
- **FumeApp/ModelTyper**: Автоматическая генерация TS типов из Laravel моделей
- **Строгая типизация**: Запрет на `any` тип
- **API типизация**: Generic типы для всех API запросов
- **Form validation**: VeeValidate + Zod для type-safe валидации

## Database Design Patterns

### 1. UUID Strategy
```sql
-- Все основные таблицы используют UUID
CREATE TABLE restaurants (
    id UUID PRIMARY KEY,
    -- ...
);
```

### 2. Soft Deletes Pattern
- Критически важные сущности используют soft deletes
- Сохранение истории для аудита
- Возможность восстановления данных

### 3. Many-to-Many Flexibility
```php
// Блюда могут быть в нескольких категориях
$dish->categories()->attach($categoryIds);

// Сезонные категории
$summerDishes = Category::where('type', 'seasonal')
    ->where('name', 'Летние блюда')
    ->first()
    ->dishes;
```

### 4. Hierarchical Categories
```php
// Nested Set для категорий
use Kalnoy\Nestedset\NodeTrait;

class Category extends BaseModel
{
    use NodeTrait;
    // Поддержка parent-child отношений
}
```

## Authentication & Authorization Patterns

### 1. Session-Based Auth (Sanctum SPA)
```javascript
// Инициализация CSRF
await apiClient.get('/sanctum/csrf-cookie');

// Все запросы с credentials
axios.defaults.withCredentials = true;
```

### 2. Role-Based Access Control
```php
// Spatie/Laravel-Permission интеграция
Route::middleware(['auth:sanctum', 'role:admin'])->group(...);

// В моделях
public function isRestaurantOwner(): bool
{
    return $this->hasRole('restaurant_owner') && $this->restaurant_id;
}
```

## Development Patterns

### 1. Docker-First Development
- Полная контейнеризация среды разработки
- Make commands для автоматизации
- Hot Module Replacement для Vue компонентов
- Отдельные контейнеры для каждого сервиса

### 2. Automated Type Generation
```bash
# Автоматическая генерация типов
make types  # -> php artisan model:typer
```

### 3. Multiple Vite Servers Strategy
```javascript
// vite.config.ts - Multiple entry points
input: [
    "resources/site/css/app.css",
    "resources/site/ts/main.ts",
    "resources/account/css/app.css", 
    "resources/account/ts/main.ts",
    "resources/admin/css/app.css",
    "resources/admin/ts/main.ts",
]
```

## Integration Patterns

### 1. Telegram Bot Integration
```php
// DefStudio/Telegraph для Telegram API
use DefStudio\Telegraph\Facades\Telegraph;

// Encrypted bot tokens в БД
protected function casts(): array
{
    return [
        'telegram_bot_token' => 'encrypted',
    ];
}
```

### 2. File Management Pattern
- Laravel Storage с public disk
- Автоматическая оптимизация изображений
- Polymorphic file attachments

### 3. Caching Strategy
- Redis для session storage
- Query result caching
- API response caching

## Performance Patterns

### 1. Eager Loading Strategy
```php
// Предотвращение N+1 проблем
Restaurant::with(['menus.dishes.categories', 'events'])
    ->active()
    ->get();
```

### 2. Database Indexing
```php
// Стратегические индексы
$table->index(['is_active', 'created_at']);
$table->index('restaurant_id');
$table->index(['likeable_type', 'likeable_id']);
```

### 3. Frontend Optimization
- Code splitting по интерфейсам
- Lazy loading компонентов
- Asset optimization через Vite 