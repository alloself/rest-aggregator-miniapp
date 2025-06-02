# System Patterns - REST Aggregator Miniapp

## Архитектурные принципы

### 1. Domain-Driven Design (DDD) - Enhanced
- **Rich Domain Models**: Restaurant, Menu, Dish, Event как бизнес-объекты
- **Aggregate Roots**: Restaurant как корневая сущность для меню и событий  
- **Value Objects**: Price, Location, DateRange для событий
- **Repository Pattern**: Абстракция доступа к сложным данным
- **Service Layer**: Бизнес-логика для рекомендаций и агрегации
- **Event-Driven**: Domain events для лайков, booking, content creation

### 2. Multi-Tenant Restaurant Platform
```
Platform Layer (Admin)
    ↓
Aggregation Layer (Collections, Recommendations)  
    ↓
Restaurant Layer (Menus, Events, News)
    ↓
Content Layer (Dishes, Categories, User Interactions)
    ↓
Mini App Layer (Telegram Integration)
```

### 3. Content Management Architecture
- **Flexible Categorization**: Many-to-many отношения для контента
- **Polymorphic Relationships**: Unified system для лайков, комментариев
- **Content Moderation**: Workflow для approval/rejection
- **Versioning**: История изменений для меню и событий

## Database Design Patterns

### 1. Core Domain Tables
```sql
-- Restaurant Aggregate
restaurants (uuid, name, description, address, phone, telegram_bot_token, settings, created_at, deleted_at)

-- Menu Management  
menus (uuid, restaurant_id, name, description, is_active, sort_order, created_at)
dishes (uuid, menu_id, name, description, price, image_url, is_available, allergens, created_at)
categories (uuid, name, slug, type, color, icon, parent_id, created_at) -- Hierarchical
dish_categories (dish_id, category_id, created_at) -- Many-to-many

-- Event System
events (uuid, restaurant_id, title, description, start_date, end_date, 
        location_lat, location_lng, location_address, max_participants, 
        price, status, created_at)
event_registrations (uuid, event_id, user_id, telegram_user_id, status, 
                    registered_at, confirmed_at)

-- Social Features  
likes (uuid, user_id, likeable_type, likeable_id, created_at)
user_favorites (uuid, user_id, favorable_type, favorable_id, created_at) -- Polymorphic
user_interactions (uuid, user_id, interaction_type, target_type, target_id, 
                  metadata, created_at) -- View, share, etc.

-- Content Aggregation
curated_collections (uuid, title, description, type, criteria, is_active, 
                    created_by, created_at)
collection_items (uuid, collection_id, item_type, item_id, sort_order, created_at)

-- News & Announcements
news (uuid, author_type, author_id, title, content, excerpt, 
      published_at, is_featured, created_at) -- Polymorphic author
```

### 2. Advanced Relationships
```sql
-- Hierarchical Categories (self-referencing)
categories.parent_id → categories.id

-- Polymorphic Likes
likes.likeable_type → 'App\Models\Dish', 'App\Models\Event', 'App\Models\News'
likes.likeable_id → dishes.id, events.id, news.id

-- Polymorphic Collections  
collection_items.item_type → 'App\Models\Dish', 'App\Models\Event'
collection_items.item_id → dishes.id, events.id

-- Geospatial Events
events with POINT(location_lat, location_lng) for spatial queries
```

### 3. Indexing Strategy
```sql
-- Performance Critical Indexes
INDEX idx_restaurants_location ON restaurants(location_lat, location_lng);
INDEX idx_events_datetime ON events(start_date, end_date);
INDEX idx_dishes_restaurant_available ON dishes(restaurant_id, is_available);
INDEX idx_likes_polymorphic ON likes(likeable_type, likeable_id);
INDEX idx_categories_hierarchy ON categories(parent_id, type);

-- Search Optimization
FULLTEXT idx_dishes_search ON dishes(name, description);
FULLTEXT idx_events_search ON events(title, description);
```

## Business Logic Patterns

### 1. Menu Management Service
```php
class MenuManagementService {
    public function createDish(Restaurant $restaurant, array $dishData): Dish
    public function assignCategories(Dish $dish, array $categoryIds): void
    public function updateSeasonalAvailability(Restaurant $restaurant, string $season): void
    public function generateMenuAnalytics(Restaurant $restaurant): MenuAnalytics
}
```

