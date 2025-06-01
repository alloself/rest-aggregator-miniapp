# Типы и API клиент

Этот документ описывает типизацию и использование API клиента в проекте Restaurant Aggregator Platform.

## Структура типов

### Автоматически генерируемые типы

- **`models.d.ts`** - типы Laravel моделей, генерируемые через `fumeapp/modeltyper`
- **`api.d.ts`** - типы API ответов и запросов (написаны вручную)
- **`auth.ts`** - типы для session-based аутентификации
- **`index.ts`** - центральный экспорт всех типов

### Type Guards

```typescript
import { isApiError, isPaginationResponse } from '@shared/types';

if (isApiError(response)) {
  console.error(response.message);
}

if (isPaginationResponse(response)) {
  console.log(response.data, response.meta);
}
```

## API Client (Sanctum SPA)

Проект использует **Laravel Sanctum SPA** аутентификацию (session-based), а не токены.

### Основные принципы

1. **Session cookies** вместо Bearer токенов
2. **CSRF защита** через cookies
3. **withCredentials: true** для отправки cookies
4. **Автоматическая обработка** 419 (CSRF) и 401 (Unauthorized) ошибок

### Использование

```typescript
import { apiClient } from '@shared/api/client';

// Аутентификация
await apiClient.login({ 
  email: 'user@example.com', 
  password: 'password' 
});

// Получение данных пользователя
const user = await apiClient.user();

// API запросы (автоматически включают session cookie)
const restaurants = await apiClient.get<PaginationResponse<Restaurant>>('/api/restaurants');

// Выход
await apiClient.logout();
```

### Автоматические возможности

- **CSRF Cookie**: Автоматически получается перед первым POST запросом
- **Retry на 419**: При CSRF ошибке токен обновляется и запрос повторяется
- **Auth Events**: При 401 ошибке отправляется `auth:unauthorized` событие

### Настройка environment

```env
VITE_API_URL=http://localhost:8000
```

## Важные отличия от токенной аутентификации

❌ **НЕ используем:**
- Bearer токены в headers
- localStorage для токенов
- Ручное управление токенами

✅ **Используем:**
- Session cookies (HttpOnly, secure)
- CSRF protection
- withCredentials для axios
- Automatic cookie handling

## События аутентификации

```typescript
// Слушаем события потери аутентификации
window.addEventListener('auth:unauthorized', () => {
  // Обновляем состояние UI, перенаправляем на логин
  router.push('/login');
});
```

## Обновление типов

```bash
# Генерация типов моделей из Laravel
make types
```

Эта команда запускает `php artisan model:typer` внутри Docker контейнера. 