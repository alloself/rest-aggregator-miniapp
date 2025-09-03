// Minimal Telegram WebApp typings used in this project

export {};

declare global {
  type TelegramInsets = { top: number; bottom: number; left: number; right: number };

  interface TelegramWebApp {
    ready: () => void;
    initData: string;
    // Viewport & safe areas
    viewportHeight?: number;
    safeAreaInset?: TelegramInsets;
    contentSafeAreaInset?: TelegramInsets;
    // Feature checks & fullscreen
    isVersionAtLeast?: (version: string) => boolean;
    requestFullscreen?: () => void;
    // Expanding and gestures
    expand?: () => void;
    setupSwipeBehavior?: (opts: { allow_vertical_swipe: boolean }) => void;
    // Events
    onEvent?: (
      event: 'viewportChanged' | 'safeAreaChanged' | 'contentSafeAreaChanged' | 'fullscreenChanged',
      handler: () => void,
    ) => void;
    offEvent?: (
      event: 'viewportChanged' | 'safeAreaChanged' | 'contentSafeAreaChanged' | 'fullscreenChanged',
      handler: () => void,
    ) => void;
  }

  interface TelegramGlobal {
    WebApp?: TelegramWebApp;
  }

  interface Window {
    Telegram?: TelegramGlobal;
  }
}


