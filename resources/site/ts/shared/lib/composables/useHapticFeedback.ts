/**
 * useHapticFeedback
 * Возвращает функцию для мягкой вибрации на мобильных устройствах (если поддерживается).
 */
export const useHapticFeedback = () => {
  /**
   * Триггерит вибрацию с указанным паттерном, по умолчанию 10мс
   */
  const trigger = (pattern: number | number[] = 15): void => {
    if (typeof window === 'undefined') return;

    // Попытаться использовать Telegram WebApp Haptic, если доступно (работает на iOS/Android внутри Telegram)
    const tg = (
      window as unknown as {
        Telegram?: {
          WebApp?: {
            HapticFeedback?: { impactOccurred: (style: 'light' | 'medium' | 'heavy' | 'rigid' | 'soft') => void };
          };
        };
      }
    ).Telegram;
    if (tg?.WebApp?.HapticFeedback?.impactOccurred) {
      // light — мягкий отклик, наиболее близкий к короткой вибрации
      tg.WebApp.HapticFeedback.impactOccurred('light');
      return;
    }

    const hasCoarsePointer = typeof window.matchMedia === 'function' && window.matchMedia('(pointer: coarse)').matches;
    const hasTouchPoints =
      'maxTouchPoints' in navigator && (navigator as Navigator & { maxTouchPoints: number }).maxTouchPoints > 0;
    const nav = navigator as Navigator & { vibrate?: (p: number | number[]) => boolean };

    if ((hasCoarsePointer || hasTouchPoints) && typeof nav.vibrate === 'function') {
      // Некоторые браузеры игнорируют слишком короткие вибрации; 15-30 мс обычно надежнее
      nav.vibrate(pattern);
    }
  };

  return { trigger };
};
