# Tailwind CSS v4 Workflow Guide - REST Aggregator Platform

## ✅ Флоу работы с Tailwind CSS v4 (Проверено с документацией)

### 🎯 **Основные принципы работы с Tailwind CSS v4:**

#### 1. **Простая инициализация**
- **Один CSS импорт**: `@import "tailwindcss";` заменяет все `@tailwind` директивы
- **Zero Configuration**: Автоматическое обнаружение исходных файлов
- **Pure CSS Only**: НЕ работает с SCSS - только чистый CSS

#### 2. **Автоматическое обнаружение контента**
Tailwind CSS v4 автоматически сканирует:
- ✅ Все файлы в проекте (кроме .gitignore)
- ✅ Исключает бинарные файлы (изображения, видео)
- ✅ Исключает CSS файлы
- ✅ Исключает lock файлы пакетных менеджеров

#### 3. **@source директива для кастомных источников**
Для явного указания источников сканирования:
```css
@import "tailwindcss";
@source "../shared";                    # Общие компоненты
@source "../account/ts";                # Компоненты аккаунта
@source "../site/ts";                   # Компоненты сайта
@source "../node_modules/@company/ui";  # Внешние UI библиотеки
```

#### 4. **CSS-first конфигурация**
```css
@import "tailwindcss";

@theme {
  --font-display: "Satoshi", "sans-serif";
  --breakpoint-3xl: 1920px;
  --color-brand-100: oklch(0.99 0 0);
  --color-brand-500: oklch(0.84 0.18 117.33);
}
```

### 🏗 **Архитектура проекта с Tailwind CSS v4:**

#### **Структура CSS файлов:**
```
resources/
├── site/css/app.css      → Site interface CSS entry
├── account/css/app.css   → Restaurant dashboard CSS entry  
├── admin/css/app.css     → Admin panel CSS entry
└── shared/              → TypeScript components (сканируется автоматически)
```

#### **Содержимое каждого CSS файла:**
```css
/* Базовый импорт Tailwind */
@import "tailwindcss";

/* Указание источников для сканирования */
@source "../site/ts";       # Для site/css/app.css
@source "../shared";        # Общие компоненты для всех

/* Кастомные CSS классы для интерфейса */
@layer components {
  .site-nav { /* ... */ }
  .hero-section { /* ... */ }
}
```

### 🔧 **Content Detection в проекте:**

#### **Автоматически сканируются:**
- `resources/site/ts/` - Vue компоненты сайта
- `resources/account/ts/` - Vue компоненты dashboard
- `resources/admin/ts/` - Vue компоненты админки
- `resources/shared/` - Общие компоненты
- `resources/views/` - Blade шаблоны

#### **Игнорируются автоматически:**
- `node_modules/` (в .gitignore)
- `storage/` (в .gitignore) 
- Бинарные файлы (jpg, png, mp4, zip)
- CSS файлы (сам Tailwind)

### 🎨 **Theme Configuration:**

#### **Кастомизация дизайн-токенов:**
```css
@theme {
  /* Цветовая палитра */
  --color-restaurant-primary: oklch(0.84 0.18 117.33);
  --color-restaurant-accent: oklch(0.53 0.12 118.34);
  
  /* Типографика */
  --font-display: "Inter", "sans-serif";
  --text-hero: 4rem;
  --text-hero--line-height: 4.5rem;
  
  /* Брейкпоинты */
  --breakpoint-restaurant-wide: 1440px;
  
  /* Кастомные утилиты */
  --ease-restaurant: cubic-bezier(0.3, 0, 0, 1);
}
```

#### **Использование CSS переменных в runtime:**
```css
.custom-component {
  background: var(--color-restaurant-primary);
  font-family: var(--font-display);
  transition-timing-function: var(--ease-restaurant);
}
```

### 🧩 **Safelisting классов:**

#### **Принудительная генерация классов:**
```css
@import "tailwindcss";

/* Safelist конкретных классов */
@source inline("underline font-bold");

/* Safelist с вариантами */
@source inline("{hover:,focus:,}bg-restaurant-{100,200,300}");

/* Safelist с диапазонами */
@source inline("grid-cols-{1,2,3,4,5,6}");
```

