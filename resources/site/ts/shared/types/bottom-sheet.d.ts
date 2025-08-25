export interface BottomSheetOptions {
  /**
   * Показывать ли handle (полоску) для свайпа
   */
  showHandle?: boolean
  /**
   * Можно ли закрыть по клику на backdrop
   */
  closableByBackdrop?: boolean
  /**
   * Можно ли закрыть свайпом вниз
   */
  closableBySwipe?: boolean
  /**
   * Высота bottom sheet в процентах от экрана
   */
  height?: number
  /**
   * CSS класс для кастомизации
   */
  class?: string
  /**
   * Z-index для overlay
   */
  zIndex?: number
}
export interface BottomSheetItem {
  id: string
  component: any
  props?: Record<string, any>
  options?: BottomSheetOptions
}

export interface BottomSheetState {
  sheets: BottomSheetItem[]
  isAnimating: boolean
}

export interface BottomSheetMethods {
  /**
   * Открыть новый bottom sheet
   */
  open: (component: any, props?: Record<string, any>, options?: BottomSheetOptions) => string
  /**
   * Закрыть конкретный bottom sheet по ID
   */
  close: (id: string) => void
  /**
   * Закрыть последний открытый bottom sheet
   */
  closeLast: () => void
  /**
   * Закрыть все bottom sheet'ы
   */
  closeAll: () => void
}

