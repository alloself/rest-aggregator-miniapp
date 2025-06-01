import _ from 'lodash';

// Date formatting utilities
export function formatDate(date: string | Date | null): string {
  if (!date) return '';
  
  const d = typeof date === 'string' ? new Date(date) : date;
  return d.toLocaleDateString('ru-RU', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}

export function formatDateTime(date: string | Date | null): string {
  if (!date) return '';
  
  const d = typeof date === 'string' ? new Date(date) : date;
  return d.toLocaleDateString('ru-RU', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}

export function formatTime(date: string | Date | null): string {
  if (!date) return '';
  
  const d = typeof date === 'string' ? new Date(date) : date;
  return d.toLocaleTimeString('ru-RU', {
    hour: '2-digit',
    minute: '2-digit',
  });
}

// Price formatting
export function formatPrice(price: number | null): string {
  if (price === null || price === undefined) return '';
  
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(price);
}

// Phone number formatting
export function formatPhoneNumber(phone: string | null): string {
  if (!phone) return '';
  
  // Remove all non-digits
  const digits = phone.replace(/\D/g, '');
  
  // Format Russian phone number
  if (digits.length === 11 && digits.startsWith('7')) {
    return `+7 (${digits.slice(1, 4)}) ${digits.slice(4, 7)}-${digits.slice(7, 9)}-${digits.slice(9)}`;
  }
  
  // Return original if can't format
  return phone;
}

// Text truncation
export function truncateText(text: string | null, maxLength: number = 100): string {
  if (!text) return '';
  
  if (text.length <= maxLength) return text;
  
  return text.slice(0, maxLength).trim() + '...';
}

// Slug generation
export function generateSlug(text: string): string {
  return text
    .toLowerCase()
    .replace(/[а-я]/g, (char) => {
      const map: Record<string, string> = {
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo',
        'ж': 'zh', 'з': 'z', 'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm',
        'н': 'n', 'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
        'ф': 'f', 'х': 'h', 'ц': 'ts', 'ч': 'ch', 'ш': 'sh', 'щ': 'sch',
        'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya'
      };
      return map[char] || char;
    })
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');
}

// Lodash-powered utilities
export function debounceSearch<T extends (...args: any[]) => any>(
  func: T,
  wait: number = 300
): _.DebouncedFunc<T> {
  return _.debounce(func, wait);
}

export function throttleScroll<T extends (...args: any[]) => any>(
  func: T,
  wait: number = 100
): _.DebouncedFunc<T> {
  return _.throttle(func, wait);
}

// Deep clone for complex objects
export function deepClone<T>(obj: T): T {
  return _.cloneDeep(obj);
}

// Group items by key
export function groupBy<T>(
  collection: T[],
  iteratee: keyof T | ((item: T) => any)
): Record<string, T[]> {
  return _.groupBy(collection, iteratee);
}

// Chunk array into smaller arrays
export function chunk<T>(array: T[], size: number): T[][] {
  return _.chunk(array, size);
}

// Remove duplicates from array
export function uniq<T>(array: T[]): T[] {
  return _.uniq(array);
}

// Remove duplicates by key
export function uniqBy<T>(
  array: T[],
  iteratee: keyof T | ((item: T) => any)
): T[] {
  return _.uniqBy(array, iteratee);
}

// Safely get nested property
export function get<TResult = any>(
  object: any,
  path: string,
  defaultValue?: TResult
): TResult | undefined {
  return _.get(object, path, defaultValue);
}

// Check if value is empty
export function isEmpty(value: any): boolean {
  return _.isEmpty(value);
}

// Capitalize first letter
export function capitalize(str: string): string {
  return _.capitalize(str);
}

// Start case (Title Case)
export function startCase(str: string): string {
  return _.startCase(str);
}

// Kebab case
export function kebabCase(str: string): string {
  return _.kebabCase(str);
}

// Camel case
export function camelCase(str: string): string {
  return _.camelCase(str);
}

// Snake case
export function snakeCase(str: string): string {
  return _.snakeCase(str);
}

// Pick specific properties from object
export function pick<T extends Record<string, any>, K extends keyof T>(
  object: T,
  keys: K[]
): Pick<T, K> {
  return _.pick(object, keys) as Pick<T, K>;
}

// Omit specific properties from object
export function omit<T extends Record<string, any>, K extends keyof T>(
  object: T,
  keys: K[]
): Omit<T, K> {
  return _.omit(object, keys) as Omit<T, K>;
}

// Merge objects deeply
export function merge<T>(...objects: Partial<T>[]): T {
  return _.merge({}, ...objects) as T;
} 