<template>
  <section class="ingredient-manager">
    <!-- Alertas de notificación -->
    <transition name="fade">
      <Alert
        v-if="localSuccessMessage"
        :message="localSuccessMessage"
        type="success"
        :auto-close="4000"
        @close="localSuccessMessage = ''"
        class="ingredient-manager__alert-fixed"
      />
    </transition>

    <transition name="fade">
      <Alert
        v-if="localErrorMessage"
        :message="localErrorMessage"
        type="error"
        :auto-close="5000"
        @close="localErrorMessage = ''"
        class="ingredient-manager__alert-fixed"
      />
    </transition>

    <!-- Header -->
    <div class="ingredient-manager__header">
      <div>
        <h2 class="ingredient-manager__title">🥫 Administrar Ingredientes</h2>
        <p class="ingredient-manager__subtitle">Crea, edita y elimina ingredientes para tus pizzas</p>
      </div>
      <button @click="openForm()" class="btn btn--primary">
        ➕ Crear Ingrediente
      </button>
    </div>

    <!-- Buscador -->
    <div class="ingredient-manager__search-container">
      <input
        v-model="searchTerm"
        type="text"
        placeholder="🔍 Buscar ingrediente..."
        class="ingredient-manager__search"
      />
    </div>

    <!-- Formulario crear/editar -->
    <transition name="slideDown">
      <div v-if="formVisible" class="ingredient-manager__form-container">
        <h3 class="ingredient-manager__form-title">
          {{ form.id ? '✏️ Editar Ingrediente' : '🆕 Crear Ingrediente' }}
        </h3>
        <input
          v-model="form.name"
          type="text"
          placeholder="Nombre del ingrediente"
          class="ingredient-manager__form-input"
          @keyup.enter="submitForm"
        />
        <div class="ingredient-manager__form-actions">
          <button @click="submitForm()" class="btn btn--primary">
            ✓ {{ form.id ? 'Actualizar' : 'Crear' }}
          </button>
          <button @click="closeForm()" class="btn btn--secondary">
            ✕ Cancelar
          </button>
        </div>
      </div>
    </transition>

    <!-- Contenido principal -->
    <div class="ingredient-manager__content">
      <!-- Tabla de ingredientes -->
      <div v-if="paginatedIngredients.length > 0" class="ingredient-manager__table-wrapper">
        <table class="ingredient-manager__table">
          <thead class="ingredient-manager__table-head">
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody class="ingredient-manager__table-body">
            <tr v-for="ingredient in paginatedIngredients" :key="ingredient.id" class="ingredient-manager__table-row">
              <td class="ingredient-manager__table-cell ingredient-manager__table-cell--id">{{ ingredient.id }}</td>
              <td class="ingredient-manager__table-cell">{{ ingredient.name }}</td>
              <td class="ingredient-manager__table-cell ingredient-manager__table-cell--actions">
                <button
                  @click="editIngredient(ingredient)"
                  class="btn btn--edit btn--small"
                  title="Editar"
                >
                  ✏️ Editar
                </button>
                <button
                  @click="deleteIngredientHandler(ingredient.id)"
                  class="btn btn--delete btn--small"
                  title="Eliminar"
                >
                  🗑️ Eliminar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Sin resultados -->
      <div v-else class="ingredient-manager__empty">
        <p>📭 No hay ingredientes registrados</p>
        <p class="ingredient-manager__empty-subtitle">Crea tu primer ingrediente para comenzar</p>
      </div>
    </div>

    <!-- Paginación -->
    <div v-if="totalPages > 1" class="ingredient-manager__pagination">
      <button
        @click="prevPage"
        :disabled="currentPage === 1"
        class="btn btn--secondary btn--small"
      >
        ← Anterior
      </button>
      <span class="ingredient-manager__pagination-info">
        Página <strong>{{ currentPage }}</strong> de <strong>{{ totalPages }}</strong>
      </span>
      <button
        @click="nextPage"
        :disabled="currentPage === totalPages"
        class="btn btn--secondary btn--small"
      >
        Siguiente →
      </button>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useIngredientStore } from '@/stores/useIngredientStore'
import Alert from '@/components/Alert.vue'
import '@/assets/styles/ingredientes.css'

const ingredientStore = useIngredientStore()
const localErrorMessage = ref('')
const localSuccessMessage = ref('')
const formVisible = ref(false)
const form = ref<{ id?: number, name: string }>({ name: '' })
const searchTerm = ref('')
const currentPage = ref(1)
const perPage = 5

const filteredIngredients = computed(() => {
  const ingredients = ingredientStore.ingredients
  if (!searchTerm.value.trim()) return ingredients
  return ingredients.filter(i => i.name.toLowerCase().includes(searchTerm.value.toLowerCase()))
})

const totalPages = computed(() => Math.ceil(filteredIngredients.value.length / perPage))
const paginatedIngredients = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filteredIngredients.value.slice(start, start + perPage)
})

watch(searchTerm, () => currentPage.value = 1)

function openForm() {
  form.value = { name: '' }
  formVisible.value = true
  localErrorMessage.value = ''
  localSuccessMessage.value = ''
}

function editIngredient(ingredient: any) {
  form.value = { id: ingredient.id, name: ingredient.name }
  formVisible.value = true
  localErrorMessage.value = ''
  localSuccessMessage.value = ''
}

function closeForm() {
  formVisible.value = false
  form.value = { name: '' }
}

function validateIngredientName(name: string): string | null {
  const trimmed = name.trim()
  
  // Validar vacío
  if (!trimmed) {
    return 'El nombre del ingrediente es obligatorio'
  }
  
  // Validar longitud mínima
  if (trimmed.length < 2) {
    return 'El nombre debe tener al menos 2 caracteres'
  }
  
  // Validar longitud máxima
  if (trimmed.length > 50) {
    return 'El nombre no puede exceder 50 caracteres'
  }
  
  // Validar que no sea solo números
  if (/^\d+$/.test(trimmed)) {
    return 'El nombre no puede ser solo números'
  }
  
  return null
}

async function submitForm() {
  localErrorMessage.value = ''
  localSuccessMessage.value = ''
  
  const validationError = validateIngredientName(form.value.name)
  if (validationError) {
    localErrorMessage.value = validationError
    return
  }

  try {
    const fd = new FormData()
    if (form.value.id) fd.append('id', form.value.id.toString())
    fd.append('name', form.value.name.trim())
    await ingredientStore.saveIngredient(fd)
    localSuccessMessage.value = form.value.id ? 'Ingrediente actualizado.' : 'Ingrediente creado.'
    formVisible.value = false
  } catch (err: any) {
    localErrorMessage.value = err?.message || 'Error al guardar ingrediente.'
  }
}

async function deleteIngredientHandler(id: number) {
  if (!confirm('¿Eliminar ingrediente?')) return
  try {
    await ingredientStore.deleteIngredient(id)
    localSuccessMessage.value = 'Ingrediente eliminado.'
  } catch (err: any) {
    localErrorMessage.value = err?.message || 'Error al eliminar ingrediente.'
  }
}

function nextPage() { if (currentPage.value < totalPages.value) currentPage.value++ }
function prevPage() { if (currentPage.value > 1) currentPage.value-- }

onMounted(() => ingredientStore.fetchIngredients())
</script>