export interface Category {
  // columns
  id: string
  name: string
  slug: string
  type: string
  color: string | null
  icon: string | null
  parent_id: string | null
  created_at: string | null
  updated_at: string | null
  // relations
  parent: Category
  children: Category[]
  dishes: Dish[]
}

export interface CollectionItem {
  // columns
  id: string
  collection_id: string
  item_type: string
  item_id: string
  sort_order: number
  created_at: string | null
  updated_at: string | null
  // relations
  collection: CuratedCollection
  item: CollectionItem
}

export interface CuratedCollection {
  // columns
  id: string
  title: string
  description: string | null
  type: string
  image_path: string | null
  is_active: boolean
  sort_order: number
  created_by_admin_id: string
  created_at: string | null
  updated_at: string | null
  // relations
  created_by_admin: User
  items: CollectionItem[]
}

export interface Dish {
  // columns
  id: string
  menu_id: string
  name: string
  description: string | null
  price: number
  image_path: string | null
  is_available: boolean
  allergens: Record<string, unknown> | null
  sort_order: number
  created_at: string | null
  updated_at: string | null
  deleted_at: string | null
  // mutators
  likes_count: number
  // relations
  menu: Menu
  restaurant: Restaurant
  categories: Category[]
  likes: Like[]
  collection_items: CollectionItem[]
}

export interface Event {
  // columns
  id: string
  restaurant_id: string
  title: string
  description: string | null
  start_date: string
  end_date: string
  max_participants: number | null
  price: number | null
  status: string
  created_at: string | null
  updated_at: string | null
  deleted_at: string | null
  // mutators
  likes_count: number
  is_active: boolean
  // relations
  restaurant: Restaurant
  likes: Like[]
  collection_items: CollectionItem[]
}

export interface Like {
  // columns
  id: string
  telegram_user_id: string
  likeable_type: string
  likeable_id: string
  created_at: string | null
  updated_at: string | null
  // relations
  likeable: Like
}

export interface Menu {
  // columns
  id: string
  restaurant_id: string
  name: string
  description: string | null
  is_active: boolean
  sort_order: number
  created_at: string | null
  updated_at: string | null
  // relations
  restaurant: Restaurant
  dishes: Dish[]
}

export interface News {
  // columns
  id: string
  title: string
  content: string
  image_path: string | null
  author_type: string
  author_id: string
  status: string
  is_featured: boolean
  published_at: string | null
  created_at: string | null
  updated_at: string | null
  deleted_at: string | null
  // mutators
  likes_count: number
  // relations
  author: News
  likes: Like[]
  collection_items: CollectionItem[]
}

export interface Permission {
  // columns
  id: string
  name: string
  guard_name: string
  created_at: string | null
  updated_at: string | null
  // relations
  roles: Role[]
  users: User[]
  permissions: Permission[]
}

export interface Restaurant {
  // columns
  id: string
  name: string
  description: string | null
  address: string | null
  phone: string | null
  telegram_bot_token: string
  settings: Record<string, unknown> | null
  is_active: boolean
  created_by_admin_id: string
  created_at: string | null
  updated_at: string | null
  deleted_at: string | null
  // relations
  created_by_admin: User
  menus: Menu[]
  events: Event[]
  news: News[]
  collection_items: CollectionItem[]
}

export interface Role {
  // columns
  id: string
  name: string
  guard_name: string
  created_at: string | null
  updated_at: string | null
  // relations
  permissions: Permission[]
  users: User[]
}

export interface User {
  // columns
  id: string
  name: string
  email: string
  email_verified_at: string | null
  restaurant_id: string | null
  password?: string
  two_factor_secret?: string | null
  two_factor_recovery_codes?: string | null
  two_factor_confirmed_at: string | null
  remember_token?: string | null
  created_at: string | null
  updated_at: string | null
  deleted_at: string | null
  // relations
  restaurant: Restaurant
  notifications: any[]
  roles: Role[]
  permissions: Permission[]
  tokens: any[]
}

