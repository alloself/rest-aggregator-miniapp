// API Response Types для Restaurant Aggregator Platform
// Эти типы НЕ генерируются автоматически и создаются вручную
// Типы моделей импортируются из models.d.ts (автогенерируемый ModelTyper)

// Generic API Response wrapper
export interface ApiResponse<T> {
  data: T;
  message?: string;
  status: 'success' | 'error';
}

// Pagination response (Laravel JsonResource::collection)
export interface PaginatedResponse<T> {
  data: T[];
  links: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
  meta: {
    current_page: number;
    from: number | null;
    last_page: number;
    links: Array<{
      url: string | null;
      label: string;
      active: boolean;
    }>;
    path: string;
    per_page: number;
    to: number | null;
    total: number;
  };
}

// Error response
export interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
}

// Common API parameters
export interface PaginationParams {
  page?: number;
  per_page?: number;
}

export interface FilterParams {
  search?: string;
  sort_by?: string;
  sort_direction?: 'asc' | 'desc';
}

export interface QueryParams extends PaginationParams, FilterParams {
  [key: string]: any;
}

// Like action response
export interface LikeActionResponse {
  liked: boolean;
  likes_count: number;
  message: string;
}

// Restaurant statistics response
export interface RestaurantStatsResponse {
  total_dishes: number;
  total_events: number;
  total_likes: number;
  popular_dishes: Array<{
    id: string;
    name: string;
    likes_count: number;
  }>;
  upcoming_events: number;
}

// Collection filters for curated collections
export interface CollectionFilters {
  type?: 'seasonal' | 'trending' | 'thematic' | 'geographic';
  restaurant_id?: string;
  date_range?: {
    start: string;
    end: string;
  };
  categories?: string[];
  limit?: number;
}

// File upload response
export interface FileUploadResponse {
  path: string;
  url: string;
  filename: string;
  size: number;
  mime_type: string;
}

// Telegram user data from Mini App
export interface TelegramUserData {
  id: number;
  first_name: string;
  last_name?: string;
  username?: string;
  photo_url?: string;
  auth_date: number;
  hash: string;
}

// Form validation errors
export type ValidationErrors = Record<string, string[]>;

// API endpoints configuration
export interface ApiEndpoints {
  restaurants: string;
  menus: string;
  dishes: string;
  categories: string;
  events: string;
  likes: string;
  collections: string;
  news: string;
  upload: string;
} 