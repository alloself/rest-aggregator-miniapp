import { markRaw, ref, Component } from 'vue';
import type { BottomSheetOptions, BottomSheetItem } from '../../types/bottom-sheet';

const sheets = ref<BottomSheetItem[]>([]);

let idCounter = 0;

export const lockBodyScroll = (): void => {
  const body = document.body;
  const current_count_attr = body.getAttribute('data-scroll-lock-count') || '0';
  const current_count = Number(current_count_attr);
  body.setAttribute('data-scroll-lock-count', String(current_count + 1));
  if (current_count > 0) return;

  const scroll_y = window.scrollY || window.pageYOffset || 0;
  body.setAttribute('data-scroll-lock-y', String(scroll_y));
  body.style.position = 'fixed';
  body.style.top = `-${scroll_y}px`;
  body.style.left = '0';
  body.style.right = '0';
  body.style.width = '100%';
  body.style.overflow = 'hidden';
};

export const unlockBodyScroll = (): void => {
  const body = document.body;
  const current_count_attr = body.getAttribute('data-scroll-lock-count') || '0';
  const next = Math.max(0, Number(current_count_attr) - 1);
  body.setAttribute('data-scroll-lock-count', String(next));
  if (next > 0) return;

  const y_attr = body.getAttribute('data-scroll-lock-y') || '0';
  const y = Number(y_attr) || 0;

  body.style.position = '';
  body.style.top = '';
  body.style.left = '';
  body.style.right = '';
  body.style.width = '';
  body.style.overflow = '';

  body.removeAttribute('data-scroll-lock-y');
  window.scrollTo(0, y);
};

export const useBottomSheet = () => {
  const generateId = (): string => {
    return `sheet-${++idCounter}`;
  };

  const open = (
    component: Component,
    props: Record<string, unknown> = {},
    options: BottomSheetOptions = {},
  ): string => {
    const id = generateId();

    const defaultOptions: BottomSheetOptions = {
      showHandle: true,
      closableByBackdrop: true,
      closableBySwipe: true,
      height: 60,
      zIndex: 1000 + sheets.value.length,
    };

    sheets.value.push({
      id,
      component: markRaw(component),
      props,
      options: { ...defaultOptions, ...options },
    });
    lockBodyScroll();
    return id;
  };

  /**
   * Закрыть по ID
   */
  const close = (id: string): void => {
    const index = sheets.value.findIndex((sheet) => sheet.id === id);
    if (index > -1) {
      sheets.value.splice(index, 1);
      unlockBodyScroll();
    }
  };

  /**
   * Закрыть последний
   */
  const closeLast = (): void => {
    if (sheets.value.length > 0) {
      sheets.value.pop();
      unlockBodyScroll();
    }
  };

  /**
   * Закрыть все
   */
  const closeAll = (): void => {
    const count = sheets.value.length;
    sheets.value.splice(0);
    for (let i = 0; i < count; i += 1) {
      unlockBodyScroll();
    }
  };

  return {
    sheets,
    open,
    close,
    closeLast,
    closeAll,
  };
};
