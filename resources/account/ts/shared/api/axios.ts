import { ApiError } from "@/shared";

import axios, { AxiosResponse, AxiosError } from "axios";
import router from "../../app/router";

export const client = axios.create({
    baseURL: import.meta.env.VITE_API_URL,
    headers: {
        Accept: "application/json",
    },
    withCredentials: true,
    withXSRFToken: true,
});

export const getCSRFCookie = async () => {
    try {
        await client.get("/sanctum/csrf-cookie");
    } catch (error) {
        throw error;
    }
};

export const refreshCSRFAndRetry = async (error: AxiosError<ApiError>) => {
    try {
        await getCSRFCookie();

        if (error.config) {
            return client.request(error.config);
        }
    } catch (csrfError) {
        return Promise.reject(csrfError);
    }

    return Promise.reject(error);
};

const handleAuthenticationError = () => {
    router.push({ name: "login" });
};

export const setupInterceptors = () => {
    client.interceptors.request.use(
        (config) => {
            if (!(config.data instanceof FormData)) {
                config.headers["Content-Type"] = "application/json";
            } else {
                config.headers["Content-Type"] = "multipart/form-data";
            }
            return config;
        },
        (error) => Promise.reject(error),
    );

    client.interceptors.response.use(
        (response: AxiosResponse) => response,
        async (error: AxiosError<ApiError>) => {
            if (error.response?.status === 419) {
                return refreshCSRFAndRetry(error);
            }

            if (error.response?.status === 401 || error.response?.status === 403) {
                handleAuthenticationError();
            }

            return Promise.reject(error);
        },
    );
};

export const initClient = async () => {
    setupInterceptors();
    await getCSRFCookie();
};
