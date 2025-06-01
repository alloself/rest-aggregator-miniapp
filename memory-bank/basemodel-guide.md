# BaseModel Architecture Guide

## Обзор архитектуры

Создана унифицированная архитектура моделей с базовой моделью для обеспечения консистентности UUID primary keys во всем проекте.

## Компоненты архитектуры

### 1. BaseModel (Abstract)
**Файл**: `app/Models/BaseModel.php`

```php
abstract class BaseModel extends Model
{
    use HasUuids;
    
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
```

**Назначение**: Базовая модель для всех обычных моделей проекта.

**Преимущества**:
- Автоматические UUID primary keys
- Общие cast настройки
- Единообразие архитектуры
- Место для общих model events

## Паттерны использования

### Для обычных моделей (наследование от BaseModel)
```php
class TelegramUser extends BaseModel
{
    use HasFactory;
    
    protected $fillable = [
        'telegram_id',
        'username',
        // ...
    ];
    
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'telegram_id' => 'integer',
            'settings' => 'array',
        ]);
    }
}
```

### Для специальных моделей (использование HasUuids)
```php
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasRoles;
    
    public $incrementing = false;
    protected $keyType = 'string';
    
    // ... остальная логика
}
```

### Для моделей третьих пакетов
```php
class Permission extends SpatiePermission
{
    use HasUuids;
    
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'name',
        'guard_name',
    ];
}
```

## Миграции

Все миграции должны использовать UUID primary keys:

```php
Schema::create('table_name', function (Blueprint $table) {
    $table->uuid('id')->primary();
    // ... другие поля
    $table->timestamps();
});
```

## Тестирование

### UUID Validation Helper
```php
private function isValidUuid(string $uuid): bool
{
    return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[47][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid) === 1;
}
```

Поддерживает UUID v4 и v7 (Laravel 12 использует v7 по умолчанию).

### Тестирование моделей
```php
public function test_model_uses_uuid(): void
{
    $model = ModelName::create([...]);
    
    $this->assertIsString($model->id);
    $this->assertTrue($this->isValidUuid($model->id));
}
```

## Преимущества архитектуры

### 1. Консистентность
- Все модели используют UUID
- Единообразные настройки
- Предсказуемое поведение

### 2. DRY принцип
- Общие cast настройки в BaseModel
- Минимальное дублирование UUID настроек
- Простая архитектура без лишних абстракций

### 3. Гибкость
- BaseModel для обычных моделей
- HasUuids для специальных случаев
- Совместимость с третьими пакетами

### 4. Тестируемость
- Единообразные тесты
- Общие helper методы
- Покрытие всех сценариев

## Рекомендации

### При создании новых моделей:
1. **Обычные модели**: Наследуйтесь от `BaseModel`
2. **Аутентификация**: Используйте `HasUuids` в `Authenticatable`
3. **Третьи пакеты**: Используйте `HasUuids` + явные настройки
4. **Миграции**: Всегда используйте `uuid('id')->primary()`

### При работе с relationships:
```php
// Foreign keys тоже должны быть UUID
$table->uuid('user_id');
$table->foreign('user_id')->references('id')->on('users');
```

### При тестировании:
- Всегда проверяйте UUID generation
- Тестируйте relationships с UUID
- Используйте общий helper для validation

## Статус
✅ **Реализовано**: BaseModel, все основные модели с UUID  
✅ **Упрощено**: Удален лишний trait, используется Laravel's HasUuids напрямую  
✅ **Протестировано**: Unit и Feature тесты покрывают всю функциональность  
📋 **Готово к использованию**: Архитектура готова для новых моделей 