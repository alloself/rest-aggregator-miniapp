// Minimal Telegram WebApp typings used in this project

export {};

declare global {
  interface TelegramWebApp {
    ready: () => void;
    initData: string;
  }

  interface TelegramGlobal {
    WebApp?: TelegramWebApp;
  }

  interface Window {
    Telegram?: TelegramGlobal;
  }
}