### 2. Event Planning Service  
```php
class EventPlanningService {
    public function createEvent(Restaurant $restaurant, array $eventData): Event
    public function registerUserForEvent(Event $event, User $user): EventRegistration
    public function checkAvailability(Event $event): bool
    public function sendReminders(Event $event): void
}
```

### 3. Content Aggregation Service
```php
class ContentAggregationService {
    public function createCollection(array $criteria): CuratedCollection
    public function getSeasonalDishes(string $season, ?array $geoBounds = null): Collection
    public function getTrendingEvents(string $timeframe): Collection
    public function getPersonalizedRecommendations(User $user): Collection
}
```

### 4. Social Features Service
```php
class SocialFeaturesService {
    public function toggleLike(User $user, Likeable $item): bool
    public function getPopularContent(string $type, string $timeframe): Collection
    public function trackUserInteraction(User $user, string $interaction, $target): void
    public function generateEngagementMetrics(Restaurant $restaurant): array
}
```

## Frontend Architecture Patterns

### 1. Feature-Sliced Design (FSD) Structure
```
resources/js/
├── shared/                          # Общие ресурсы
│   ├── ui/                         # UI компоненты (UPDATED STRATEGY)
│   │   ├── shadcn/                 # Базовые shadcn-vue компоненты
│   │   │   ├── button/             # Button, ButtonGroup
│   │   │   ├── card/               # Card, CardHeader, CardContent
│   │   │   ├── input/              # Input, Textarea, Select
│   │   │   ├── dialog/             # Modal, Sheet, Popover
│   │   │   └── table/              # Table, DataTable
│   │   ├── custom/                 # Custom компоненты НА ОСНОВЕ shadcn
│   │   │   ├── calendar/           # Calendar (shadcn Card + Button)
│   │   │   ├── image-upload/       # Image upload (shadcn Input + Card)
│   │   │   ├── like-button/        # Like system (shadcn Button)
│   │   │   └── drag-drop/          # Drag-drop (shadcn Card + List)
│   │   └── icons/                  # mdi-icons integration
│   ├── api/                        # API clients
│   │   ├── restaurants.ts          
│   │   ├── menus.ts
│   │   ├── events.ts
│   │   └── collections.ts
│   ├── stores/                     # Pinia stores
│   │   ├── auth.ts
│   │   ├── restaurants.ts
│   │   └── user-preferences.ts
│   └── utils/                      # Utilities
│       ├── date-helpers.ts
│       ├── geo-utils.ts
│       └── image-utils.ts
├── entities/                       # Domain entities
│   ├── restaurant/
│   │   ├── model/                  # TypeScript types
│   │   ├── ui/                     # Restaurant components
│   │   └── api/                    # Restaurant API calls
│   ├── menu/
│   │   ├── model/                  # Menu, Dish types
│   │   ├── ui/                     # Menu components  
│   │   └── api/                    # Menu CRUD
│   ├── event/
│   │   ├── model/                  # Event types
│   │   ├── ui/                     # Event components
│   │   └── api/                    # Event API
│   └── collection/
│       ├── model/                  # Collection types
│       ├── ui/                     # Collection display
│       └── api/                    # Collection API
├── features/                       # Business features
│   ├── menu-management/
│   │   ├── create-dish/            # Dish creation flow
│   │   ├── category-assignment/    # Category management
│   │   ├── bulk-operations/        # Batch editing
│   │   └── analytics/              # Menu analytics
│   ├── event-planning/
│   │   ├── create-event/           # Event creation
│   │   ├── calendar-view/          # Calendar interface
│   │   ├── registration/           # User registration
│   │   └── geolocation/            # Maps integration
│   ├── content-curation/
│   │   ├── collection-builder/     # Admin collection tools
│   │   ├── recommendation-engine/  # Smart suggestions
│   │   └── seasonal-automation/    # Automated collections
│   └── social-features/
│       ├── like-system/            # Like functionality
│       ├── favorites/              # User favorites
│       └── engagement-tracking/    # Analytics
├── widgets/                        # Composite UI blocks
│   ├── restaurant-card/            # Restaurant preview
│   ├── menu-widget/                # Menu display
│   ├── event-calendar/             # Calendar widget
│   ├── collection-carousel/        # Content collections
│   └── trending-widget/            # Popular content
└── pages/                          # Page-level components
    ├── site/                       # Public site pages
    │   ├── home/                   # Homepage with collections
    │   ├── restaurant-catalog/     # Restaurant directory
    │   └── event-calendar/         # Event discovery
    ├── restaurant/                 # Restaurant dashboard
    │   ├── dashboard/              # Analytics overview
    │   ├── menu-management/        # Menu CRUD
    │   ├── event-management/       # Event planning
    │   └── analytics/              # Performance metrics
    └── admin/                      # Admin panel
        ├── restaurant-management/   # Restaurant oversight
        ├── content-moderation/     # Content approval
        ├── collection-management/  # Curated collections
        └── platform-analytics/     # Global metrics
```

