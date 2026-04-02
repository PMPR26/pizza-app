import { ref } from 'vue'
import { defineStore } from 'pinia'
import { api } from '@/services/api'

export const useOrderStore = defineStore('order', () => {
  const adminOrders = ref<any[]>([])
  const loading = ref(false)
  const errorMessage = ref('')

  async function fetchOrders() {
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

  return {
    adminOrders,
    loading,
    errorMessage,
    fetchOrders
  }
})