export type UserRole = 'admin' | 'customer'

export interface User {
  id: number
  name: string
  email: string
  role: UserRole
  created_at?: string
}

export interface Ingredient {
  id: number
  name: string
}

export interface Pizza {
  id: number
  name: string
  description: string
  price: number
  image: string
  ingredients?: Ingredient[]
}

export interface Order {
  id: number
  user_id: number
  pizza_id: number
  quantity: number
  total: number
  ordered_at: string
  checkout_group_id?: string | null
  pizza?: Pizza
  user?: User
}

export interface EditingPizza {
  id: number
  name: string
  description: string
  price: number
  image: string
  ingredientIds: number[]
}
