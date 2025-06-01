# UUID Compatibility Guide

## Проблема
При добавлении пакетов `spatie/laravel-permission`, `spatie/laravel-query-builder`, `spatie/laravel-sluggable` возникла проблема несовместимости миграций с архитектурной концепцией использования UUID для всех primary keys.

## Обнаруженные проблемы
1. **Spatie Permission миграции** использовали `bigIncrements('id')` вместо UUID
2. **Polymorphic relationships** требовали настройки для UUID совместимости
3. **Конфигурация пакета** не была адаптирована для UUID архитектуры

## Примененное решение

### 1. Адаптация миграции Permission Tables
**Файл**: `database/migrations/2025_06_01_114841_create_permission_tables.php`

Изменения:
- `bigIncrements('id')` → `uuid('id')->primary()`
- `unsignedBigInteger()` → `uuid()` для всех foreign keys
- Сохранение всех constraints и indexes

### 2. Создание кастомных моделей с UUID поддержкой

**Permission Model** (`app/Models/Permission.php`):
```php
use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Permission extends SpatiePermission
{
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
}
```

**Role Model** (`app/Models/Role.php`):
```php
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Role extends SpatieRole
{
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
}
```

### 3. Обновление конфигурации пакета
**Файл**: `config/permission.php`

```php
'models' => [
    'permission' => App\Models\Permission::class,
    'role' => App\Models\Role::class,
],
```

### 4. Интеграция с User моделью
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasRoles;
    // ...
}
```

## Тестирование решения

Создан тест `tests/Feature/UuidCompatibilityTest.php` для проверки:
- UUID generation для всех моделей
- Корректная работа role assignment
- Корректная работа permission assignment
- Polymorphic relationships с UUID

## Рекомендации для будущих пакетов

### Checklist при добавлении новых пакетов:
1. ✅ Проверить миграции на использование `bigIncrements`
2. ✅ Адаптировать primary keys к UUID
3. ✅ Создать кастомные модели если необходимо
4. ✅ Обновить конфигурацию пакета
5. ✅ Добавить тесты для UUID совместимости
6. ✅ Обновить Memory Bank documentation

### Паттерн решения:
1. **Анализ**: Изучить миграции пакета
2. **Адаптация**: Заменить auto-increment на UUID
3. **Модели**: Создать кастомные модели с `HasUuids`
4. **Конфигурация**: Обновить config пакета
5. **Тестирование**: Добавить тесты совместимости

## Известные ограничения
- Некоторые пакеты могут требовать integer IDs для оптимизации
- Performance impact от UUID вместо integer (минимальный)
- Сложность миграции existing data (для future cases)

## Статус
✅ **Решено**: Spatie Permission полностью совместим с UUID архитектурой
🔄 **В процессе**: Мониторинг других пакетов на совместимость
📋 **Задача**: Создать automated check для будущих миграций 