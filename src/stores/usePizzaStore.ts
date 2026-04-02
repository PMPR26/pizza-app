import { ref } from 'vue'
import { defineStore } from 'pinia'
import { api } from '@/services/api'
import { useShopStore } from '@/stores/shop'
import type { Pizza, Ingredient, EditingPizza } from '@/types'

interface PizzaFormData {
  name: string
  description: string
  price: number
  ingredientIds: number[]
  imageFile?: File | null
}

interface PaginatedPizzas {
  data: Pizza[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export const usePizzaStore = defineStore('pizza', () => {
  const pizzas = ref<Pizza[]>([])
  const paginatedPizzas = ref<PaginatedPizzas | null>(null)
  const editingPizza = ref<EditingPizza | null>(null)
  const loading = ref(false)
  const errorMessage = ref('')
  const successMessage = ref('')
  const searchQuery = ref('')
  const currentPage = ref(1)
  const perPage = ref(10)

  function validatePizzaData(data: PizzaFormData): string | null {
    // Validar nombre
    if (!data.name.trim()) return 'El nombre de la pizza es obligatorio'
    if (data.name.trim().length < 3) return 'El nombre debe tener al menos 3 caracteres'
    if (data.name.trim().length > 100) return 'El nombre no puede exceder 100 caracteres'
    
    // Validar descripción
    if (!data.description.trim()) return 'La descripción es obligatoria'
    if (data.description.trim().length < 10) return 'La descripción debe tener al menos 10 caracteres'
    if (data.description.trim().length > 500) return 'La descripción no puede exceder 500 caracteres'
    
    // Validar precio
    if (data.price <= 0) return 'El precio debe ser mayor a 0'
    if (data.price > 999) return 'El precio no puede ser mayor a 999 Bs'
    if (!/^\d+(\.\d{1,2})?$/.test(data.price.toString())) return 'El precio debe ser un número válido con máximo 2 decimales'
    
    // Validar ingredientes
    if (!data.ingredientIds.length) return 'Debe seleccionar al menos un ingrediente'
    if (data.ingredientIds.length > 20) return 'No puedes seleccionar más de 20 ingredientes'
    
    return null
  }

  async function fetchPizzas(page = 1, search = '') {
    loading.value = true
    errorMessage.value = ''
    try {
      const params = new URLSearchParams({
        page: page.toString(),
        per_page: perPage.value.toString(),
      })
      if (search) {
        params.append('search', search)
      }
      const { data } = await api.get<PaginatedPizzas>(`/pizzas?${params}`)
      paginatedPizzas.value = data
      pizzas.value = data.data // Para compatibilidad
      currentPage.value = data.current_page
    } catch (err: any) {
      errorMessage.value = err?.response?.data?.message || 'Error al cargar pizzas'
    } finally {
      loading.value = false
    }
  }

  function setEditingPizza(pizza: Pizza) {
    editingPizza.value = {
      id: pizza.id,
      name: pizza.name,
      description: pizza.description,
      price: pizza.price,
      image: pizza.image,
      ingredientIds: pizza.ingredients?.map((i: Ingredient) => i.id) || []
    }
  }

  function clearEditingPizza() {
    editingPizza.value = null
  }

  function setSearchQuery(query: string) {
    searchQuery.value = query
    currentPage.value = 1
    fetchPizzas(1, query)
  }

  function setPage(page: number) {
    currentPage.value = page
    fetchPizzas(page, searchQuery.value)
  }

  function setPerPage(newPerPage: number) {
    perPage.value = newPerPage
    currentPage.value = 1
    fetchPizzas(1, searchQuery.value)
  }

  async function savePizza(formData: PizzaFormData) {
    const validationError = validatePizzaData(formData)
    if (validationError) {
      errorMessage.value = validationError
      return
    }

    loading.value = true
    errorMessage.value = ''
    successMessage.value = ''
    try {
      const payload = new FormData()
      payload.append('name', formData.name.trim())
      payload.append('description', formData.description.trim())
      payload.append('price', formData.price.toString())

      formData.ingredientIds.forEach(id => payload.append('ingredients[]', id.toString()))
      if (formData.imageFile) {
        payload.append('image', formData.imageFile)
      }

      if (editingPizza.value?.id) {
        payload.append('_method', 'PUT')
        await api.post(`/pizzas/${editingPizza.value.id}`, payload, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
        successMessage.value = 'Pizza actualizada correctamente'
      } else {
        await api.post('/pizzas', payload, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
        successMessage.value = 'Pizza creada correctamente'
      }
      clearEditingPizza()
      await fetchPizzas()
      await useShopStore().fetchPizzas()
    } catch (err: any) {
      errorMessage.value = err?.response?.data?.message || 'Error al guardar la pizza'
      console.error('Error saving pizza:', err)
    } finally {
      loading.value = false
    }
  }

  async function deletePizza(id: number) {
    if (!confirm('¿Estás seguro de que quieres eliminar esta pizza?')) return

    loading.value = true
    errorMessage.value = ''
    successMessage.value = ''
    try {
      await api.delete(`/pizzas/${id}`)
      successMessage.value = 'Pizza eliminada correctamente'
      await fetchPizzas()
      await useShopStore().fetchPizzas()
    } catch (err: any) {
      errorMessage.value = err?.response?.data?.message || 'Error al eliminar la pizza'
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
    paginatedPizzas,
    editingPizza,
    loading,
    errorMessage,
    successMessage,
    searchQuery,
    currentPage,
    perPage,
    fetchPizzas,
    setSearchQuery,
    setPage,
    setPerPage,
    setEditingPizza,
    clearEditingPizza,
    savePizza,
    deletePizza,
    clearErrorMessage,
    clearSuccessMessage
  }
})