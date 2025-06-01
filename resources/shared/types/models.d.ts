export interface Category {
  // columns
  id: string
  name: string
  slug: string
  type: string
  color?: string | null
  icon?: string | null
  parent_id?: string | null
  created_at?: Date | null
  updated_at?: Date | null
  // relations
  parent?: Category
  children?: Category[]
  dishes?: Dish[]
}

export interface CollectionItem {
  // columns
  id: string
  collection_id: string
  item_type: string
  item_id: string
  sort_order: number
  created_at?: Date | null
  updated_at?: Date | null
  // relations
  collection?: CuratedCollection
  item?: CollectionItem
}

export interface CuratedCollection {
  // columns
  id: string
  title: string
  description?: string | null
  type: string
  image_path?: string | null
  is_active: boolean
  sort_order: number
  created_by_admin_id: string
  created_at?: Date | null
  updated_at?: Date | null
  // relations
  created_by_admin?: User
  items?: CollectionItem[]
}

export interface Dish {
  // columns
  id: string
  menu_id: string
  name: string
  description?: string | null
  price: number
  image_path?: string | null
  is_available: boolean
  allergens?: Record<string, unknown> | null
  sort_order: number
  created_at?: Date | null
  updated_at?: Date | null
  deleted_at?: Date | null
  // overrides
  likes_count: number
  // relations
  menu?: Menu
  restaurant?: Restaurant
  categories?: Category[]
  likes?: Like[]
  collection_items?: CollectionItem[]
}

export interface Event {
  // columns
  id: string
  restaurant_id: string
  title: string
  description?: string | null
  start_date: Date
  end_date: Date
  max_participants?: number | null
  price?: number | null
  status: string
  created_at?: Date | null
  updated_at?: Date | null
  deleted_at?: Date | null
  // overrides
  is_active: boolean
  likes_count: number
  // relations
  restaurant?: Restaurant
  likes?: Like[]
  collection_items?: CollectionItem[]
}

export interface File {
}

export interface Like {
  // columns
  id: string
  telegram_user_id: string
  likeable_type: string
  likeable_id: string
  created_at?: Date | null
  updated_at?: Date | null
  // relations
  likeable?: Like
}

export interface Menu {
  // columns
  id: string
  restaurant_id: string
  name: string
  description?: string | null
  is_active: boolean
  sort_order: number
  created_at?: Date | null
  updated_at?: Date | null
  // relations
  restaurant?: Restaurant
  dishes?: Dish[]
}

export interface News {
  // columns
  id: string
  title: string
  content: string
  image_path?: string | null
  author_type: string
  author_id: string
  status: string
  is_featured: boolean
  published_at?: Date | null
  created_at?: Date | null
  updated_at?: Date | null
  deleted_at?: Date | null
  // overrides
  likes_count: number
  // relations
  author?: News
  likes?: Like[]
  collection_items?: CollectionItem[]
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
}

export interface Fileable {
  // relations
  fileable?: Fileable
  file?: File
}

export interface Restaurant {
  // columns
  id: string
  name: string
  description?: string | null
  address?: string | null
  phone?: string | null
  telegram_bot_token: string
  settings?: Record<string, unknown> | null
  is_active: boolean
  created_by_admin_id: string
  created_at?: Date | null
  updated_at?: Date | null
  deleted_at?: Date | null
  // relations
  created_by_admin?: User
  menus?: Menu[]
  events?: Event[]
  news?: News[]
  collection_items?: CollectionItem[]
}

export interface Role {
  // columns
  id: string
  name: string
  guard_name: string
  created_at?: Date | null
  updated_at?: Date | null
  // relations
  permissions?: Permission[]
  users?: User[]
}

export interface User {
  // columns
  id: string
  name: string
  email: string
  email_verified_at?: Date | null
  restaurant_id?: string | null
  two_factor_confirmed_at?: Date | null
  created_at?: Date | null
  updated_at?: Date | null
  deleted_at?: Date | null
  // overrides
  tokens: never
  notifications: never
  // relations
  restaurant?: Restaurant
  roles?: Role[]
  permissions?: Permission[]
}
