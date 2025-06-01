// Central export for all types in Restaurant Aggregator Platform

// ✨ Auto-generated model types from Laravel (ModelTyper)
export type * from './models';

// 🔧 Hand-crafted API response types
export type {
  ApiResponse,
  PaginatedResponse,
  ApiError,
  PaginationParams,
  FilterParams,
  QueryParams,
  LikeActionResponse,
  RestaurantStatsResponse,
  CollectionFilters,
  FileUploadResponse,
  TelegramUserData,
  ValidationErrors,
  ApiEndpoints
} from './api';

// 🔐 Session-based authentication types (Sanctum SPA)
export type {
  AuthUser,
  LoginRequest,
  LoginResponse,
  AuthState
} from './auth';

// Import types for guards
import type { ApiError, PaginatedResponse } from './api';
import type { User } from './models';

// Type guards and utilities
export function isApiError(obj: any): obj is ApiError {
  return obj && typeof obj === 'object' && 'message' in obj;
}

export function isPaginatedResponse<T>(obj: any): obj is PaginatedResponse<T> {
  return obj && typeof obj === 'object' && 'data' in obj && Array.isArray(obj.data) && 'current_page' in obj;
}

export function isUser(obj: any): obj is User {
  return obj && typeof obj === 'object' && 'id' in obj && 'email' in obj && 'name' in obj;
} 