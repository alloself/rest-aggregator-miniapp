import { ref, reactive, markRaw, onBeforeUnmount } from 'vue'
import type { BottomSheetOptions, BottomSheetItem, BottomSheetState } from '../../types/bottom-sheet'

// Глобальное состояние
const state = reactive<BottomSheetState>({
  sheets: [],
  isAnimating: false
})

let idCounter = 0

function generateId(): string {
  return `sheet-${++idCounter}`
}

/**
 * Открыть bottom sheet
 */
function open(component: any, props: Record<string, any> = {}, options: BottomSheetOptions = {}): string {
  const id = generateId()
  
  const defaultOptions: BottomSheetOptions = {
    showHandle: true,
    closableByBackdrop: true,
    closableBySwipe: true,
    height: 60,
    zIndex: 1000 + state.sheets.length
  }
  
  const item: BottomSheetItem = {
    id,
    component: markRaw(component),
    props,
    options: { ...defaultOptions, ...options }
  }
  
  state.sheets.push(item)
  return id
}

/**
 * Закрыть по ID
 */
function close(id: string): void {
  const index = state.sheets.findIndex(sheet => sheet.id === id)
  if (index > -1) {
    state.sheets.splice(index, 1)
  }
}

/**
 * Закрыть последний
 */
function closeLast(): void {
  if (state.sheets.length > 0) {
    state.sheets.pop()
  }
}

/**
 * Закрыть все
 */
function closeAll(): void {
  state.sheets.splice(0)
}

export function useBottomSheet() {
  return {
    state,
    open,
    close,
    closeLast,
    closeAll
  }
}

export const bottomSheet = {
  open,
  close,
  closeLast,
  closeAll
}
