// Authentication types for Sanctum SPA (session-based auth)
import type { User, Restaurant } from './models';

// Authenticated user with computed roles
export interface AuthUser extends Omit<User, 'roles'> {
  // Override roles to be string array (computed from Role relations)
  roles: string[]; // role names array like ["admin", "restaurant_owner"]
  restaurant?: Restaurant; // Preloaded restaurant for restaurant owners
}

export interface LoginRequest {
  email: string;
  password: string;
  remember?: boolean;
  offer_accepted?: boolean;
}

export interface LoginResponse {
  user: AuthUser;
  message: string;
  // No token - Sanctum SPA uses httpOnly cookies
}

// Auth Store State for session-based auth
export interface AuthState {
  user: AuthUser | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  // No token state - sessions handle this
}

// CSRF token for forms
export interface CSRFToken {
  token: string;
}

// Profile update data
export interface ProfileUpdateRequest {
  name: string;
  email: string;
  current_password?: string;
  password?: string;
  password_confirmation?: string;
}

// Password reset
export interface PasswordResetRequest {
  email: string;
}

export interface PasswordResetResponse {
  message: string;
  status: string;
}

// Two-factor authentication
export interface TwoFactorChallengeRequest {
  code?: string;
  recovery_code?: string;
}

// Permissions and roles
export interface Permission {
  id: string;
  name: string;
  guard_name: string;
}

export interface Role {
  id: string;
  name: string;
  guard_name: string;
  permissions: Permission[];
}

// Re-export Restaurant from models for convenience
export type { Restaurant } from './models'; 