// API Response Types для Restaurant Aggregator Platform
// Эти типы НЕ генерируются автоматически и создаются вручную

// Generic API Response wrapper
export interface ApiResponse<T> {
  data: T;
  message?: string;
  status: 'success' | 'error';
}

// Pagination response
export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  links: {
    first: string;
    last: string;
    prev?: string;
    next?: string;
  };
}

// Error response
export interface ErrorResponse {
  message: string;
  errors?: Record<string, string[]>;
  status: 'error';
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