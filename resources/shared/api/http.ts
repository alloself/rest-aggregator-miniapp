import axios, { type AxiosError, type AxiosInstance, type AxiosResponse } from 'axios';
import type { ApiError } from '@/shared/types';

export interface HttpClientOptions {
  baseURL?: string;
  withCSRF?: boolean;
  csrfEndpoint?: string;
  handle419Refresh?: boolean;
  onAuthError?: () => void;
}

export interface HttpClientApi {
  client: AxiosInstance;
  getCSRFCookie: () => Promise<void>;
  refreshCSRFAndRetry: (error: AxiosError<ApiError>) => Promise<any>;
  setupInterceptors: () => void;
  initClient: () => Promise<void>;
}

/**
 * Создает настроенный Axios инстанс с типовыми перехватчиками (CSRF, JSON, Auth).
 */
export function createHttpClient(options: HttpClientOptions = {}): HttpClientApi {
  const {
    baseURL,
    withCSRF = true,
    csrfEndpoint = '/sanctum/csrf-cookie',
    handle419Refresh = true,
    onAuthError,
  } = options;

  const client = axios.create({
    baseURL,
    headers: { Accept: 'application/json' },
    withCredentials: true,
    withXSRFToken: true,
  });

  const getCSRFCookie = async (): Promise<void> => {
    if (!withCSRF) return;
    await client.get(csrfEndpoint);
  };

  const refreshCSRFAndRetry = async (error: AxiosError<ApiError>) => {
    if (!handle419Refresh) {
      return Promise.reject(error);
    }

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

  const setupInterceptors = (): void => {
    client.interceptors.request.use(
      (config) => {
        if (!(config.data instanceof FormData)) {
          config.headers['Content-Type'] = 'application/json';
        } else {
          config.headers['Content-Type'] = 'multipart/form-data';
        }
        return config;
      },
      (error) => Promise.reject(error),
    );

    client.interceptors.response.use(
      (response: AxiosResponse) => response,
      async (error: AxiosError<ApiError>) => {
        const status = error.response?.status;
        if (status === 419) {
          return refreshCSRFAndRetry(error);
        }

        if ((status === 401 || status === 403) && typeof onAuthError === 'function') {
          onAuthError();
        }

        return Promise.reject(error);
      },
    );
  };

  const initClient = async (): Promise<void> => {
    setupInterceptors();
    await getCSRFCookie();
  };

  return { client, getCSRFCookie, refreshCSRFAndRetry, setupInterceptors, initClient };
}


