# System Patterns - REST Aggregator Miniapp

## Архитектурные принципы

### 1. Domain-Driven Design (DDD)
- **Services Layer**: Бизнес-логика инкапсулирована в сервисы
- **Repository Pattern**: Абстракция доступа к данным
- **Command Pattern**: Команды бота как отдельные классы
- **Event-Driven**: События для асинхронной обработки

### 2. Clean Architecture
```
Controllers -> Services -> Repositories -> Models
     |              |
     v              v
  Commands      Queues/Jobs
```

### 3. Bot Architecture Pattern
- **Command Registry**: Регистрация и роутинг команд бота
- **State Management**: Redis для хранения состояния диалогов
- **Middleware**: Аутентификация и авторизация пользователей
- **Response Formatting**: Унифицированное форматирование ответов

## Основные компоненты

### 1. Bot Command System
```php
abstract class BotCommand {
    abstract public function execute(Update $update): BotResponse;
    abstract public function getPattern(): string;
    abstract public function getDescription(): string;
}
```

### 2. API Client Pattern
```php
interface ApiClientInterface {
    public function makeRequest(ApiEndpoint $endpoint, array $params): ApiResponse;
    public function validateResponse(ApiResponse $response): bool;
}
```

### 3. State Management
- **Session Storage**: Redis для временного состояния
- **Conversation Context**: Контекст диалога между командами
- **User Preferences**: Настройки пользователя

### 4. Queue System
- **API Call Jobs**: Асинхронные HTTP запросы
- **Aggregation Jobs**: Обработка множественных ответов
- **Notification Jobs**: Отправка результатов пользователю

## Database Design Patterns

### 1. Core Tables
```
telegram_users (uuid, telegram_id, username, settings, created_at)
api_endpoints (uuid, user_id, name, url, method, headers, created_at)
api_calls (uuid, user_id, endpoint_id, request_data, response_data, status, created_at)
bot_commands (uuid, command, description, is_active, created_at)
user_sessions (uuid, user_id, session_data, expires_at)
```

### 2. Relationships
- User -> ApiEndpoints (1:many)
- User -> ApiCalls (1:many)
- ApiEndpoint -> ApiCalls (1:many)
- User -> UserSessions (1:many)

### 3. Indexing Strategy
- Telegram ID для быстрого поиска пользователей
- User ID + created_at для истории вызовов
- Session expiry для cleanup jobs

## Service Layer Patterns

### 1. TelegramBotService
- Обработка webhook событий
- Роутинг команд
- Форматирование ответов

### 2. ApiAggregatorService  
- Выполнение HTTP запросов
- Агрегация результатов
- Кэширование ответов

### 3. UserSessionService
- Управление состоянием диалогов
- Контекст операций
- Expire management

### 4. CommandRegistryService
- Регистрация команд бота
- Валидация паттернов
- Help generation

## Error Handling Patterns

### 1. Exception Hierarchy
```
BotException
├── InvalidCommandException
├── ApiRequestException  
├── AuthenticationException
└── ValidationException
```

### 2. Error Response Format
```json
{
    "success": false,
    "error": {
        "code": "API_REQUEST_FAILED",
        "message": "User-friendly message",
        "details": "Technical details"
    }
}
```

## Security Patterns

### 1. Bot Authentication
- Telegram User ID verification
- Rate limiting per user
- Command access control

### 2. API Security
- Headers encryption для sensitive data
- Request signing
- SSL/TLS для всех внешних запросов

### 3. Data Protection
- PII encryption в базе
- Session data expiry
- Audit logging

## Performance Patterns

### 1. Caching Strategy
- API responses caching (Redis)
- User sessions caching
- Command metadata caching

### 2. Queue Optimization
- Batch processing для множественных API calls
- Priority queues для срочных операций
- Dead letter queues для failed jobs

### 3. Database Optimization
- Connection pooling
- Query optimization
- Lazy loading для relationships

## Testing Patterns

### 1. Unit Tests
- Service layer тестирование
- Command logic тестирование
- Repository pattern mocking

### 2. Integration Tests  
- Bot webhook тестирование
- API client тестирование
- Database integration

### 3. Feature Tests
- End-to-end bot scenarios
- API aggregation workflows
- Error handling flows 