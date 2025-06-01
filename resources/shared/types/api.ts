// Import all model types
export * from './models';

// Generic API Response Types
export interface ApiResponse<T> {
  data: T;
  message?: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
  links: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
}

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