// API Client
export { apiClient } from './client';

// API Services
export { authAPI, AuthAPI } from './auth';
export { restaurantAPI, RestaurantAPI } from './restaurant';
export { menuAPI, MenuAPI } from './menu';

// Re-export types (models and API types separately to avoid conflicts)
export type * from '../types/models';
export type {
  ApiResponse,
  PaginatedResponse,
  PaginationParams,
  FilterParams,
  QueryParams,
} from '../types/api';
export type {
  AuthUser,
  AuthToken,
  LoginRequest,
  LoginResponse,
  AuthState,
} from '../types/auth';

// Export common ApiError from api types
export type { ApiError } from '../types/api'; 