### 2. UI Component Architecture Principles (UPDATED)

#### **Component Layering Strategy**
```
UI Architecture Layers:
1. Base Layer: shadcn-vue компоненты (Button, Card, Input, Dialog, Table)
2. Custom Layer: Специфичные компоненты НА ОСНОВЕ shadcn
3. Feature Layer: Бизнес-логика компонентов
4. Page Layer: Композиция для страниц
```

#### **Custom Component Development Rules**
- ✅ **ВСЕГДА базируйтесь на shadcn-vue компонентах**
- ✅ **Icons: только mdi-icons** (НЕ lucide-vue-next)
- ❌ **ЗАПРЕЩЕНО**: vue-draggable-plus, vue-datepicker, vue-toastification
- ✅ **Консистентность**: Все custom компоненты наследуют стиль shadcn
- ✅ **Доступность**: Автоматически через shadcn базу

#### **Example Custom Component Pattern**
```typescript
// ✅ ПРАВИЛЬНО: Calendar на основе shadcn Card + Button
<template>
  <Card class="calendar-widget">
    <CardHeader>
      <CardTitle>Event Calendar</CardTitle>
    </CardHeader>
    <CardContent>
      <div class="calendar-grid">
        <Button 
          v-for="date in dates" 
          :key="date" 
          variant="ghost"
          @click="selectDate(date)"
        >
          {{ date }}
        </Button>
      </div>
    </CardContent>
  </Card>
</template>

// ❌ НЕПРАВИЛЬНО: Внешняя библиотека
import { VueDatePicker } from '@vuepic/vue-datepicker'
```

### 3. State Management Patterns
```typescript
// Pinia Store Architecture
interface MenuStore {
  dishes: Dish[]
  categories: Category[]
  selectedCategories: string[]
  
  // Actions
  createDish(dishData: CreateDishRequest): Promise<Dish>
  updateDish(dishId: string, updates: Partial<Dish>): Promise<Dish>
  assignCategories(dishId: string, categoryIds: string[]): Promise<void>
  loadMenuAnalytics(restaurantId: string): Promise<MenuAnalytics>
}

interface EventStore {
  events: Event[]
  selectedDate: Date
  selectedLocation: GeoLocation
  
  // Actions  
  createEvent(eventData: CreateEventRequest): Promise<Event>
  registerForEvent(eventId: string): Promise<EventRegistration>
  loadEventsByDateRange(start: Date, end: Date): Promise<Event[]>
}

interface SocialStore {
  userLikes: Set<string>
  favorites: Map<string, any>
  
  // Actions
  toggleLike(itemType: string, itemId: string): Promise<boolean>
  addToFavorites(item: any): Promise<void>
  trackInteraction(interaction: UserInteraction): Promise<void>
}
```

## API Design Patterns

### 1. RESTful API Structure
```
# Menu Management
GET    /api/restaurants/{id}/menus
POST   /api/restaurants/{id}/menus
GET    /api/menus/{id}/dishes
POST   /api/menus/{id}/dishes
PUT    /api/dishes/{id}
DELETE /api/dishes/{id}
POST   /api/dishes/{id}/categories/{categoryId}
DELETE /api/dishes/{id}/categories/{categoryId}

# Event Management  
GET    /api/restaurants/{id}/events
POST   /api/restaurants/{id}/events
PUT    /api/events/{id}
DELETE /api/events/{id}
POST   /api/events/{id}/register
DELETE /api/events/{id}/register

# Social Features
POST   /api/likes
DELETE /api/likes/{id}
GET    /api/content/{type}/{id}/analytics
POST   /api/favorites
DELETE /api/favorites/{id}

# Content Aggregation
GET    /api/collections
POST   /api/collections (Admin only)
GET    /api/collections/seasonal/{season}
GET    /api/collections/trending
GET    /api/recommendations/personalized
```

