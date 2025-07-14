// AUTO-GENERATED FILE. DO NOT EDIT.
// Generated from: resources/shared/svg

export const customIconMap = {
  "star": () => import('~icons/custom/star')
} as const

export type CustomIconName = keyof typeof customIconMap

// Helper function to check if icon exists
export const hasCustomIcon = (name: string): name is CustomIconName => {
  return name in customIconMap
}
