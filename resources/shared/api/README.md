# API Client - Sanctum SPA Migration

## Миграция от токенной к session-based аутентификации

### Что изменилось

**Удалено:**
- ❌ Bearer токены и localStorage
- ❌ Методы `setToken()`, `getToken()`, `clearToken()`
- ❌ Authorization headers

**Добавлено:**
- ✅ Session-based аутентификация через cookies
- ✅ Автоматическое получение CSRF cookie
- ✅ `withCredentials: true` для отправки cookies
- ✅ Автоматическая обработка 419 (CSRF) ошибок
- ✅ События `auth:unauthorized` при потере сессии

### Новый workflow

```typescript
// 1. Логин (автоматически получает CSRF cookie)
await apiClient.login({ email, password });

// 2. Запросы автоматически используют session cookie
const user = await apiClient.user();
const restaurants = await apiClient.get('/api/restaurants');

// 3. Логаут (очищает session)
await apiClient.logout();
```

### Конфигурация Laravel

Убедитесь, что настроено:

```php
// config/sanctum.php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1')),

// config/cors.php
'supports_credentials' => true,
'paths' => ['api/*', '/login', '/logout', '/sanctum/csrf-cookie'],

// config/session.php  
'domain' => env('SESSION_DOMAIN', 'localhost'),
```

### Автоматические возможности

- **CSRF защита**: Токен автоматически получается и отправляется
- **Retry механизм**: При 419 ошибке токен обновляется и запрос повторяется
- **Auth события**: При 401 ошибке отправляется событие потери аутентификации
- **Session cookies**: HttpOnly, secure cookies для безопасности

### Использование в store

```typescript
// Слушаем события потери аутентификации
window.addEventListener('auth:unauthorized', () => {
  authStore.clearAuth();
  router.push('/login');
});
``` 