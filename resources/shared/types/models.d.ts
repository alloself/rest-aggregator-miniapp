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
  // relations
  parent?: Category
  children?: Category[]
  // counts
  children_count: number
  // exists
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

export interface Fileable {
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
  description?: string | null
  working_hours?: string[] | null
  user_id: string
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
  permissions?: Permission[]
  // counts
  roles_count: number
  permissions_count: number
  // exists
  roles_exists: boolean
  permissions_exists: boolean
}
