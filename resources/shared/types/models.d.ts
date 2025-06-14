export interface File {
  // columns
  id: number
  name: string
  path: string
  mime_type?: string | null
  size?: number | null
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
}

export interface Fileable {
  // relations
  fileable?: Fileable
  file?: File
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
}
