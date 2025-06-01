// Import generated model types
import type { User, Restaurant } from './models';

// Use the generated User interface but extend for auth context
export interface AuthUser extends Omit<User, 'roles'> {
  // Override roles to be string array (computed from Role relations)
  roles: string[]; // role names array like ["admin", "restaurant_owner"]
}

export interface AuthToken {
  token: string;
  type: 'Bearer';
  expires_at: string | null;
}

export interface LoginRequest {
  email: string;
  password: string;
  device_name?: string;
}

export interface LoginResponse {
  user: AuthUser;
  token: string;
  token_type: 'Bearer';
  expires_at: string | null;
}

// Re-export Restaurant from models for convenience
export type { Restaurant } from './models';

// Auth Store State
export interface AuthState {
  user: AuthUser | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
}

// API Error Response
export interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
} 