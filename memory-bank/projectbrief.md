# REST Aggregator Miniapp - Project Brief

## Описание проекта
REST Aggregator Miniapp - это Laravel 12 сервис-агрегатор для ресторанов, который позволяет ресторанам создавать страницы своих заведений и развертывать персональные Telegram Mini Apps для взаимодействия с клиентами.

## Основные цели
1. **Restaurant Aggregator Platform**: Централизованная платформа для ресторанов с возможностью создания профилей заведений
2. **Telegram Mini Apps**: Автоматическое развертывание персональных Telegram Mini Apps для каждого ресторана
3. **Multi-Interface Architecture**: Три отдельных интерфейса - основной сайт, ЛК ресторанов, админка
4. **Bot Token Management**: Рестораны указывают свои Telegram bot токены для управления Mini Apps
5. **Centralized Control**: Сервис полностью управляет всеми развернутыми Telegram Mini Apps

## Ключевые функции
- Регистрация и управление ресторанами на платформе
- Создание профилей ресторанов с меню, контактами, описанием
- Интеграция с Telegram Bot API через токены ресторанов
- Автоматическое развертывание и управление Telegram Mini Apps
- Личный кабинет для ресторанов с аналитикой и настройками
- Административная панель для управления всей платформой
- Публичный сайт для презентации сервиса и ресторанов

## Архитектура интерфейсов
### Three-Blade Architecture (STANDARD LOCATION)
1. **Main Site Blade** (`resources/views/site.blade.php`) - публичный сайт сервиса
2. **Restaurant Dashboard Blade** (`resources/views/restaurant.blade.php`) - ЛК ресторанов  
3. **Admin Panel Blade** (`resources/views/admin.blade.php`) - админка сервиса

**Blade Templates Structure:**
- `resources/views/` - стандартная Laravel директория для view файлов
- `resources/views/layouts/` - базовые layout template
- `resources/views/components/` - переиспользуемые Blade компоненты

### Frontend Architecture - FSD (Feature-Sliced Design)
- **Vue 3 Latest + TypeScript** - основной фреймворк
- **Pinia** - state management
- **Vue Router** - маршрутизация  
- **VeeValidate + Zod** - валидация форм
- **SCSS + БЭМ** - стилизация

## Технические требования
- Laravel 12 (PHP 8.2+) - backend framework
- MySQL для основной базы данных ресторанов и пользователей  
- Redis для кэширования и сессий
- Multiple Telegram Bot API integration через токены ресторанов
- Docker контейнеры для разработки
- Vue 3 + TypeScript SPA внутри Blade templates

## Архитектурные принципы
- **Multi-Tenant Architecture**: Поддержка множества ресторанов
- **Telegram Mini Apps Integration**: Персональные боты для каждого ресторана
- **Blade + Vue Hybrid**: Laravel Blade как оболочка, Vue как SPA внутри
- **Feature-Sliced Design**: Структурирование frontend кода по фичам
- **Type-Safe Development**: TypeScript + Zod для безопасности типов

## Основные сущности
- **Restaurants**: Профили ресторанов с данными и настройками
- **Telegram Bots**: Привязка bot токенов к ресторанам
- **Mini Apps**: Развернутые Telegram Mini Apps для каждого ресторана  
- **Users**: Администраторы платформы и владельцы ресторанов
- **Analytics**: Статистика использования Mini Apps

## Окружение разработки
- WSL2 Linux контейнеры
- Make-based automation  
- Hot reload для Vue development
- Integrated debugging tools
- Multi-environment support (site/restaurant/admin)

## Целевая аудитория
**Рестораны и заведения общественного питания**, которым нужен простой способ создать присутствие в Telegram через персональные Mini Apps без необходимости разработки собственного решения. 