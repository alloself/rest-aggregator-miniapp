export interface Category {
  // columns
  id: string
  slug: string
  name: string
  order: number
  _lft: number
  _rgt: number
  parent_id?: string | null
  created_at?: Date | null
  updated_at?: Date | null
  // relations
  restaurants?: Restaurant[]
  parent?: Category
  children?: Category[]
  // counts
  restaurants_count: number
  children_count: number
  // exists
  restaurants_exists: boolean
  parent_exists: boolean
  children_exists: boolean
}

export interface Dish {
  // columns
  id: number
  slug: string
  name: string
  category_id: string
  order: number
  created_at?: Date | null
  updated_at?: Date | null
}

export interface Event {
  // columns
  id: string
  slug: string
  title: string
  subtitle: string
  start_at: Date
  price?: number | null
  description: string
  restaurant_id: string
  order: number
  deleted_at?: Date | null
  created_at?: Date | null
  updated_at?: Date | null
  // relations
  restaurant?: Restaurant
  images?: File[]
  files?: File[]
  // counts
  images_count: number
  files_count: number
  // exists
  restaurant_exists: boolean
  images_exists: boolean
  files_exists: boolean
}

export interface File {
  // columns
  id: string
  url: string
  name: string
  extension: string
  created_at?: Date | null
  updated_at?: Date | null
  // relations
  fileables?: Fileable[]
  // counts
  fileables_count: number
  // exists
  fileables_exists: boolean
}

export interface Message {
  // columns
  id: number
  description?: string | null
  deleted_at?: Date | null
  created_at?: Date | null
  updated_at?: Date | null
}

export interface News {
  // columns
  id: string
  slug: string
  title: string
  description: string
  is_published: boolean
  restaurant_id: string
  order: number
  deleted_at?: Date | null
  created_at?: Date | null
  updated_at?: Date | null
  // relations
  restaurant?: Restaurant
  images?: File[]
  files?: File[]
  // counts
  images_count: number
  files_count: number
  // exists
  restaurant_exists: boolean
  images_exists: boolean
  files_exists: boolean
}

export interface Offer {
  // columns
  id: number
  slug: string
  name: string
  restaurant_id: string
  order: number
  created_at?: Date | null
  updated_at?: Date | null
}

export interface Permission {
  // columns
  id: string
  name: string
  guard_name: string
  created_at?: Date | null
  updated_at?: Date | null
  // relations
  roles?: Role[]
  users?: User[]
  permissions?: Permission[]
  // counts
  roles_count: number
  users_count: number
  permissions_count: number
  // exists
  roles_exists: boolean
  users_exists: boolean
  permissions_exists: boolean
}

export interface Categorizable {
  // columns
  id: string
  category_id: string
  categorizable_type: string
  categorizable_id: string
  key?: string | null
  order: number
  created_at?: Date | null
  updated_at?: Date | null
}

export interface Fileable {
  // columns
  id: string
  fileable_type: string
  fileable_id: string
  type: string
  key?: string | null
  order: number
  file_id: string
  // relations
  fileable?: Fileable
  file?: File
  // counts
  // exists
  file_exists: boolean
}

export interface Restaurant {
  // columns
  id: string
  slug: string
  name: string
  subtitle?: string | null
  average_receipt?: string | null
  phone?: string | null
  telegram_bot_token?: string | null
  address?: string | null
  description?: string | null
  welcome_message?: string | null
  working_hours?: Record<string, unknown> | null
  yandex_metrica_code?: string | null
  deleted_at?: Date | null
  created_at?: Date | null
  updated_at?: Date | null
  // relations
  news?: News[]
  events?: Event[]
  categories?: Category[]
  users?: User[]
  images?: File[]
  files?: File[]
  // counts
  news_count: number
  events_count: number
  categories_count: number
  users_count: number
  images_count: number
  files_count: number
  // exists
  news_exists: boolean
  events_exists: boolean
  categories_exists: boolean
  users_exists: boolean
  images_exists: boolean
  files_exists: boolean
}

export interface Role {
  // columns
  id: string
  team_id?: string | null
  name: string
  guard_name: string
  created_at?: Date | null
  updated_at?: Date | null
  // relations
  permissions?: Permission[]
  users?: User[]
  // counts
  permissions_count: number
  users_count: number
  // exists
  permissions_exists: boolean
  users_exists: boolean
}

export interface User {
  // columns
  id: string
  first_name: string
  last_name?: string | null
  middle_name?: string | null
  email?: string | null
  phone?: string | null
  username?: string | null
  avatar_url?: string | null
  email_verified_at?: Date | null
  created_at?: Date | null
  updated_at?: Date | null
  deleted_at?: Date | null
  // mutators
  full_avatar_url: string
  // overrides
  tokens: never
  notifications: never
  // relations
  restaurants?: Restaurant[]
  friends?: User[]
  friend_of?: User[]
  roles?: Role[]
  permissions?: Permission[]
  images?: File[]
  files?: File[]
  // counts
  restaurants_count: number
  friends_count: number
  friend_of_count: number
  roles_count: number
  permissions_count: number
  images_count: number
  files_count: number
  // exists
  restaurants_exists: boolean
  friends_exists: boolean
  friend_of_exists: boolean
  roles_exists: boolean
  permissions_exists: boolean
  images_exists: boolean
  files_exists: boolean
}
