export interface Category {
  // columns
  id: string
  slug: string
  name: string
  order: number
  restaurant_id: string
  _lft: number
  _rgt: number
  parent_id?: string | null
  created_at?: Date | null
  updated_at?: Date | null
  // mutators
  full_path: string
  // relations
  restaurant?: Restaurant
  dishes?: Dish[]
  dishes_count: number
  dishes_exists: boolean
  parent?: Category
  children?: Category[]
  children_count: number
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
  id: number
  slug: string
  name: string
  restaurant_id: string
  order: number
  created_at?: Date | null
  updated_at?: Date | null
}

export interface File {
  // columns
  id: string
  url: string
  name: string
  extension: string
  created_at?: Date | null
  updated_at?: Date | null
}

export interface News {
  // columns
  id: number
  slug: string
  name: string
  restaurant_id: string
  order: number
  created_at?: Date | null
  updated_at?: Date | null
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
  roles_count: number
  roles_exists: boolean
  users?: User[]
  users_count: number
  users_exists: boolean
  permissions?: Permission[]
  permissions_count: number
  permissions_exists: boolean
}

export interface Fileable {
  // relations
  fileable?: Fileable
  file?: File
}

export interface Restaurant {
  // columns
  id: number
  slug: string
  name: string
  created_at?: Date | null
  updated_at?: Date | null
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
  permissions_count: number
  permissions_exists: boolean
  users?: User[]
  users_count: number
  users_exists: boolean
}

export interface User {
  // columns
  id: string
  first_name: string
  last_name?: string | null
  middle_name?: string | null
  email: string
  email_verified_at?: Date | null
  created_at?: Date | null
  updated_at?: Date | null
  deleted_at?: Date | null
  // overrides
  tokens: never
  notifications: never
  // relations
  roles?: Role[]
  roles_count: number
  roles_exists: boolean
  permissions?: Permission[]
  permissions_count: number
  permissions_exists: boolean
}
