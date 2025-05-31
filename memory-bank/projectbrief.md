# REST Aggregator Miniapp - Project Brief

## Описание проекта
REST Aggregator Miniapp - это Laravel 12 приложение с интеграцией Telegram бота для агрегации вызовов REST API. Проект предназначен для предоставления удобного интерфейса через Telegram бота для выполнения HTTP запросов к различным внешним API и агрегации результатов.

## Основные цели
1. **Telegram Bot Interface**: Предоставить пользователям возможность взаимодействия с внешними API через Telegram бота
2. **API Aggregation**: Агрегировать и обрабатывать результаты от множественных REST API вызовов  
3. **User Management**: Управление пользователями бота и их сессиями
4. **Request Logging**: Логирование всех API вызовов для анализа и отладки
5. **Queue Processing**: Асинхронная обработка длительных запросов через очереди

## Ключевые функции
- Регистрация и аутентификация пользователей через Telegram
- Настройка и управление внешними API endpoints
- Выполнение HTTP запросов к внешним сервисам
- Агрегация и форматирование ответов
- Система команд бота для различных операций
- Сессионное управление диалогами с ботом
- Кэширование результатов запросов
- Фоновая обработка сложных операций

## Технические требования
- Laravel 12 (PHP 8.2+)
- MySQL для основной базы данных
- Redis для кэширования, сессий и очередей
- Telegram Bot API через DefStudio/Telegraph пакет
- Docker контейнеры для разработки
- TailwindCSS v4 для frontend
- Vite 6.3.5 для сборки ассетов

## Архитектурные принципы
- Container-first development с Docker
- Webhook-based Telegram bot communication
- Queue-driven architecture для тяжелых операций
- Command pattern для bot operations
- Service layer для бизнес-логики
- UUID primary keys для уникальности
- Redis state management для bot sessions

## Окружение разработки
- WSL2 Linux контейнеры
- Make-based automation
- Hot reload development server
- Integrated debugging tools
- SSL support для production webhook

## Целевая аудитория
Разработчики и интеграторы, которым нужен быстрый способ тестирования и агрегации данных от различных REST API через удобный Telegram интерфейс. 