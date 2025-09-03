/**
 * Форматирует цену для отображения:
 * - Принимает number или string
 * - Отбрасывает дробную часть (копейки)
 * - Удаляет посторонние символы
 * - Форматирует разделители тысяч по ru-RU
 * - Добавляет символ валюты ₽
 */
export function formatPrice(price: number | string): string {
  const raw = typeof price === 'string' ? price : String(price);
  const before_dot = raw.split('.')[0];
  const cleaned = before_dot.replace(/[^\d-]/g, '');
  const numeric = Number(cleaned);
  const base = Number.isFinite(numeric) ? numeric.toLocaleString('ru-RU') : before_dot;
  return `${base} ₽`;
}


