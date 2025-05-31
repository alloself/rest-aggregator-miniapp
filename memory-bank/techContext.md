# Technical Context - REST Aggregator Miniapp

## Технологический стек

### Backend Framework
- **Laravel 12**: Основной фреймворк (требует PHP 8.2+)
- **PHP 8.2+**: Язык разработки backend
- **Composer**: Управление PHP зависимостями

### Database & Storage
- **MySQL**: Основная реляционная база данных
- **Redis**: Кэширование, сессии, очереди
- **File Storage**: Локальное хранение для development

### Telegram Bot Integration
- **DefStudio/Telegraph v1.59**: Основной пакет для Telegram Bot API
- **telegram-bot/api v2.5**: Дополнительный Telegram API клиент
- **Webhook Architecture**: Получение обновлений от Telegram

### HTTP Client & API
- **Guzzle HTTP v7.8**: HTTP клиент для внешних API запросов
- **Predis v2.2**: Redis клиент для PHP

### Frontend & Build Tools
- **TailwindCSS v4.1.8**: CSS фреймворк (новая архитектура)
- **Vite v6.3.5**: Build tool и dev server
- **Axios v1.9.0**: HTTP клиент для frontend
- **Laravel Vite Plugin v1.2.0**: Интеграция Vite с Laravel

### Development Tools  
- **Docker & Docker Compose**: Контейнеризация
- **Make**: Автоматизация команд разработки
- **Laravel Pint**: Code style fixer
- **Laravel Pail**: Log viewing tool
- **Concurrently**: Параллельный запуск процессов

### Testing Framework
- **PHPUnit v11.0.1**: Unit и feature тестирование
- **Faker**: Генерация тестовых данных
- **Mockery**: Мокирование для тестов

## Инфраструктура разработки

### Docker Configuration
```yaml
# Основные сервисы
- app: PHP 8.2-FPM + Laravel
- nginx: Web server + SSL support  
- mysql: Database server
- redis: Cache & sessions
- phpmyadmin: Database admin interface
```

### Порты и доступ
- **8080**: Nginx (основное приложение)
- **8081**: phpMyAdmin
- **3306**: MySQL (internal)
- **6379**: Redis (internal)
- **5173**: Vite dev server

### Make Commands
```bash
make setup     # Первоначальная настройка проекта
make up        # Запуск всех контейнеров
make down      # Остановка контейнеров
make shell     # Вход в app контейнер
make composer  # Установка PHP зависимостей
make migrate   # Запуск миграций
make fresh     # Свежая установка БД
make test      # Запуск тестов
make logs      # Просмотр логов
```

## Конфигурация среды

### Environment Variables (.env)
```env
# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=rest_aggregator
DB_USERNAME=sail
DB_PASSWORD=password

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Telegram Bot
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_WEBHOOK_URL=https://your-domain.com/telegram/webhook

# Queue
QUEUE_CONNECTION=redis
```

### Конфигурация Telegraph
```php
// config/telegraph.php - потребуется создать
'bots' => [
    'default' => [
        'token' => env('TELEGRAM_BOT_TOKEN'),
        'webhook' => [
            'url' => env('TELEGRAM_WEBHOOK_URL'),
            'certificate' => env('TELEGRAM_WEBHOOK_CERTIFICATE'),
        ],
    ],
],
```

## Архитектура развертывания

### Development Environment
- **WSL2**: Windows Subsystem for Linux
- **Ubuntu 20.04**: Базовая OS
- **Docker Desktop**: Контейнеризация на Windows

### Directory Structure
```
rest-aggregator-miniapp/
├── app/                 # Laravel application
├── database/            # Migrations, seeders
├── docker/              # Docker configurations
├── memory-bank/         # Project documentation
├── resources/           # Views, assets
├── routes/              # API routes
├── storage/             # Logs, cache
├── tests/               # Test suites
├── docker-compose.yml   # Docker services
├── Makefile            # Development automation
└── .cursorrules        # Project intelligence
```

## Security Configuration

### SSL/HTTPS Setup
- SSL сертификаты в `docker/nginx/ssl/`
- Nginx конфигурация для HTTPS
- Webhook требует HTTPS для production

### Bot Security
- Webhook URL validation
- Request signature verification  
- Rate limiting middleware
- User authentication через Telegram ID

## Performance Configuration

### Redis Configuration
```
# Использование для:
- Sessions: Laravel sessions в Redis
- Cache: API responses caching
- Queues: Background job processing
- Bot State: User conversation state
```

### Database Optimization
- UTF8MB4 collation для emoji support
- UUID primary keys для distributed system compatibility
- Proper indexing для Telegram ID и timestamps

### Queue Configuration
```php
// config/queue.php
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
    ],
],
```

## Development Workflow

### Hot Reload Setup
- Vite dev server на порту 5173
- Автоматическая перезагрузка CSS/JS
- Laravel mix для asset compilation

### Debugging Tools
- Laravel Telescope (опционально)
- Laravel Pail для логов в реальном времени
- PHPMyAdmin для database inspection
- Redis CLI для cache debugging

### Code Quality Tools
- Laravel Pint для code formatting
- PHPUnit для automated testing
- Static analysis tools (опционально)

## External Integrations

### Telegram Bot API
- Webhook URL: `/telegram/webhook`
- Management endpoints для webhook setup
- DefStudio/Telegraph package configuration

### Third-party APIs
- Guzzle HTTP client для внешних запросов
- Retry logic для failed requests
- Response caching в Redis
- Error handling и logging 