#### **Исключение классов:**
```css
/* Исключить классы из генерации */
@source not inline("container");
@source not "../legacy-components";
```

### 🚀 **Оптимизация производительности:**

#### **Базовый путь для сканирования:**
```css
@import "tailwindcss" source("../src");
```

#### **Отключение автоматического обнаружения:**
```css
@import "tailwindcss" source(none);
@source "../site";
@source "../account"; 
@source "../admin";
```

### 🎯 **Кастомные утилиты и варианты:**

#### **Добавление кастомных утилит:**
```css
@utility tab-4 {
  tab-size: 4;
}

@utility restaurant-card {
  @apply rounded-lg border border-gray-200 p-6 shadow-sm;
  transition: all 0.2s ease;
}
```

#### **Кастомные варианты:**
```css
@variant restaurant-active (&[data-active="true"]);
@variant mobile-only (@media (max-width: 768px));

/* Использование: restaurant-active:bg-green-100 mobile-only:hidden */
```

### 📋 **Workflow команды разработки:**

#### **1. Разработка компонентов:**
```vue
<template>
  <!-- Tailwind классы автоматически обнаруживаются -->
  <div class="restaurant-card hover:shadow-md transition-all">
    <h3 class="text-restaurant-primary font-display">{{ restaurant.name }}</h3>
  </div>
</template>
```

#### **2. Build процесс:**
```bash
# Development с HMR
npm run dev

# Production build
npm run build

# Проверка CSS генерации
npx tailwindcss -i resources/site/css/app.css -o dist/site.css
```

#### **3. Отладка классов:**
```css
/* Временный safelist для отладки */
@source inline("debug-red debug-blue debug-green");

/* CSS для отладки */
@utility debug-red { background: red !important; }
@utility debug-blue { background: blue !important; }
@utility debug-green { background: green !important; }
```

### ⚠️ **Важные ограничения и советы:**

#### **НЕ работают динамические классы:**
```javascript
// ❌ НЕПРАВИЛЬНО - классы не будут сгенерированы
const color = 'red';
className = `text-${color}-500`;

// ✅ ПРАВИЛЬНО - полные имена классов
const colorMap = {
  red: 'text-red-500',
  blue: 'text-blue-500'
};
className = colorMap[color];
```

#### **SCSS больше не поддерживается:**
```css
/* ❌ Не работает в v4 */
@import "tailwindcss/base";
@import "tailwindcss/components"; 
@import "tailwindcss/utilities";

/* ✅ Правильно в v4 */
@import "tailwindcss";
```

#### **Миграция с v3:**
```bash
# Автоматическая миграция проекта
npx @tailwindcss/upgrade@next
```

### 🔍 **Troubleshooting:**

#### **Классы не генерируются:**
1. Проверьте @source директивы
2. Убедитесь что файл не в .gitignore
3. Используйте safelist для тестирования
4. Проверьте синтаксис класса

#### **Проблемы с производительностью:**
1. Ограничьте source paths  
2. Исключите большие директории через @source not
3. Используйте source(none) для точного контроля

### 🎯 **Конкретно для REST Aggregator Platform:**

#### **Interface-specific источники:**
```css
/* site/css/app.css */
@import "tailwindcss";
@source "../site/ts";
@source "../shared";

/* account/css/app.css */ 
@import "tailwindcss";
@source "../account/ts";
@source "../shared";

/* admin/css/app.css */
@import "tailwindcss";
@source "../admin/ts";
@source "../shared";
```

#### **Общая тема для всех интерфейсов:**
```css
@theme {
  --color-restaurant-primary: oklch(0.65 0.13 142.5);
  --color-restaurant-secondary: oklch(0.45 0.09 240);
  --font-restaurant: "Inter", system-ui, sans-serif;
  --breakpoint-restaurant-xl: 1440px;
}
```

Этот workflow обеспечивает максимальную производительность и правильную работу Tailwind CSS v4 в многоинтерфейсном Laravel приложении. 