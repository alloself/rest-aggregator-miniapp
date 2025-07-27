import { Component } from "vue";
import type { ApiError, PaginatedResponse } from "./api";
import type { User } from "./models";
import { FormContext } from "vee-validate";
export type * from "./models";

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
    ApiEndpoints,
} from "./api";

export interface IBaseEntity {
    id: string | number;
    [key: string]: unknown;
}

export interface IBaseTreeEntity extends IBaseEntity {
    children?: IBaseTreeEntity[];
}

export interface IItems<T extends IBaseEntity> {
    itemTitle?: string | ((item: T) => string);
    itemValue?: string | ((item: T) => string | number);
}

export interface ISmartFormField {
    component: Component | string;
    key: string;
    props?: Record<string, unknown>;
    events?: Record<string, Function>;
    rule?: unknown;
    readonly?: boolean;
}

export interface ISmartFormProps {
    fields: ISmartFormField[];
    form: FormContext | undefined | null;
    initialValues?: Record<string, unknown>;
    loading?: boolean;
    readonly?: boolean;
    initialItems?: Record<string, unknown>;
}

export type { AuthUser, LoginRequest, LoginResponse, AuthState } from "./auth";

export function isApiError(obj: any): obj is ApiError {
    return obj && typeof obj === "object" && "message" in obj;
}

export function isPaginatedResponse<T>(obj: any): obj is PaginatedResponse<T> {
    return (
        obj &&
        typeof obj === "object" &&
        "data" in obj &&
        Array.isArray(obj.data) &&
        "current_page" in obj
    );
}

export function isUser(obj: any): obj is User {
    return (
        obj &&
        typeof obj === "object" &&
        "id" in obj &&
        "email" in obj &&
        "name" in obj
    );
}
