import axios, { AxiosInstance, AxiosResponse, AxiosError } from "axios";
import type { ApiError } from "../types/api";

interface LoginCredentials {
    email: string;
    password: string;
    remember?: boolean;
}

class ApiClient {
    private instance: AxiosInstance;

    constructor() {
        this.instance = axios.create({
            baseURL: import.meta.env.VITE_API_URL,
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            withCredentials: true,
        });

        this.setupInterceptors();
    }

    async init() {
        try {
            await this.getCSRFCookie();
        } catch (error) {
            throw error;
        }
    }

    private setupInterceptors() {
        this.instance.interceptors.response.use(
            (response: AxiosResponse) => response,
            async (error: AxiosError<ApiError>) => {
                if (error.response?.status === 419) {
                    return this.refreshCSRFAndRetry(error);
                }

                if (error.response?.status === 401) {
                    this.handleAuthenticationError();
                }

                return Promise.reject(this.formatError(error));
            },
        );
    }

    private async refreshCSRFAndRetry(
        error: AxiosError<ApiError>,
    ): Promise<any> {
        try {
            // Получаем новый CSRF токен
            await this.getCSRFCookie();

            // Повторяем исходный запрос
            if (error.config) {
                return this.instance.request(error.config);
            }
        } catch (csrfError) {
            console.error("Failed to refresh CSRF token:", csrfError);
        }

        return Promise.reject(this.formatError(error));
    }

    private async handleAuthenticationError() {
        try {
            // Выполняем logout для очистки сессии на сервере
            await this.post<void>("/auth/logout");
        } catch (error) {
            // Игнорируем ошибки logout, так как пользователь уже не авторизован
            console.warn(
                "Logout failed during authentication error handling:",
                error,
            );
        }

        const currentPath = window.location.pathname;
        let loginUrl = "/";

        if (currentPath.startsWith("/account")) {
            loginUrl = "/account/login";
        } else if (currentPath.startsWith("/admin")) {
            loginUrl = "/admin/login";
        } else {
            loginUrl = "/";
        }

        window.location.href = loginUrl;
    }

    private formatError(error: AxiosError<ApiError>): ApiError {
        if (error.response?.data) {
            return error.response.data;
        }

        return {
            message: error.message || "Произошла неизвестная ошибка",
        };
    }

    async getCSRFCookie(): Promise<void> {
        try {
            await this.instance.get("/sanctum/csrf-cookie");
        } catch (error) {
            console.error("Failed to get CSRF cookie:", error);
            throw error;
        }
    }

    async get<T>(url: string, config = {}) {
        const response = await this.instance.get<T>(url, config);
        return response.data;
    }

    async post<T>(url: string, data = {}, config = {}) {
        const response = await this.instance.post<T>(url, data, config);
        return response.data;
    }

    async put<T>(url: string, data = {}, config = {}) {
        const response = await this.instance.put<T>(url, data, config);
        return response.data;
    }

    async patch<T>(url: string, data = {}, config = {}) {
        const response = await this.instance.patch<T>(url, data, config);
        return response.data;
    }

    async delete<T>(url: string, config = {}) {
        const response = await this.instance.delete<T>(url, config);
        return response.data;
    }

    async login(credentials: LoginCredentials) {
        await this.post<void>("/auth/login", credentials);
    }

    async logout() {
        await this.post<void>("/auth/logout");
    }
    async user<T = unknown>() {
        return this.get<T>("/api/user");
    }
}

export const apiClient = new ApiClient();
