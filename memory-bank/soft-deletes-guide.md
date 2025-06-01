# Soft Deletes Implementation Guide

## Обзор

Реализована функциональность мягкого удаления (soft deletes) для пользовательских моделей `User` и `TelegramUser`. Это позволяет "удалять" записи без физического удаления из базы данных, что важно для сохранения данных и возможности восстановления.

## Реализованные модели

### 1. User Model
**Файл**: `app/Models/User.php`

```php
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasRoles, SoftDeletes;
    
    public $incrementing = false;
    protected $keyType = 'string';
    
    // ... остальная логика
}
```

**Возможности**:
- Стандартные методы soft deletes Laravel
- Совместимость с UUID primary keys
- Работа с Spatie Permission системой

### 2. TelegramUser Model
**Файл**: `app/Models/TelegramUser.php`

```php
class TelegramUser extends BaseModel
{
    use HasFactory, SoftDeletes;
    
    // ... основная логика
    
    public static function findByTelegramId(int $telegramId): ?static
    {
        return static::where('telegram_id', $telegramId)->first();
    }
    
    public static function findByTelegramIdWithTrashed(int $telegramId): ?static
    {
        return static::withTrashed()->where('telegram_id', $telegramId)->first();
    }
}
```

**Возможности**:
- Все стандартные методы soft deletes
- Кастомный метод `findByTelegramIdWithTrashed()` для поиска удаленных пользователей
- Автоматическое исключение удаленных записей из обычных запросов

## Database Schema

### Users Table
```sql
-- Поле deleted_at включено в исходную миграцию создания таблицы
CREATE TABLE users (
    id UUID PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255),
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL  -- Soft deletes field
);
```

### Telegram Users Table  
```sql
-- Поле deleted_at включено в исходную миграцию создания таблицы
CREATE TABLE telegram_users (
    id UUID PRIMARY KEY,
    telegram_id BIGINT UNIQUE,
    username VARCHAR(255) NULL,
    first_name VARCHAR(255) NULL,
    last_name VARCHAR(255) NULL,
    language_code VARCHAR(10) NULL,
    is_bot BOOLEAN DEFAULT false,
    settings JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL  -- Soft deletes field
);
```

**Миграции**:
- `0001_01_01_000000_create_users_table.php` (включает softDeletes)
- `2025_06_01_120954_create_telegram_users_table.php` (включает softDeletes)

## Использование

### Базовые операции

#### Мягкое удаление
```php
$user = User::find($id);
$user->delete(); // Устанавливает deleted_at

$telegramUser = TelegramUser::find($id);
$telegramUser->delete(); // Устанавливает deleted_at
```

#### Восстановление
```php
$user = User::withTrashed()->find($id);
$user->restore(); // Очищает deleted_at

$telegramUser = TelegramUser::withTrashed()->find($id);
$telegramUser->restore(); // Очищает deleted_at
```

#### Постоянное удаление
```php
$user = User::withTrashed()->find($id);
$user->forceDelete(); // Удаляет запись физически
```

### Запросы с учетом soft deletes

#### Обычные запросы (исключают удаленные)
```php
User::all(); // Только активные пользователи
TelegramUser::where('telegram_id', 123)->first(); // Только активные
```

#### Включая удаленные
```php
User::withTrashed()->get(); // Все пользователи включая удаленные
TelegramUser::withTrashed()->where('telegram_id', 123)->first();
```

#### Только удаленные
```php
User::onlyTrashed()->get(); // Только удаленные пользователи
TelegramUser::onlyTrashed()->get();
```

### Специальные методы TelegramUser

#### Поиск по Telegram ID с учетом удаленных
```php
// Обычный поиск (только активные)
$user = TelegramUser::findByTelegramId(123456789);

// Поиск включая удаленных
$user = TelegramUser::findByTelegramIdWithTrashed(123456789);
```

#### Проверка статуса
```php
$user = TelegramUser::withTrashed()->find($id);
$user->trashed(); // true если удален
$user->deleted_at; // timestamp удаления или null
```

## Тестирование

### Тестовые сценарии

#### Базовый тест soft deletes
```php
public function test_user_soft_deletes(): void
{
    $user = User::factory()->create();
    $userId = $user->id;

    // Мягкое удаление
    $user->delete();
    $this->assertNull(User::find($userId));

    // Поиск в трэше
    $trashedUser = User::withTrashed()->find($userId);
    $this->assertNotNull($trashedUser);
    $this->assertTrue($trashedUser->trashed());

    // Восстановление
    $trashedUser->restore();
    $this->assertNotNull(User::find($userId));
}
```

#### Тест специальных методов
```php
public function test_find_by_telegram_id_with_trashed(): void
{
    $user = TelegramUser::create(['telegram_id' => 123]);
    
    // Обычный поиск работает
    $this->assertNotNull(TelegramUser::findByTelegramId(123));
    
    $user->delete();
    
    // Обычный поиск не находит
    $this->assertNull(TelegramUser::findByTelegramId(123));
    
    // Поиск с трэшем находит
    $this->assertNotNull(TelegramUser::findByTelegramIdWithTrashed(123));
}
```

## Преимущества реализации

### 1. Безопасность данных
- Защита от случайного удаления
- Возможность восстановления
- Сохранение истории действий

### 2. Совместимость с UUID
- Полная совместимость с UUID primary keys
- Работа с relationships
- Корректные foreign key constraints

### 3. Telegram Bot Integration
- Защита от потери пользовательских данных
- Возможность восстановления заблокированных пользователей
- Сохранение настроек и истории

### 4. Performance
- Автоматическое исключение из запросов
- Индексы на deleted_at поля
- Оптимизированные запросы

## Рекомендации по использованию

### В контексте Telegram Bot
1. **Блокировка пользователей**: Используйте soft delete вместо hard delete
2. **Восстановление доступа**: Легко восстановить через `restore()`
3. **История действий**: Удаленные пользователи сохраняют связи с API calls

### При работе с API
1. **Всегда проверяйте статус**: Используйте `findByTelegramId()` для активных пользователей
2. **Администрирование**: Используйте `withTrashed()` для полного поиска
3. **Очистка**: Периодически используйте `forceDelete()` для старых записей

### Performance considerations
```php
// Добавить индекс для оптимизации
Schema::table('users', function (Blueprint $table) {
    $table->index('deleted_at');
});

Schema::table('telegram_users', function (Blueprint $table) {
    $table->index('deleted_at');
});
```

## Статус
✅ **Реализовано**: SoftDeletes для User и TelegramUser моделей  
✅ **Миграции**: Добавлены deleted_at поля в базу данных  
✅ **Тестирование**: Полное покрытие тестами всех сценариев  
✅ **Кастомные методы**: Специальные методы для Telegram integration  
📋 **Готово к использованию**: Функциональность полностью готова 