// src/stores/useIngredientStore.ts
import { ref } from 'vue'
import { defineStore } from 'pinia'
import { api } from '@/services/api'
import { useShopStore } from '@/stores/shop'

export const useIngredientStore = defineStore('ingredient', () => {
  const ingredients = ref<{ id: number; name: string }[]>([])
  const loading = ref(false)
  const errorMessage = ref('')
  const successMessage = ref('')

  // Listar
  async function fetchIngredients() {
    loading.value = true
    errorMessage.value = ''
    try {
      const { data: response } = await api.get<any>('/ingredients')
      ingredients.value = response.data || response
    } catch (err: any) {
      errorMessage.value = err?.response?.data?.message || 'Error al cargar ingredientes'
    } finally {
      loading.value = false
    }
  }

  // Crear / actualizar
  async function saveIngredient(formData: FormData) {
    loading.value = true
    errorMessage.value = ''
    successMessage.value = ''
    try {
      if (formData.get('id')) {
        const id = formData.get('id')
        await api.put(`/ingredients/${id}`, formData)
        successMessage.value = 'Ingrediente actualizado'
      } else {
        await api.post('/ingredients', formData)
        successMessage.value = 'Ingrediente creado'
      }
      await fetchIngredients()
      await useShopStore().fetchPizzas()
    } catch (err: any) {
      errorMessage.value = err?.response?.data?.message || 'Error al guardar ingrediente'
    } finally {
      loading.value = false
    }
  }

  // Eliminar
  async function deleteIngredient(id: number) {
    loading.value = true
    errorMessage.value = ''
    successMessage.value = ''
    try {
      await api.delete(`/ingredients/${id}`)
      successMessage.value = 'Ingrediente eliminado'
      await fetchIngredients()
      await useShopStore().fetchPizzas()
    } catch (err: any) {
      errorMessage.value = err?.response?.data?.message || 'Error al eliminar ingrediente'
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
    ingredients,
    loading,
    errorMessage,
    successMessage,
    fetchIngredients,
    saveIngredient,
    deleteIngredient,
    clearErrorMessage,
    clearSuccessMessage,
  }
})