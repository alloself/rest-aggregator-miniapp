import type {
  Restaurant,News,File,Category,User,Dish,Event
} from './models'

// Автоматически сгенерированные типы для Laravel API Resources
// Не редактируйте вручную - файл перезаписывается командой generate:resource-types

// Вспомогательные типы для pivot полей
export interface CategoryPivot {
  id: string | null
  key: string | null
  order: number | null
}

// Рабочие часы ресторана
export interface WorkingHours {
  [day: string]: {
    open: string
    close: string
    is_closed: boolean
  } | null
}
export interface RestaurantResourceData {
  id: string
  name: string
  subtitle?: string | null
  slug: string
  description?: string | null
  working_hours?: WorkingHours | null
  address?: string | null
  average_receipt?: number | null
  phone?: string | null
  bot_username: string
  yandex_metrica_code?: string | null
  // seo
  seo_title?: string | null
  seo_description?: string | null
  seo_keywords?: string | null
  seo_robots?: string | null
  seo_canonical_url?: string | null
  // og
  og_title?: string | null
  og_description?: string | null
  og_image_url?: string | null
  // theme colors
  primary_color?: string | null
  secondary_color?: string | null
  tertiary_color?: string | null
  background_color?: string | null
  files?: FileResourceData[]
  images?: FileResourceData[]
  categories?: CategoryResourceData[]
}

export interface NewsResourceData {
  id: string
  slug: string
  title: string
  description?: string | null
  order?: number | null
  created_at?: Date | null
  updated_at?: Date | null
  restaurant?: RestaurantResourceData
  files?: FileResourceData[]
  images?: FileResourceData[]
}

export interface FileResourceData {
  id: string
  name: string
  url: string
  extension: string
  created_at?: Date | null
  updated_at?: Date | null
  pivot?: CategoryPivot
}

export interface CategoryResourceData {
  id: string
  name: string
  slug: string
  order?: number | null
  dishes?: DishResourceData[]
  pivot?: CategoryPivot
  children?: CategoryResourceData[]
}

export interface UserResourceData {
  id: string
  first_name: string
  last_name?: string | null
  middle_name?: string | null
  email?: string | null
}

export interface DishResourceData {
  id: string
  slug: string
  name: string
  description?: string | null
  price?: number | null
  order?: number | null
  category_id: string
  created_at?: Date | null
  updated_at?: Date | null
  images?: FileResourceData[]
  files?: FileResourceData[]
}

export interface EventResourceData {
  id: string
  slug: string
  title: string
  subtitle?: string | null
  start_at?: Date | null
  price?: number | null
  description?: string | null
  order?: number | null
  created_at?: Date | null
  updated_at?: Date | null
  restaurant?: RestaurantResourceData
  is_sold_out: boolean
  files?: FileResourceData[]
  images?: FileResourceData[]
}