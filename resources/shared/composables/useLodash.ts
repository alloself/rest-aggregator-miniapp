import { computed, ref, type Ref } from 'vue';
import _ from 'lodash';

/**
 * Композабл для работы с lodash утилитами
 */
export function useLodash() {
  /**
   * Создать debounced версию реактивного значения
   */
  function useDebounced<T>(value: Ref<T>, delay: number = 300) {
    const debouncedValue = ref<T>(value.value) as Ref<T>;
    
    const updateValue = _.debounce((newValue: T) => {
      debouncedValue.value = newValue;
    }, delay);
    
    // Наблюдаем за изменениями исходного значения
    const stopWatcher = computed(() => value.value);
    stopWatcher.effect = () => {
      updateValue(value.value);
    };
    
    return {
      debouncedValue,
      stopWatcher
    };
  }
  
  /**
   * Создать throttled версию функции
   */
  function useThrottled<T extends (...args: any[]) => any>(
    func: T,
    wait: number = 100
  ) {
    return _.throttle(func, wait);
  }
  
  /**
   * Фильтрация коллекции с поддержкой реактивности
   */
  function useFilteredCollection<T>(
    collection: Ref<T[]>,
    predicate: (item: T) => boolean
  ) {
    return computed(() => collection.value.filter(predicate));
  }
  
  /**
   * Группировка коллекции с поддержкой реактивности
   */
  function useGroupedCollection<T>(
    collection: Ref<T[]>,
    iteratee: keyof T | ((item: T) => any)
  ) {
    return computed(() => _.groupBy(collection.value, iteratee));
  }
  
  /**
   * Сортировка коллекции с поддержкой реактивности
   */
  function useSortedCollection<T>(
    collection: Ref<T[]>,
    iteratees: Array<keyof T | ((item: T) => any)> = [],
    orders: Array<'asc' | 'desc'> = ['asc']
  ) {
    return computed(() => _.orderBy(collection.value, iteratees, orders));
  }
  
  /**
   * Поиск в коллекции с поддержкой реактивности
   */
  function useSearchCollection<T>(
    collection: Ref<T[]>,
    searchTerm: Ref<string>,
    searchFields: Array<keyof T>
  ) {
    const debouncedSearch = ref('');
    
    const updateSearch = _.debounce((term: string) => {
      debouncedSearch.value = term;
    }, 300);
    
    computed(() => searchTerm.value).effect = () => {
      updateSearch(searchTerm.value);
    };
    
    const filteredCollection = computed(() => {
      if (!debouncedSearch.value) return collection.value;
      
      const term = debouncedSearch.value.toLowerCase();
      
      return collection.value.filter(item => 
        searchFields.some(field => {
          const value = item[field];
          return String(value).toLowerCase().includes(term);
        })
      );
    });
    
    return {
      filteredCollection,
      searchTerm: debouncedSearch
    };
  }
  
  /**
   * Утилиты для работы с объектами
   */
  const objectUtils = {
    pick: <T extends Record<string, any>, K extends keyof T>(
      object: T,
      keys: K[]
    ): Pick<T, K> => _.pick(object, keys) as Pick<T, K>,
    
    omit: <T extends Record<string, any>, K extends keyof T>(
      object: T,
      keys: K[]
    ): Omit<T, K> => _.omit(object, keys) as Omit<T, K>,
    
    get: <TResult = any>(
      object: any,
      path: string,
      defaultValue?: TResult
    ): TResult | undefined => _.get(object, path, defaultValue),
    
    set: (object: any, path: string, value: any) => _.set(object, path, value),
    
    has: (object: any, path: string): boolean => _.has(object, path),
    
    merge: <T>(...objects: Partial<T>[]): T => _.merge({}, ...objects) as T,
    
    cloneDeep: <T>(obj: T): T => _.cloneDeep(obj),
    
    isEmpty: (value: any): boolean => _.isEmpty(value),
    
    isEqual: (value: any, other: any): boolean => _.isEqual(value, other)
  };
  
  /**
   * Утилиты для работы с массивами
   */
  const arrayUtils = {
    chunk: <T>(array: T[], size: number): T[][] => _.chunk(array, size),
    
    compact: <T>(array: (T | null | undefined)[]): T[] => _.compact(array),
    
    difference: <T>(array: T[], ...values: T[][]): T[] => _.difference(array, ...values),
    
    intersection: <T>(...arrays: T[][]): T[] => _.intersection(...arrays),
    
    union: <T>(...arrays: T[][]): T[] => _.union(...arrays),
    
    uniq: <T>(array: T[]): T[] => _.uniq(array),
    
    uniqBy: <T>(
      array: T[],
      iteratee: keyof T | ((item: T) => any)
    ): T[] => _.uniqBy(array, iteratee),
    
    flatten: <T>(array: Array<T | T[]>): T[] => _.flatten(array),
    
    flattenDeep: <T>(array: any[]): T[] => _.flattenDeep(array),
    
    shuffle: <T>(array: T[]): T[] => _.shuffle(array),
    
    sample: <T>(array: T[]): T | undefined => _.sample(array),
    
    sampleSize: <T>(array: T[], n: number): T[] => _.sampleSize(array, n)
  };
  
  /**
   * Утилиты для работы со строками
   */
  const stringUtils = {
    camelCase: (str: string): string => _.camelCase(str),
    
    kebabCase: (str: string): string => _.kebabCase(str),
    
    snakeCase: (str: string): string => _.snakeCase(str),
    
    startCase: (str: string): string => _.startCase(str),
    
    capitalize: (str: string): string => _.capitalize(str),
    
    lowerCase: (str: string): string => _.lowerCase(str),
    
    upperCase: (str: string): string => _.upperCase(str),
    
    trim: (str: string, chars?: string): string => _.trim(str, chars),
    
    truncate: (str: string, options?: _.TruncateOptions): string => 
      _.truncate(str, options),
    
    deburr: (str: string): string => _.deburr(str),
    
    escape: (str: string): string => _.escape(str),
    
    unescape: (str: string): string => _.unescape(str)
  };
  
  /**
   * Утилиты для работы с числами
   */
  const numberUtils = {
    clamp: (number: number, lower: number, upper: number): number => 
      _.clamp(number, lower, upper),
    
    inRange: (number: number, start: number, end?: number): boolean => 
      _.inRange(number, start, end),
    
    random: (lower?: number, upper?: number, floating?: boolean): number => 
      _.random(lower, upper, floating),
    
    round: (number: number, precision?: number): number => 
      _.round(number, precision),
    
    ceil: (number: number, precision?: number): number => 
      _.ceil(number, precision),
    
    floor: (number: number, precision?: number): number => 
      _.floor(number, precision)
  };
  
  return {
    // Реактивные утилиты
    useDebounced,
    useThrottled,
    useFilteredCollection,
    useGroupedCollection,
    useSortedCollection,
    useSearchCollection,
    
    // Утилиты по категориям
    objectUtils,
    arrayUtils,
    stringUtils,
    numberUtils,
    
    // Прямой доступ к lodash
    _
  };
} 