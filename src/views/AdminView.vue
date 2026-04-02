<template>
  <section class="admin-view">
    <!-- Header -->
    <header class="admin-view__header">
      <div>
        <h1 class="admin-view__title">⚙️ Panel de Administración</h1>
        <p class="admin-view__subtitle">Gestiona pizzas, ingredientes y revisa los pedidos de clientes</p>
      </div>
    </header>

    <!-- Menú de secciones -->
    <nav class="admin-view__nav">
      <button
        @click="activeSection = 'ingredients'"
        :class="['btn', activeSection === 'ingredients' ? 'btn--primary' : 'btn--secondary']"
      >
        🥫 Ingredientes
      </button>
      <button
        @click="activeSection = 'pizzas'"
        :class="['btn', activeSection === 'pizzas' ? 'btn--primary' : 'btn--secondary']"
      >
        🍕 Pizzas
      </button>
      <button
        @click="activeSection = 'orders'"
        :class="['btn', activeSection === 'orders' ? 'btn--primary' : 'btn--secondary']"
      >
        📦 Pedidos
      </button>
    </nav>

    <!-- Contenido dinámico -->
    <div class="admin-view__content">
      <!-- Ingredientes -->
      <IngredientManager v-if="activeSection === 'ingredients'" class="w-full" />

      <!-- Pizzas -->
      <div v-if="activeSection === 'pizzas'" class="admin-view__section">
        <nav class="admin-view__section-nav">
          <button
            @click="pizzaTab = 'list'"
            :class="['btn btn--small', pizzaTab === 'list' ? 'btn--primary' : 'btn--secondary']"
          >
            📜 Listado
          </button>
          <button
            @click="createPizza"
            :class="['btn btn--small', pizzaTab === 'form' ? 'btn--primary' : 'btn--secondary']"
          >
            ➕ Crear / Editar
          </button>
        </nav>

        <PizzaList v-if="pizzaTab === 'list'" class="w-full" />
        <PizzaForm
          v-if="pizzaTab === 'form'"
          :ingredients="ingredients"
          :editing-pizza="editingPizza"
          @submit="submitPizzaForm"
          class="admin-view__form"
        />
      </div>

      <!-- Pedidos -->
      <OrdersPanel v-if="activeSection === 'orders'" class="w-full" :orders="adminOrders" />
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import type { EditingPizza } from '@/types'
import { storeToRefs } from 'pinia'

// Stores
import { useShopStore } from '@/stores/shop'
import { useIngredientStore } from '@/stores/useIngredientStore'
import { usePizzaStore } from '@/stores/usePizzaStore'

import IngredientManager from '@/components/admin/IngredientManager.vue'
import PizzaList from '@/components/admin/PizzaList.vue'
import PizzaForm from '@/components/admin/PizzaForm.vue'
import OrdersPanel from '@/components/admin/OrdersPanel.vue'

const shop = useShopStore()
const ingredientStore = useIngredientStore()
const pizzaStore = usePizzaStore()

// Referencias pizzas y pedidos
const { adminOrders, errorMessage: shopErrorMessage, successMessage: shopSuccessMessage } = storeToRefs(shop)
const { ingredients, errorMessage: ingredientErrorMessage, successMessage: ingredientSuccessMessage } = storeToRefs(ingredientStore)
const { editingPizza, errorMessage: pizzaErrorMessage, successMessage: pizzaSuccessMessage } = storeToRefs(pizzaStore)

const activeSection = ref<'ingredients' | 'pizzas' | 'orders'>('ingredients')
const pizzaTab = ref<'list' | 'form'>('list')

// Cambiar a form cuando se edita una pizza
watch(
  () => editingPizza.value,
  (newEditing) => {
    if (newEditing) {
      pizzaTab.value = 'form'
    }
  }
)

// Limpiar notificaciones cuando cambia la sección activa
watch(
  () => activeSection.value,
  () => {
    // Limpiar todas las notificaciones cuando se cambia de sección
    pizzaStore.clearErrorMessage()
    pizzaStore.clearSuccessMessage()
    ingredientStore.clearErrorMessage()
    ingredientStore.clearSuccessMessage()
    shop.clearErrorMessage()
    shop.clearSuccessMessage()
  }
)

onMounted(async () => {
  await ingredientStore.fetchIngredients()
  await pizzaStore.fetchPizzas()
  await shop.fetchAllOrders()
})

// Funciones pizzas
function createPizza() {
  pizzaStore.clearEditingPizza()
  pizzaTab.value = 'form'
}

async function submitPizzaForm(formData: { name: string; description: string; price: number; ingredientIds: number[]; imageFile: File | null }) {
  await pizzaStore.savePizza(formData)
  pizzaTab.value = 'list'
}
</script>