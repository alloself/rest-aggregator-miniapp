# ModelTyper Configuration Solution

## 🎯 **Проблема**
Команда `php artisan model:typer` генерировала некорректные TypeScript типы:
- `int` вместо `number`
- `bool` вместо `boolean` 
- Нежелательные поля `tokens` и `notifications` в User интерфейсе
- Timestamps как строки вместо Date объектов

## ✅ **Решение**

### 1. **Конфигурация ModelTyper**
```php
// config/modeltyper.php
return [
    'output-file' => true,
    'output-file-path' => './resources/shared/types/models.d.ts',
    'timestamps-date' => true,        // Date объекты вместо строк
    'plurals' => true,               // Генерация plural типов
    'optional-nullables' => true,    // Nullable поля как optional
    'no-hidden' => true,             // Исключение hidden полей
    'excluded_models' => [
        'PersonalAccessToken',
        'DatabaseNotification', 
        'FailedJob',
        'Job',
        'JobBatch',
        'PasswordReset',
    ],
];
```

### 2. **Кастомные интерфейсы в моделях**

#### User модель - исключение нежелательных полей:
```php
// app/Models/User.php
public array $interfaces = [
    'tokens' => [
        'type' => 'never',
    ],
    'notifications' => [
        'type' => 'never',
    ],
];

protected $hidden = [
    'password',
    'remember_token',
    'two_factor_recovery_codes',
    'two_factor_secret',
    'tokens',           // Скрываем Sanctum tokens
    'notifications',    // Скрываем notifications
];
```

#### Модели с accessor методами - правильные типы:
```php
// app/Models/Dish.php
public array $interfaces = [
    'likes_count' => [
        'type' => 'number',
    ],
];

public function getLikesCountAttribute(): float
{
    return (float) $this->likes()->count();
}
```

```php
// app/Models/Event.php
public array $interfaces = [
    'is_active' => [
        'type' => 'boolean',
    ],
    'likes_count' => [
        'type' => 'number',
    ],
];

public function getIsActiveAttribute(): bool
{
    $now = now();
    return $this->start_date <= $now && $this->end_date >= $now;
}

public function getLikesCountAttribute(): float
{
    return (float) $this->likes()->count();
}
```

```php
// app/Models/News.php
public array $interfaces = [
    'likes_count' => [
        'type' => 'number',
    ],
];

public function getLikesCountAttribute(): float
{
    return (float) $this->likes()->count();
}
```

### 3. **Упрощенная команда Make**
```makefile
# Makefile
types: ## Generate TypeScript types from Laravel models
	@echo "$(GREEN)🔧 Generating TypeScript types...$(NC)"
	@docker-compose exec app php artisan model:typer
	@echo "$(GREEN)✅ TypeScript types generated successfully!$(NC)"
```

## 🎉 **Результат**

### Корректный User интерфейс:
```typescript
export interface User {
  // columns
  id: string
  name: string
  email: string
  email_verified_at?: Date | null
  restaurant_id?: string | null
  two_factor_confirmed_at?: Date | null
  created_at?: Date | null
  updated_at?: Date | null
  deleted_at?: Date | null
  // overrides
  tokens: never
  notifications: never
  // relations
  restaurant?: Restaurant
  roles?: Roles
  permissions?: Permissions
}
```

### Корректные accessor типы:
```typescript
export interface Dish {
  // ...
  // overrides
  likes_count: number  // ✅ вместо int
  // ...
}

export interface Event {
  // ...
  // overrides
  is_active: boolean   // ✅ вместо bool
  likes_count: number  // ✅ вместо int
  // ...
}
```

### Корректные timestamps:
```typescript
created_at?: Date | null     // ✅ вместо string
updated_at?: Date | null     // ✅ вместо string
```

## 🔑 **Ключевые принципы**

1. **Кастомные интерфейсы** - основной способ переопределения типов
2. **Правильная конфигурация** - включение нужных опций в config/modeltyper.php
3. **Исключение системных моделей** - через excluded_models
4. **Типобезопасные accessor методы** - возвращают правильные PHP типы
5. **Минимальная постобработка** - ModelTyper генерирует корректные типы сразу

## 📝 **Команды**

```bash
# Генерация типов
make types

# Или напрямую
make shell
php artisan model:typer
```

Теперь команда `php artisan model:typer` генерирует корректные TypeScript типы без необходимости постобработки!

## 📁 **Итоговая структура типов**

Очищена структура типов от дублирования:

```
resources/shared/types/
├── index.ts              # 🎯 Центральный экспорт всех типов  
├── models.d.ts           # ✨ Автогенерируемые Laravel модели (ModelTyper)
├── api.d.ts              # 🔧 Ручные API response типы
├── auth.ts               # 🔐 Типы аутентификации  
├── vite-env.d.ts         # ⚡ Vite environment типы
└── README.md             # 📚 Документация
```

**Удалены дублирующие файлы**:
- ❌ `models.ts` (устаревшие типы с неправильными форматами)
- ❌ `api.ts` (реэкспортировал неправильные типы)

**Центральный импорт**:
```typescript
// ✅ Используйте центральный импорт
import type { Restaurant, User, ApiResponse } from '@shared/types';

// ❌ Избегайте прямых импортов  
import type { Restaurant } from '../types/models';
``` 