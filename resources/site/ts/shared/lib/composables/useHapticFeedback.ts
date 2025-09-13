/**
 * useHapticFeedback
 * Возвращает функцию для мягкой вибрации на мобильных устройствах (если поддерживается).
 */
export const useHapticFeedback = () => {
  /**
   * Триггерит вибрацию с указанным паттерном, по умолчанию 10мс
   */
  const trigger = (pattern: number | number[] = 10): void => {
    if (typeof window === 'undefined') return;

    const hasCoarsePointer = typeof window.matchMedia === 'function' && window.matchMedia('(pointer: coarse)').matches;
    const hasTouchPoints =
      'maxTouchPoints' in navigator && (navigator as Navigator & { maxTouchPoints: number }).maxTouchPoints > 0;
    const nav = navigator as Navigator & { vibrate?: (p: number | number[]) => boolean };

    if ((hasCoarsePointer || hasTouchPoints) && typeof nav.vibrate === 'function') {
      nav.vibrate(pattern);
    }
  };

  return { trigger };
};
