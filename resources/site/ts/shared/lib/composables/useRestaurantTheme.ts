import { watch } from 'vue';
import type { Ref } from 'vue';
import type { RestaurantResourceData } from '@/shared/types/resources';

const DEFAULT_PRIMARY = '#FFFBF7';
const DEFAULT_SECONDARY = '#FAF6F0';
const DEFAULT_TERTIARY = '#EBE4DE';
const DEFAULT_BACKGROUND = '#FFFCF6';

function hexToRgb(hex: string): { r: number; g: number; b: number } | null {
  const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  if (!result) return null;
  return {
    r: parseInt(result[1], 16),
    g: parseInt(result[2], 16),
    b: parseInt(result[3], 16),
  };
}

function darkenHex(hex: string, amount: number): string {
  const rgb = hexToRgb(hex);
  if (!rgb) return hex;
  const r = Math.max(0, rgb.r - amount);
  const g = Math.max(0, rgb.g - amount);
  const b = Math.max(0, rgb.b - amount);
  return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
}

function applyTheme(restaurant: RestaurantResourceData | null): void {
  const root = document.documentElement;

  if (!restaurant) {
    root.style.removeProperty('--color-bg-primary');
    root.style.removeProperty('--color-bg-secondary');
    root.style.removeProperty('--color-secondary');
    root.style.removeProperty('--color-bg-secondary-hover');
    root.style.removeProperty('--color-bg-secondary-active');
    root.style.removeProperty('--color-bg-card');
    root.style.removeProperty('--color-surface-tertiary');
    root.style.removeProperty('--color-surface-cool');
    root.style.removeProperty('--color-accent-secondary');
    return;
  }

  const primary = restaurant.primary_color ?? DEFAULT_PRIMARY;
  const secondary = restaurant.secondary_color ?? DEFAULT_SECONDARY;
  const tertiary = restaurant.tertiary_color ?? DEFAULT_TERTIARY;
  const background = restaurant.background_color ?? DEFAULT_BACKGROUND;

  root.style.setProperty('--color-bg-primary', background);
  root.style.setProperty('--color-bg-secondary', secondary);
  root.style.setProperty('--color-secondary', secondary);
  root.style.setProperty('--color-bg-secondary-hover', darkenHex(secondary, 15));
  root.style.setProperty('--color-bg-secondary-active', darkenHex(secondary, 25));
  root.style.setProperty('--color-bg-card', primary);
  root.style.setProperty('--color-surface-tertiary', tertiary);
  root.style.setProperty('--color-surface-cool', tertiary);
  root.style.setProperty('--color-accent-secondary', primary);
}

export function useRestaurantTheme(restaurant: Ref<RestaurantResourceData | null>): void {
  watch(
    restaurant,
    (value) => {
      applyTheme(value ?? null);
    },
    { immediate: true },
  );
}
