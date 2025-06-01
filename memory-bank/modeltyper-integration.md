# ModelTyper Integration - Restaurant Aggregator Platform

## Обзор интеграции

[FumeApp/ModelTyper](https://github.com/fumeapp/modeltyper) автоматически генерирует TypeScript интерфейсы из Laravel моделей, обеспечивая полную типобезопасность между backend и frontend в Restaurant Aggregator Platform.

## Установка и настройка

### 🔧 **Установка ModelTyper**
```bash
# В Docker контейнере
make shell
composer require --dev fumeapp/modeltyper

# Публикация конфигурации (опционально)
php artisan vendor:publish --provider="FumeApp\ModelTyper\ModelTyperServiceProvider" --tag=config
```

### 🧹 **Очистка нежелательных полей**
**Проблема**: ModelTyper автоматически генерирует поля `tokens` и `notifications` для User модели от Laravel Sanctum и Notifications трейтов.

**Решение**: Использование кастомных интерфейсов ModelTyper для исключения нежелательных полей:

```php
// app/Models/User.php
public array $interfaces = [
    'tokens' => [
        'type' => 'never',
    ],
    'notifications' => [
        'type' => 'never',
    ],
];

protected $hidden = [
    'password',
    'remember_token', 
    'two_factor_recovery_codes',
    'two_factor_secret',
    'tokens',           // Скрываем Sanctum tokens от ModelTyper
    'notifications',    // Скрываем notifications от ModelTyper
];
```

**Дополнительно**:
1. **Исключение моделей**: PersonalAccessToken и DatabaseNotification исключены в конфигурации
2. **Кастомные типы**: Для accessor методов используются кастомные интерфейсы

### ⚙️ **Конфигурация для проекта**
```php
// config/modeltyper.php
return [
    'output_file' => resource_path('shared/types/models.d.ts'),
    
    'models_directory' => app_path('Models'),
    
    'custom_mappings' => [
        // UUID mapping
        'string' => 'string',
        'uuid' => 'string',
        
        // Encrypted fields
        'encrypted' => 'string',
        
        // JSON fields
        'json' => 'Record<string, any>',
        'array' => 'any[]',
        
        // Restaurant specific
        'price' => 'number',
        'coordinates' => 'Point | null',
    ],
    
    'include_relations' => true,
    'include_timestamps' => true,
    'optional_relations' => false,
    'plurals' => true,
];
```

## Структура генерируемых типов

### 📁 **Файловая структура**
```
resources/shared/types/
├── models.d.ts           # Автогенерируемые типы Laravel моделей
├── api.d.ts              # Ручные типы для API responses
├── forms.d.ts            # Типы для Vue форм
└── components.d.ts       # Типы для Vue компонентов
```

### 🏪 **Restaurant Domain Types**
```typescript
// resources/shared/types/models.d.ts (автогенерируется)

export interface Restaurant {
  // columns
  id: string;
  name: string;
  description: string;
  address?: string;
  phone?: string;
  telegram_bot_token: string;  // encrypted
  is_active: boolean;
  created_by_admin_id: string;
  created_at?: Date;
  updated_at?: Date;
  deleted_at?: Date;
  
  // relations
  menus: Menu[];
  events: Event[];
  owner: User;
  likes: Like[];
}

export interface Menu {
  id: string;
  restaurant_id: string;
  name: string;
  description?: string;
  is_active: boolean;
  sort_order: number;
  created_at?: Date;
  updated_at?: Date;
  
  // relations
  restaurant: Restaurant;
  dishes: Dish[];
}

export interface Dish {
  id: string;
  menu_id: string;
  name: string;
  description?: string;
  price: number;
  image_path?: string;
  is_available: boolean;
  allergens?: string[];
  created_at?: Date;
  updated_at?: Date;
  
  // relations
  menu: Menu;
  categories: Category[];
  likes: Like[];
}

export interface Category {
  id: string;
  name: string;
  slug: string;
  type: 'seasonal' | 'cuisine' | 'custom';
  color?: string;
  icon?: string;
  parent_id?: string;
  created_at?: Date;
  updated_at?: Date;
  
  // relations
  dishes: Dish[];
  parent?: Category;
  children: Category[];
}

export interface Event {
  id: string;
  restaurant_id: string;
  title: string;
  description?: string;
  start_date: Date;
  end_date: Date;
  max_participants?: number;
  price?: number;
  status: 'draft' | 'published' | 'cancelled';
  created_at?: Date;
  updated_at?: Date;
  
  // relations
  restaurant: Restaurant;
  registrations: EventRegistration[];
  likes: Like[];
}

export interface EventRegistration {
  id: string;
  event_id: string;
  user_id?: string;
  telegram_user_id: string;
  status: 'pending' | 'confirmed' | 'cancelled';
  registered_at: Date;
  confirmed_at?: Date;
  
  // relations
  event: Event;
}

export interface Like {
  id: string;
  telegram_user_id: string;
  likeable_type: 'App\\Models\\Dish' | 'App\\Models\\Event' | 'App\\Models\\News';
  likeable_id: string;
  created_at?: Date;
}

export interface CuratedCollection {
  id: string;
  title: string;
  description?: string;
  type: 'seasonal' | 'trending' | 'thematic' | 'geographic';
  criteria: Record<string, any>;
  is_active: boolean;
  created_by_admin_id: string;
  created_at?: Date;
  updated_at?: Date;
  
  // relations
  items: CollectionItem[];
  creator: User;
}

export interface CollectionItem {
  id: string;
  collection_id: string;
  item_type: 'App\\Models\\Dish' | 'App\\Models\\Event';
  item_id: string;
  sort_order: number;
  created_at?: Date;
  
  // relations
  collection: CuratedCollection;
}

export interface News {
  id: string;
  author_type: 'App\\Models\\User' | 'App\\Models\\Restaurant';
  author_id: string;
  title: string;
  content: string;
  excerpt?: string;
  is_featured: boolean;
  published_at?: Date;
  created_at?: Date;
  updated_at?: Date;
  
  // relations
  likes: Like[];
}

// Collection types
export type Restaurants = Restaurant[];
export type Menus = Menu[];
export type Dishes = Dish[];
export type Categories = Category[];
export type Events = Event[];
export type EventRegistrations = EventRegistration[];
export type Likes = Like[];
export type CuratedCollections = CuratedCollection[];
export type CollectionItems = CollectionItem[];
export type NewsArticles = News[];
```

## Laravel Models Requirements

### 🏗️ **Model Setup для ModelTyper**

```php
// app/Models/Restaurant.php
class Restaurant extends BaseModel
{
    protected $casts = [
        'telegram_bot_token' => 'encrypted',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];
    
    // REQUIRED: Return type annotations
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }
    
    public function events(): HasMany  
    {
        return $this->hasMany(Event::class);
    }
    
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_admin_id');
    }
    
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}

// app/Models/Dish.php
class Dish extends BaseModel
{
    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'allergens' => 'array',
    ];
    
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
    
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'dish_categories');
    }
    
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
    
    // REQUIRED: Mutator return types
    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value): string => '₽' . number_format($this->price, 0)
        );
    }
}

// app/Models/Category.php  
class Category extends BaseModel
{
    protected $casts = [
        'type' => CategoryTypeEnum::class,  // Enum support
    ];
    
    public function dishes(): BelongsToMany
    {
        return $this->belongsToMany(Dish::class, 'dish_categories');
    }
    
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
```

### 🔢 **Enum Support**
```php
// app/Enums/CategoryTypeEnum.php
/**
 * @property SEASONAL - Сезонные блюда
 * @property CUISINE - Тип кухни
 * @property CUSTOM - Пользовательская категория
 */
enum CategoryTypeEnum: string
{
    case SEASONAL = 'seasonal';
    case CUISINE = 'cuisine';
    case CUSTOM = 'custom';
}
```

## Frontend Integration

### 🔄 **Type-Safe API Layer**
```typescript
// resources/js/shared/api/base.ts
import type { Restaurant, Restaurants, Dish, Dishes } from '@types/models';

class BaseApi<T, TCollection> {
  constructor(private endpoint: string) {}
  
  async getAll(): Promise<TCollection> {
    const response = await axios.get(`/api/${this.endpoint}`);
    return response.data;
  }
  
  async getById(id: string): Promise<T> {
    const response = await axios.get(`/api/${this.endpoint}/${id}`);
    return response.data;
  }
  
  async create(data: Partial<T>): Promise<T> {
    const response = await axios.post(`/api/${this.endpoint}`, data);
    return response.data;
  }
  
  async update(id: string, data: Partial<T>): Promise<T> {
    const response = await axios.put(`/api/${this.endpoint}/${id}`, data);
    return response.data;
  }
  
  async delete(id: string): Promise<void> {
    await axios.delete(`/api/${this.endpoint}/${id}`);
  }
}

// Typed API clients
export const restaurantsApi = new BaseApi<Restaurant, Restaurants>('restaurants');
export const dishesApi = new BaseApi<Dish, Dishes>('dishes');
```

### 🗄️ **Type-Safe Pinia Stores**
```typescript
// resources/js/shared/stores/menu.ts
import { defineStore } from 'pinia';
import type { Menu, Menus, Dish, Dishes, Category, Categories } from '@types/models';

export const useMenuStore = defineStore('menu', {
  state: () => ({
    menus: [] as Menus,
    currentMenu: null as Menu | null,
    dishes: [] as Dishes,
    categories: [] as Categories,
    loading: false,
    error: null as string | null,
  }),
  
  getters: {
    availableDishes: (state): Dishes => 
      state.dishes.filter(dish => dish.is_available),
      
    dishesByCategory: (state) => (categoryId: string): Dishes =>
      state.dishes.filter(dish => 
        dish.categories.some(cat => cat.id === categoryId)
      ),
      
    totalDishes: (state): number => state.dishes.length,
  },
  
  actions: {
    async fetchMenus(restaurantId: string): Promise<void> {
      this.loading = true;
      this.error = null;
      
      try {
        this.menus = await menusApi.getByRestaurant(restaurantId);
      } catch (error) {
        this.error = 'Failed to fetch menus';
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async createDish(menuId: string, dishData: Partial<Dish>): Promise<Dish> {
      const dish = await dishesApi.create({
        ...dishData,
        menu_id: menuId,
      });
      
      this.dishes.push(dish);
      return dish;
    },
    
    async assignCategories(dishId: string, categoryIds: string[]): Promise<void> {
      await dishesApi.assignCategories(dishId, categoryIds);
      
      // Update local state
      const dish = this.dishes.find(d => d.id === dishId);
      if (dish) {
        dish.categories = this.categories.filter(cat => 
          categoryIds.includes(cat.id)
        );
      }
    }
  }
});
```

### 📝 **Type-Safe Forms**
```typescript
// resources/js/restaurant/features/menu-management/CreateDishForm.vue
<script setup lang="ts">
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import { z } from 'zod';
import type { Dish, Category } from '@types/models';

// Schema соответствует Laravel модели
const createDishSchema = toTypedSchema(z.object({
  name: z.string().min(1, 'Название обязательно').max(255),
  description: z.string().optional(),
  price: z.number().min(0, 'Цена должна быть положительной'),
  is_available: z.boolean().default(true),
  allergens: z.array(z.string()).optional(),
  category_ids: z.array(z.string()).min(1, 'Выберите хотя бы одну категорию'),
}));

interface Props {
  menuId: string;
  categories: Category[];
}

interface Emits {
  (e: 'dish-created', dish: Dish): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const { handleSubmit, isSubmitting } = useForm({
  validationSchema: createDishSchema,
});

const onSubmit = handleSubmit(async (values) => {
  try {
    const dish = await dishesApi.create({
      ...values,
      menu_id: props.menuId,
    });
    
    emit('dish-created', dish);
  } catch (error) {
    // Handle error
  }
});
</script>
```

## Make Command Integration

### 📝 **Makefile Updates**
```makefile
# Добавить в Makefile
.PHONY: types
types: ## Generate TypeScript types from Laravel models
	@echo "🔧 Generating TypeScript types..."
	@docker-compose exec app php artisan model:typer \
		--output-file=resources/shared/types/models.d.ts \
		--plurals \
		--optional-relations \
		--timestamps-date
	@echo "✅ TypeScript types generated successfully!"

.PHONY: types-watch
types-watch: ## Watch for model changes and regenerate types
	@echo "👀 Watching for model changes..."
	@while true; do \
		inotifywait -q -r -e modify app/Models/; \
		$(MAKE) types; \
		sleep 1; \
	done
```

## Development Workflow

### 🔄 **Type Generation Workflow**
```bash
# 1. Создание/изменение Laravel модели
vim app/Models/Restaurant.php

# 2. Генерация TypeScript типов
make types

# 3. Использование в Vue компонентах
# Типы автоматически доступны во всех Vue приложениях
```

### ⚡ **Git Hooks Integration**
```bash
# .git/hooks/pre-commit
#!/bin/bash
echo "Checking if TypeScript types are up to date..."

# Regenerate types
make types

# Check if types changed
if ! git diff --quiet resources/shared/types/models.d.ts; then
    echo "❌ TypeScript types are outdated. Please commit the updated types."
    exit 1
fi

echo "✅ TypeScript types are up to date."
```

## Benefits для Restaurant Aggregator

### 🎯 **Type Safety Benefits**
1. **Compile-time errors** при несоответствии API
2. **IntelliSense support** для моделей ресторанов
3. **Refactoring safety** при изменении структуры БД
4. **API contract enforcement** между backend и frontend
5. **Reduced runtime errors** в production

### 🚀 **Developer Experience**
1. **Auto-completion** для всех полей моделей
2. **Type hints** в IDE для отношений
3. **Validation assistance** при создании форм
4. **Documentation** через TypeScript interfaces

ModelTyper integration обеспечивает полную типобезопасность между Laravel backend и Vue 3 frontend в Restaurant Aggregator Platform! 