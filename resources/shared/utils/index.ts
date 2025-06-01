// Formatters
export * from './formatters';

// Validators
export * from './validators';

// Lodash utilities
import _ from 'lodash'

// Export lodash for use across the application
export { _ }

// Common utility functions using lodash
export const arrayUtils = {
  /**
   * Group array of objects by a key
   */
  groupBy: <T>(array: T[], key: keyof T) => _.groupBy(array, key),
  
  /**
   * Remove duplicates from array
   */
  uniq: <T>(array: T[]) => _.uniq(array),
  
  /**
   * Sort array by multiple keys
   */
  orderBy: <T>(array: T[], keys: (keyof T)[], orders?: ('asc' | 'desc')[]) => 
    _.orderBy(array, keys as string[], orders),
  
  /**
   * Chunk array into smaller arrays
   */
  chunk: <T>(array: T[], size: number) => _.chunk(array, size),
  
  /**
   * Find item in array by property
   */
  findBy: <T>(array: T[], property: keyof T, value: any) => 
    _.find(array, { [property]: value }),
}

export const objectUtils = {
  /**
   * Deep clone object
   */
  cloneDeep: <T>(obj: T): T => _.cloneDeep(obj),
  
  /**
   * Pick specific properties from object
   */
  pick: <T extends Record<string, any>, K extends keyof T>(obj: T, keys: K[]) => 
    _.pick(obj, keys) as Pick<T, K>,
  
  /**
   * Omit specific properties from object
   */
  omit: <T extends Record<string, any>, K extends keyof T>(obj: T, keys: K[]) => 
    _.omit(obj, keys) as Omit<T, K>,
  
  /**
   * Check if object is empty
   */
  isEmpty: (obj: any) => _.isEmpty(obj),
  
  /**
   * Merge objects deeply
   */
  merge: <T extends Record<string, any>>(target: T, ...sources: Partial<T>[]): T => 
    _.merge({}, target, ...sources) as T,
}

export const stringUtils = {
  /**
   * Convert string to kebab-case
   */
  kebabCase: (str: string) => _.kebabCase(str),
  
  /**
   * Convert string to camelCase
   */
  camelCase: (str: string) => _.camelCase(str),
  
  /**
   * Convert string to PascalCase
   */
  pascalCase: (str: string) => _.upperFirst(_.camelCase(str)),
  
  /**
   * Capitalize first letter
   */
  capitalize: (str: string) => _.capitalize(str),
  
  /**
   * Truncate string with ellipsis
   */
  truncate: (str: string, length: number = 30) => 
    _.truncate(str, { length }),
}

export const numberUtils = {
  /**
   * Generate random number between min and max
   */
  random: (min: number, max: number) => _.random(min, max),
  
  /**
   * Round number to specified precision
   */
  round: (num: number, precision: number = 2) => _.round(num, precision),
  
  /**
   * Clamp number between min and max
   */
  clamp: (num: number, min: number, max: number) => _.clamp(num, min, max),
}

// Restaurant-specific utilities
export const restaurantUtils = {
  /**
   * Group dishes by category
   */
  groupDishesByCategory: (dishes: any[]) => 
    _.groupBy(dishes, 'category.name'),
  
  /**
   * Sort events by date
   */
  sortEventsByDate: (events: any[]) => 
    _.orderBy(events, ['start_date'], ['asc']),
  
  /**
   * Filter available dishes
   */
  getAvailableDishes: (dishes: any[]) => 
    _.filter(dishes, { is_available: true }),
  
  /**
   * Calculate average price
   */
  calculateAveragePrice: (dishes: any[]) => 
    _.meanBy(dishes, 'price'),
} 