# Frontend Architecture - ModelTyper Integration

## Обзор

Фронтенд проекта Restaurant Aggregator Platform теперь полностью интегрирован с **FumeApp/ModelTyper** для автоматической генерации TypeScript типов из Laravel моделей. Это обеспечивает полную типобезопасность между backend и frontend.

## Архитектурные принципы

### 1. Автогенерированные типы (ModelTyper)
- **Источник истины**: Laravel модели
- **Генерация**: `make types` или `php artisan model:typer`
- **Файл**: `resources/shared/types/models.ts`
- **Обновление**: При изменении моделей

### 2. Shared Composables & Utilities
- **Переиспользование логики**: Вся повторяющаяся логика в shared/
- **Типобезопасность**: Полная типизация с использованием сгенерированных типов
- **Модульность**: Каждый composable решает конкретную задачу

### 3. Feature-Sliced Design (FSD)
- **Структура**: По фичам, а не по типам файлов
- **Изоляция**: Каждая фича независима
- **Переиспользование**: Общие компоненты в shared/

## Структура файлов

```
resources/shared/
├── types/
│   ├── models.ts          # 🤖 Автогенерированные типы ModelTyper
│   ├── auth.ts            # Типы аутентификации (расширяют models)
│   ├── api.ts             # Типы API responses
│   └── vite-env.d.ts      # Типы окружения Vite
├── api/
│   ├── client.ts          # Axios клиент с типизацией
│   ├── auth.ts            # API для аутентификации
│   ├── restaurant.ts      # API для ресторанов
│   ├── menu.ts            # API для меню и блюд
│   └── index.ts           # Экспорт всех API services
├── composables/
│   ├── useApi.ts          # Базовый API composable
│   ├── useAuth.ts         # Аутентификация
│   ├── useRestaurant.ts   # Бизнес-логика ресторанов
│   └── index.ts           # Экспорт всех composables
├── stores/
│   └── auth.ts            # Pinia store для аутентификации
├── utils/
│   ├── formatters.ts      # Функции форматирования
│   ├── validators.ts      # Функции валидации
│   └── index.ts           # Экспорт всех utilities
└── views/                 # Общие компоненты (будущее)
```

## Workflow генерации типов

### 1. Автоматическая генерация
```bash
# Генерация типов из моделей
make types

# Или напрямую
php artisan model:typer > resources/shared/types/models.ts
```

### 2. Постобработка типов
После генерации нужно исправить:
- `int` → `number`
- `bool` → `boolean`
- Неизвестные типы → `any[]` с комментариями

### 3. Использование в коде
```typescript
// ✅ Правильно - используем сгенерированные типы
import type { Restaurant, Menu, Dish } from '../types/models';

// ❌ Неправильно - создаем типы вручную
interface Restaurant {
  id: string;
  name: string;
  // ...
}
```

## API Services Architecture

### Типизированные API классы
```typescript
export class RestaurantAPI {
  async getAll(params?: QueryParams): Promise<PaginatedResponse<Restaurant>> {
    return apiClient.get<PaginatedResponse<Restaurant>>('restaurants', { params });
  }
  
  async getById(id: string): Promise<{ data: Restaurant }> {
    return apiClient.get<{ data: Restaurant }>(`restaurants/${id}`);
  }
}
```

### Преимущества
- **Автокомплит**: IDE знает все поля моделей
- **Типобезопасность**: Ошибки типов на этапе компиляции
- **Рефакторинг**: Изменения в моделях автоматически отражаются в frontend
- **Документация**: Типы служат живой документацией API

## Composables Pattern

### useApi - базовый composable
```typescript
export function useApi() {
  const loading = ref(false);
  const error = ref<string | null>(null);

  async function apiCall<T>(apiFunction: () => Promise<T>): Promise<T | null> {
    // Обработка loading, error, try/catch
  }

  function usePagination<T>() {
    // Логика пагинации
  }

  function useCrud<T>(baseEndpoint: string) {
    // CRUD операции
  }
}
```

### useRestaurant - бизнес-логика
```typescript
export function useRestaurant() {
  const { loading, error, apiCall } = useApi();
  
  // State management
  const restaurants = ref<Restaurant[]>([]);
  
  // API calls
  async function fetchRestaurants() {
    const response = await apiCall(() => restaurantAPI.getAll());
    if (response) restaurants.value = response.data;
  }
  
  // Utility functions
  function formatPhoneNumber(phone: string | null): string {
    return formatPhoneNumber(phone); // из shared/utils
  }
}
```

## Shared Utilities

### Formatters
- `formatDate()` - форматирование дат
- `formatPrice()` - форматирование цен
- `formatPhoneNumber()` - форматирование телефонов
- `truncateText()` - обрезка текста

### Validators
- `isValidEmail()` - валидация email
- `isValidPhone()` - валидация телефона
- `validatePassword()` - проверка пароля
- `validateImageFile()` - валидация изображений

## Интеграция с Vue приложениями

### Account App (ЛК ресторанов)
```typescript
// resources/account/ts/main.ts
import { useAuthStore } from '../../shared/stores/auth';
import { useAuth, useRestaurant } from '../../shared/composables';

const App = {
  setup() {
    const authStore = useAuthStore();
    authStore.initAuth();
    
    return {
      user: authStore.user,
      isAuthenticated: authStore.isAuthenticated,
    };
  }
};
```

## Преимущества новой архитектуры

### 1. Типобезопасность
- Автоматическая синхронизация типов между backend и frontend
- Ошибки типов на этапе разработки
- Надежный рефакторинг

### 2. DRY принцип
- Переиспользование логики через composables
- Общие utility функции
- Единые API services

### 3. Масштабируемость
- Модульная архитектура
- Легкое добавление новых фич
- Изолированные компоненты

### 4. Developer Experience
- Автокомплит в IDE
- Живая документация через типы
- Быстрая разработка

## Команды разработки

```bash
# Генерация типов
make types

# Запуск dev серверов
make dev

# Установка зависимостей
make npm

# Сборка production
make npm-build
```

## Следующие шаги

1. **UI Components**: Создание компонентов с использованием новых типов
2. **Form Validation**: Интеграция VeeValidate + Zod
3. **Router Guards**: Защищенные маршруты
4. **Error Handling**: Глобальная обработка ошибок
5. **Testing**: Unit тесты для composables

Эта архитектура обеспечивает надежную основу для масштабируемого и типобезопасного frontend приложения. 