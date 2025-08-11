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
    id: string;
    [key: string]: unknown;
}

export interface IBaseColumn {
    field: string;
    header: string;
}

export interface IBaseTreeEntity<T extends IBaseEntity> {
    children?: T[];
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

export function isPaginatedResponse<T>(obj: unknown): obj is PaginatedResponse<T> {
    if (!obj || typeof obj !== "object") return false;
    const anyObj = obj as Record<string, unknown>;
    const dataOk = Array.isArray(anyObj.data);
    const links = anyObj.links as Record<string, unknown> | undefined;
    const meta = anyObj.meta as Record<string, unknown> | undefined;
    const linksOk = !!links && "first" in links && "last" in links && "prev" in links && "next" in links;
    const metaOk = !!meta && "current_page" in meta && "last_page" in meta && "per_page" in meta && "total" in meta;
    return dataOk && linksOk && metaOk;
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
