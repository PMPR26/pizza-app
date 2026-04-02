import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { api } from '@/services/api'
import type { Pizza, Ingredient, Order, EditingPizza } from '@/types'

export const useShopStore = defineStore('shop', () => {
  // Estado
  const pizzas = ref<Pizza[]>([])
  const adminOrders = ref<Order[]>([])
  const myOrders = ref<Order[]>([])
  const cart = ref<{ pizza: Pizza; quantity: number }[]>([])

  const loading = ref(false)
  const errorMessage = ref('')
  const successMessage = ref('')

  // Computed
  const pizzasCount = computed(() => pizzas.value.length)
  const ordersCount = computed(() => adminOrders.value.length)
  const cartTotal = computed(() => cart.value.reduce((total, item) => total + (item.pizza.price * item.quantity), 0))
  const cartCount = computed(() => cart.value.reduce((count, item) => count + item.quantity, 0))

  // Fetchers
  async function fetchPizzas() {
    loading.value = true
    errorMessage.value = ''
    try {
      const { data: response } = await api.get<any>('/pizzas')
      pizzas.value = response.data || response
    } catch (err: any) {
      errorMessage.value = err?.response?.data?.message || 'Error al cargar pizzas'
    } finally {
      loading.value = false
    }
  }

  async function fetchAllOrders() {
    loading.value = true
    errorMessage.value = ''
    try {
      const { data: response } = await api.get<any>('/orders')
      adminOrders.value = response.data || response
    } catch (err: any) {
      errorMessage.value = err?.response?.data?.message || 'Error al cargar pedidos'
    } finally {
      loading.value = false
    }
  }

  async function fetchMyOrders() {
    loading.value = true
    errorMessage.value = ''
    try {
      const { data: response } = await api.get<any>('/my-orders')
      const orders = response.data || response
      myOrders.value = orders
      return orders
    } catch (err: any) {
      errorMessage.value = err?.response?.data?.message || 'Error al cargar mis pedidos'
      return []
    } finally {
      loading.value = false
    }
  }

  async function fetchAllData() {
    loading.value = true
    errorMessage.value = ''
    try {
      await Promise.all([fetchPizzas(), fetchAllOrders()])
    } catch {
      errorMessage.value = 'Error al cargar datos'
    } finally {
      loading.value = false
    }
  }

  // Cart functions
  function addToCart(pizza: Pizza) {
    const existing = cart.value.find(item => item.pizza.id === pizza.id)
    if (existing) {
      existing.quantity++
    } else {
      cart.value.push({ pizza, quantity: 1 })
    }
  }

  function removeFromCart(pizzaId: number) {
    cart.value = cart.value.filter(item => item.pizza.id !== pizzaId)
  }

  function incrementQuantity(pizzaId: number) {
    const item = cart.value.find(item => item.pizza.id === pizzaId)
    if (item) item.quantity++
  }

  function decrementQuantity(pizzaId: number) {
    const item = cart.value.find(item => item.pizza.id === pizzaId)
    if (item && item.quantity > 1) {
      item.quantity--
    } else {
      removeFromCart(pizzaId)
    }
  }

  async function placeOrder() {
    if (!cart.value.length) throw new Error('El carrito está vacío')

    loading.value = true
    errorMessage.value = ''
    try {
      await api.post('/orders/checkout', {
        items: cart.value.map(item => ({
          pizza_id: item.pizza.id,
          quantity: item.quantity,
        })),
      })
      cart.value = [] // Vaciar carrito después de ordenar
      successMessage.value = 'Pedido realizado correctamente'
    } catch (err: any) {
      errorMessage.value = err?.response?.data?.message || 'Error al realizar el pedido'
      throw err
    } finally {
      loading.value = false
    }
  }

  function clearErrorMessage() {
    errorMessage.value = ''
  }

  function clearSuccessMessage() {
    successMessage.value = ''
  }

  return {
    pizzas,
    adminOrders,
    myOrders,
    cart,
    loading,
    errorMessage,
    successMessage,
    pizzasCount,
    ordersCount,
    cartTotal,
    cartCount,
    fetchPizzas,
    fetchAllOrders,
    fetchMyOrders,
    fetchAllData,
    addToCart,
    removeFromCart,
    incrementQuantity,
    decrementQuantity,
    placeOrder,
    clearErrorMessage,
    clearSuccessMessage,
  }
})