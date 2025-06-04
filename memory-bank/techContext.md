# Technical Context - REST Aggregator Miniapp

## Технологический стек

### Backend Framework
- **Laravel 12**: Основной фреймворк (требует PHP 8.2+)
- **PHP 8.2+**: Язык разработки backend
- **Composer**: Управление PHP зависимостей

### Database & Storage
- **MySQL**: Основная реляционная база данных
- **Redis**: Кэширование, сессии, очереди
- **File Storage**: Локальное хранение для development

### Frontend Stack (Updated)
- **Vue 3 Latest**: Прогрессивный JavaScript фреймворк
- **TypeScript**: Статическая типизация для JavaScript
- **Pinia**: State management для Vue 3
- **Vue Router**: Клиентский роутинг
- **VeeValidate**: Валидация форм для Vue
- **Zod**: Schema validation library
- **SCSS**: CSS препроцессор для кастомных стилей
- **shadcn-vue**: UI компоненты для Vue (https://github.com/unovue/shadcn-vue)
- **mdi-icons**: Единственная иконочная библиотека (НЕ lucide-vue-next)
- **SCSS**: CSS preprocessor
- **БЭМ Methodology**: CSS naming convention

### Архитектурные паттерны
- **Feature-Sliced Design (FSD)**: Архитектурная методология для frontend
- **Blade + Vue Hybrid**: Laravel Blade как оболочка, Vue SPA внутри
- **Multi-App Architecture**: Отдельные Vue приложения для каждого интерфейса

### Telegram Integration
- **DefStudio/Telegraph v1.59**: Laravel пакет для Telegram Bot API
- **Multiple Bot Support**: Поддержка множества bot токенов
- **Mini Apps Architecture**: Развертывание персональных Mini Apps

### HTTP Client & API
- **Guzzle HTTP v7.8**: HTTP клиент для внешних API запросов
- **Predis v2.2**: Redis клиент для PHP

### Build Tools & Development
- **Vite v6.3.5**: Build tool и dev server с поддержкой multiple entry points
- **Laravel Vite Plugin v1.2.0**: Интеграция Vite с Laravel
- **Axios v1.9.0**: HTTP клиент для frontend
- **TypeScript Compiler**: Компиляция TypeScript в JavaScript

### Development Tools  
- **Docker & Docker Compose**: Контейнеризация
- **Make**: Автоматизация команд разработки
- **Laravel Pint**: Code style fixer
- **Laravel Pail**: Log viewing tool
- **FumeApp/ModelTyper**: Автоматическая генерация TypeScript типов из Laravel моделей

### Testing Framework
- **PHPUnit v11.0.1**: Unit и feature тестирование backend
- **Vitest**: Unit тестирование Vue компонентов
- **Vue Test Utils**: Тестирование Vue компонентов
- **Faker**: Генерация тестовых данных
- **Mockery**: Мокирование для тестов

## Архитектура приложения

### Three-Interface Architecture
```
Application Root
├── Site Interface (/)          # Публичный сайт
├── Restaurant Dashboard (/restaurant) # ЛК ресторанов
└── Admin Panel (/admin)        # Административная панель
```

### Frontend Structure (FSD)
```
resources/js/
├── site/                      # Публичный сайт Vue app
│   ├── app/                   # Конфигурация приложения
│   ├── pages/                 # Страницы сайта
│   ├── widgets/               # Составные UI блоки
│   ├── features/              # Бизнес-фичи
│   ├── entities/              # Бизнес-сущности
│   ├── shared/                # Переиспользуемый код
│   └── main.ts                # Entry point
├── restaurant/                # ЛК ресторанов Vue app
│   ├── app/
│   ├── pages/
│   ├── widgets/
│   ├── features/
│   ├── entities/
│   ├── shared/
│   └── main.ts
├── admin/                     # Админка Vue app
│   ├── app/
│   ├── pages/
│   ├── widgets/
│   ├── features/
│   ├── entities/
│   ├── shared/
│   └── main.ts
└── shared/                    # Общие компоненты между приложениями
    ├── ui/                    # shadcn-vue компоненты
    ├── api/                   # API клиенты
    ├── stores/                # Pinia stores
    ├── composables/           # Vue composables
    └── utils/                 # Утилиты
```

### Blade Templates Structure
```
resources/
├── shared/                   # Общие ресурсы между всеми приложениями
│   ├── types/                # TypeScript типы (ModelTyper + custom)
│   │   ├── models.d.ts       # Автогенерируемые типы Laravel моделей
│   │   └── api.d.ts          # Ручные типы для API responses
│   ├── api/                  # API клиенты
│   ├── stores/               # Pinia stores
│   ├── composables/          # Vue composables
│   └── utils/                # Утилиты
├── views/                    # Blade Templates (STANDARD LOCATION)
│   ├── site.blade.php        # Основной template для сайта
│   ├── restaurant.blade.php  # Template для ЛК ресторанов  
│   ├── admin.blade.php       # Template для админки
│   ├── layouts/              # Layout templates
│   └── components/           # Переиспользуемые Blade компоненты
└── js/                       # Vue.js приложения (используют shared/types)
```

## Инфраструктура разработки

### Development Environment (Docker)
```yaml
# Основные сервисы для разработки
- app: PHP 8.2-FPM + Laravel
- nginx: Web server + SSL support  
- mysql: Database server
- redis: Cache & sessions
- phpmyadmin: Database admin interface
- node: Node.js для frontend build
```

### Production Environment (Standard Hosting)
```bash
# Требования к хостингу
- PHP 8.2+ с расширениями: mysql, redis, gd, fileinfo, mbstring
- MySQL/MariaDB 8.0+
- Redis (опционально, fallback на file cache)
- Apache/Nginx web server
- SSL сертификат
- Composer support
- Node.js для build процесса (на deploy)
```

### Порты и доступ
- **8080**: Nginx (основное приложение)
- **8081**: phpMyAdmin
- **3306**: MySQL (internal)
- **6379**: Redis (internal)
- **5173**: Vite dev server (site)
- **5174**: Vite dev server (restaurant)
- **5175**: Vite dev server (admin)

### Vite Configuration (Multiple Entry Points)
```typescript
// vite.config.ts
export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/js/site/main.ts',
        'resources/js/restaurant/main.ts', 
        'resources/js/admin/main.ts'
      ],
      refresh: true,
    }),
    vue(),
  ],
  resolve: {
    alias: {
      '@': '/resources/js',
      '@shared': '/resources/js/shared',
    },
  },
});
```

### Package.json Dependencies (Updated)
```json
{
  "devDependencies": {
    "@vitejs/plugin-vue": "^5.0.0",
    "typescript": "^5.0.0",
    "vue": "^3.4.0",
    "vue-router": "^4.3.0",
    "pinia": "^2.1.0",
    "@vee-validate/zod": "^4.12.0",
    "vee-validate": "^4.12.0",
    "zod": "^3.22.0",
    "sass": "^1.70.0",
    "sass": "^1.77.0"
  },
  "dependencies": {
    "axios": "^1.9.0"
  }
}
```

## Конфигурация среды

### Development Environment Variables (.env)
```env
# Database (Docker containers)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=rest_aggregator
DB_USERNAME=sail
DB_PASSWORD=password

# Redis (Docker)
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
CACHE_DRIVER=redis

# Development URLs
APP_URL=http://localhost:8080
VITE_APP_URL=${APP_URL}

# Storage (Docker volumes)
FILESYSTEM_DISK=local

# Frontend Build
VITE_DEV_SERVER_HOST=0.0.0.0
VITE_DEV_SERVER_PORT=5173
```

### Production Environment Variables (.env.production)
```env
# Database (Standard hosting)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_production_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Redis (fallback to file if unavailable)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
CACHE_DRIVER=redis
SESSION_DRIVER=file

# Production URLs
APP_URL=https://yourdomain.com
VITE_APP_URL=${APP_URL}

# Storage (Public symlink for images)
FILESYSTEM_DISK=public

# Production optimizations
APP_ENV=production
APP_DEBUG=false
QUEUE_CONNECTION=database
LOG_CHANNEL=daily
```

### TypeScript Configuration (ОБНОВЛЕНО)

#### СТРОГИЕ ПРАВИЛА:
- **🚫 ЗАПРЕЩЕНО использовать `any` тип!** 
- Используйте `unknown`, конкретные типы или type guards
- При API запросах указывайте generic типы: `apiClient.get<User>('/api/user')`

#### Конфигурация tsconfig.json:
```json
{
  "compilerOptions": {
    "target": "ES2022",
    "useDefineForClassFields": true,
    "module": "ESNext",
    "moduleResolution": "bundler",
    "strict": true,
    "jsx": "preserve",
    "resolveJsonModule": true,
    "isolatedModules": true,
    "esModuleInterop": true,
    "allowSyntheticDefaultImports": true,
    "forceConsistentCasingInFileNames": true,
    "lib": ["ES2022", "DOM", "DOM.Iterable"],
    "skipLibCheck": true,
    "allowJs": true,
    "noEmit": true,
    "baseUrl": ".",
    "paths": {
      "@/*": ["./resources/*"],
      "@shared/*": ["./resources/shared/*"],
      "@site/*": ["./resources/site/*"],
      "@account/*": ["./resources/account/*"],
      "@admin/*": ["./resources/admin/*"]
    },
    "types": ["node"],
    "typeRoots": ["./node_modules/@types/", "./resources/shared/types"]
  },
  "include": [
    "resources/**/*.ts",
    "resources/**/*.d.ts", 
    "resources/**/*.tsx",
    "resources/**/*.vue",
    "resources/**/*.js"
  ],
  "exclude": ["node_modules", "dist", "vendor", "bootstrap/cache"],
  "references": [{ "path": "./tsconfig.node.json" }]
}
```

#### Типизация Vite Environment:
```typescript
// resources/shared/types/vite-env.d.ts
/// <reference types="vite/client" />

// Расширяем стандартный Vite интерфейс нашими переменными
interface ImportMetaEnv {
  readonly VITE_API_URL: string;
  readonly VITE_APP_NAME: string;
  readonly VITE_APP_ENV: string;
  // добавьте другие переменные по мере необходимости
}
```

#### Принципы типизации:
1. **Используйте ModelTyper типы**: Автоматически генерируемые типы из Laravel моделей
2. **Type Guards вместо any**: `isApiError()`, `isPaginationResponse()`  
3. **Generic API calls**: `apiClient.get<Restaurant[]>('/api/restaurants')`
4. **Strict mode**: Включен строгий режим TypeScript

## Security Configuration

### CSP Headers для Vue Apps
```php
// В каждом Blade template
<meta http-equiv="Content-Security-Policy" content="
  default-src 'self';
  script-src 'self' 'unsafe-eval';
  style-src 'self' 'unsafe-inline';
  connect-src 'self' ws: wss:;
">
```

### Authentication Strategy
- **Laravel Sanctum**: API authentication
- **Session-based**: Web authentication
- **Role-based access**: Restaurant owners vs Admins

## Performance Configuration

### Frontend Optimization
```typescript
// Vite build optimization
build: {
  rollupOptions: {
    output: {
      manualChunks: {
        vendor: ['vue', 'vue-router', 'pinia'],
        ui: ['@headlessui/vue', 'lucide-vue-next']
      }
    }
  }
}
```

### Caching Strategy
- **Laravel Response Cache**: Для публичных страниц
- **Redis Cache**: API responses и computed data
- **Browser Cache**: Static assets через Vite
- **Vue Keep-Alive**: Кэширование компонентов

## Development Workflow

### Make Commands (Updated)
```bash
make setup         # Первоначальная настройка проекта
make up            # Запуск всех контейнеров
make down          # Остановка контейнеров
make shell         # Вход в app контейнер
make npm-install   # Установка Node.js зависимостей
make dev           # Запуск Vite dev серверов
make build         # Production build всех Vue apps
make test          # Запуск всех тестов
make types         # Генерация TypeScript типов из моделей (ModelTyper)
```

### Hot Reload Setup
- **Multiple Vite Servers**: Отдельный dev server для каждого интерфейса
- **HMR Support**: Hot Module Replacement для Vue компонентов
- **TypeScript Watch**: Автоматическая проверка типов
- **SCSS Watch**: Автоматическая компиляция стилей

### Debugging Tools
- **Vue DevTools**: Браузерное расширение для Vue
- **TypeScript Language Service**: IDE support
- **Vite Inspector**: Анализ bundle composition
- **Laravel Telescope**: Backend debugging

## External Integrations

### shadcn-vue Integration
- **Component Library**: Готовые accessible компоненты
- **Customization**: Адаптация под наш design system
- **Tree Shaking**: Импорт только используемых компонентов
- **TypeScript Support**: Полная типизация компонентов

### Telegram Mini Apps
- **Multiple Bot Tokens**: Поддержка разных ресторанов
- **Webhook Management**: Автоматическое управление webhook
- **Real-time Updates**: WebSocket connections для live updates 