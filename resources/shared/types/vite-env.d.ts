/// <reference types="vite/client" />

// Расширяем стандартный Vite интерфейс нашими переменными
interface ImportMetaEnv {
  readonly VITE_API_URL: string;
  readonly VITE_APP_NAME: string;
  readonly VITE_APP_ENV: string;
  // добавьте другие переменные по мере необходимости
} 