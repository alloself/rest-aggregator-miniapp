# 🐳 Docker окружение для Telegram Hotel

Это Docker окружение предоставляет полный стек для разработки Laravel приложения с Telegram Bot интеграцией.

## 🏗️ Архитектура

### Сервисы:
- **app** - PHP 8.3-FPM с Laravel
- **nginx** - Веб-сервер Nginx
- **mysql** - База данных MySQL 8.0
- **phpmyadmin** - Веб-интерфейс для управления БД
- **node** - Node.js 20 для frontend разработки
- **redis** - Кеширование и очереди

### Порты:
- **8080** - Laravel приложение (Nginx)
- **8081** - phpMyAdmin
- **5173** - Vite dev server
- **3306** - MySQL
- **6379** - Redis

## 🚀 Быстрый старт

### 1. Клонирование и настройка
```bash
# Перейти в папку проекта
cd rest-aggregator-miniapp

# Скопировать файл окружения
cp .env.example .env

# Отредактировать .env файл с настройками для Docker:
# DB_HOST=mysql
# DB_DATABASE=telegram_hotel
# DB_USERNAME=laravel_user
# DB_PASSWORD=laravel_password
# REDIS_HOST=redis
# CACHE_STORE=redis
# QUEUE_CONNECTION=redis
```

### 2. Запуск проекта
```bash
# Полная настройка проекта (первый запуск)
make setup

# Или пошагово:
make build    # Собрать образы
make up       # Запустить контейнеры
make composer # Установить PHP зависимости
make key-generate # Сгенерировать APP_KEY
make migrate  # Выполнить миграции
```

### 3. Доступ к приложению
- **Приложение**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - Сервер: `mysql`
  - Пользователь: `laravel_user`
  - Пароль: `laravel_password`

## 📋 Основные команды

### Управление контейнерами
```bash
make up       # Запустить все контейнеры
make down     # Остановить все контейнеры
make restart  # Перезапустить контейнеры
make logs     # Показать логи всех контейнеров
make shell    # Войти в контейнер приложения
```

### PHP/Laravel команды
```bash
make composer              # Установить зависимости
make artisan cmd="migrate" # Выполнить artisan команду
make migrate              # Выполнить миграции
make migrate-fresh        # Пересоздать БД
make seed                 # Выполнить сидеры
make test                 # Запустить тесты
make cache-clear          # Очистить кеш
```

### Frontend команды
```bash
make npm       # Установить Node.js зависимости
make npm-build # Собрать frontend для продакшена
```

### Полезные команды
```bash
make help        # Показать все доступные команды
make permissions # Установить правильные права доступа
make clean       # Удалить все контейнеры и volumes
```

## 🔧 Конфигурация

### Структура Docker файлов:
```
docker/
├── php/
│   ├── Dockerfile    # PHP-FPM образ
│   └── local.ini     # PHP конфигурация
├── nginx/
│   └── default.conf  # Nginx конфигурация
└── mysql/
    └── my.cnf        # MySQL конфигурация
```

### Переменные окружения (.env):
```env
# База данных
DB_HOST=mysql
DB_DATABASE=telegram_hotel
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_password

# Redis
REDIS_HOST=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis

# URL приложения
APP_URL=http://localhost:8080
```

## 🐛 Отладка

### Просмотр логов:
```bash
make logs           # Все логи
make logs-app       # Логи PHP приложения
make logs-nginx     # Логи Nginx
make logs-mysql     # Логи MySQL
```

### Подключение к контейнерам:
```bash
make shell          # Bash в контейнере приложения
make shell-mysql    # MySQL клиент
```

### Проблемы с правами доступа:
```bash
make permissions    # Исправить права доступа
```

## 📦 Установка дополнительных пакетов

### PHP пакеты:
```bash
make shell
composer require package-name
```

### Node.js пакеты:
```bash
docker-compose exec node npm install package-name
```

## 🔄 Обновление

### Обновление зависимостей:
```bash
make composer-update  # PHP зависимости
docker-compose exec node npm update  # Node.js зависимости
```

### Пересборка образов:
```bash
make build  # Пересобрать все образы
```

## 💾 Бэкап и восстановление

### Бэкап базы данных:
```bash
docker-compose exec mysql mysqldump -u laravel_user -p telegram_hotel > backup.sql
```

### Восстановление базы данных:
```bash
docker-compose exec -T mysql mysql -u laravel_user -p telegram_hotel < backup.sql
```

## 🚨 Важные замечания

1. **Первый запуск**: Обязательно выполните `make setup` для полной настройки
2. **Права доступа**: Если возникают проблемы с правами, выполните `make permissions`
3. **Порты**: Убедитесь, что порты 8080, 8081, 3306, 5173, 6379 свободны
4. **Производительность**: Для лучшей производительности на Windows используйте WSL2

## 📞 Поддержка

При возникновении проблем:
1. Проверьте логи: `make logs`
2. Перезапустите контейнеры: `make restart`
3. Очистите кеш: `make cache-clear`
4. Пересоберите образы: `make build` 