### 2. Complex Query Patterns
```typescript
// Advanced filtering and search
interface ContentQuery {
  type?: 'dish' | 'event' | 'news'
  categories?: string[]
  location?: GeoLocation
  dateRange?: DateRange
  priceRange?: PriceRange
  sortBy?: 'popularity' | 'date' | 'distance' | 'price'
  limit?: number
  offset?: number
}

// Geospatial queries for events
interface EventLocationQuery {
  center: GeoLocation
  radius: number // in kilometers
  dateRange?: DateRange
}
```

## Performance Optimization Patterns

### 1. Caching Strategy
```php
// Redis cache keys structure
"restaurant:{id}:menu" => Menu data with dishes
"restaurant:{id}:events:month:{YYYY-MM}" => Monthly events
"collections:seasonal:{season}" => Seasonal collections
"trending:dishes:week" => Weekly trending dishes  
"user:{id}:likes" => User's liked content
"user:{id}:recommendations" => Personalized recommendations

// Cache invalidation patterns
- Menu update → Clear restaurant menu cache
- New like → Update trending caches
- Event creation → Clear restaurant events cache
```

### 2. Database Optimization
```sql
-- Partitioning for large tables
PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026)
);

-- Read replicas for analytics
SELECT COUNT(*) FROM likes WHERE likeable_type = 'App\Models\Dish' 
  -- Executed on read replica

-- Elasticsearch for complex search
{
  "query": {
    "bool": {
      "must": [
        {"match": {"name": "pizza"}},
        {"geo_distance": {"restaurant.location": {"distance": "5km", "lat": 40.7128, "lon": -74.0060}}}
      ],
      "filter": [
        {"terms": {"categories": ["italian", "fast-food"]}}
      ]
    }
  }
}
```

### 3. Image Optimization
```typescript
// Image processing pipeline
interface ImageProcessingConfig {
  sizes: {
    thumbnail: { width: 150, height: 150 }
    card: { width: 300, height: 200 }
    detail: { width: 800, height: 600 }
  }
  formats: ['webp', 'jpg']
  quality: 85
  lazy: true
}

// CDN integration
const imageUrl = generateImageUrl(dish.image_id, 'card', 'webp')
```

## Testing Patterns

### 1. Feature Testing
```php
class MenuManagementTest extends TestCase {
    public function test_restaurant_can_create_dish_with_categories()
    public function test_dish_can_be_assigned_to_multiple_categories()  
    public function test_seasonal_menu_updates_automatically()
    public function test_menu_analytics_calculation()
}

class EventPlanningTest extends TestCase {
    public function test_event_creation_with_geolocation()
    public function test_user_event_registration_flow()
    public function test_event_capacity_limits()
}
```

### 2. Frontend Component Testing
```typescript
// Vue component tests
describe('MenuManagement', () => {
  it('should create dish with categories', async () => {
    // Test drag-and-drop category assignment
  })
  
  it('should handle image upload', async () => {
    // Test image upload flow
  })
})

describe('EventCalendar', () => {
  it('should filter events by date range', async () => {
    // Test calendar filtering
  })
})
```

## Security Patterns

### 1. Authorization Patterns
```php
// Policy-based authorization
class DishPolicy {
    public function create(User $user, Restaurant $restaurant): bool
    public function update(User $user, Dish $dish): bool
    public function delete(User $user, Dish $dish): bool
}

// Middleware for restaurant ownership
class RestaurantOwnerMiddleware {
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()->ownsRestaurant($request->route('restaurant'))) {
            abort(403);
        }
        return $next($request);
    }
}
```

### 2. Data Validation
```php
// Form Request validation
class CreateDishRequest extends FormRequest {
    public function rules(): array {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|image|max:2048'
        ];
    }
}
``` 