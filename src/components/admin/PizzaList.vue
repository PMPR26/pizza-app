<script setup lang="ts">
import { onMounted, computed, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { usePizzaStore } from '@/stores/usePizzaStore'
import PizzaAdminCard from './PizzaAdminCard.vue'
import Alert from '@/components/Alert.vue'
import type { Pizza } from '@/types'
import '@/assets/styles/pizzaList.css'

const pizzaStore = usePizzaStore()
const {
  paginatedPizzas,
  loading,
  errorMessage,
  successMessage,
  searchQuery,
  currentPage,
  perPage
} = storeToRefs(pizzaStore)

const showSuccessAlert = ref(false)

onMounted(() => {
  pizzaStore.fetchPizzas()
})

function handleSearch() {
  pizzaStore.setSearchQuery(searchQuery.value)
}

function handlePageChange(page: number) {
  pizzaStore.setPage(page)
}

function handleDelete(id: number) {
  if (confirm('¿Estás seguro de que quieres eliminar esta pizza? Esta acción no se puede deshacer.')) {
    pizzaStore.deletePizza(id)
  }
}

function retryFetch() {
  pizzaStore.fetchPizzas(currentPage.value, searchQuery.value)
}

const totalPages = computed(() => paginatedPizzas.value?.last_page || 1)
const hasPizzas = computed(() => paginatedPizzas.value?.data.length || 0)
</script>

<template>
  <section class="pizza-list">
    <!-- Alertas de notificación -->
    <transition name="fade">
      <Alert
        v-if="successMessage"
        :message="successMessage"
        type="success"
        :auto-close="4000"
        class="pizza-list__alert-fixed"
      />
    </transition>

    <transition name="fade">
      <Alert
        v-if="errorMessage"
        :message="errorMessage"
        type="error"
        :auto-close="5000"
        class="pizza-list__alert-fixed"
        @close="pizzaStore.clearErrorMessage"
      />
    </transition>

    <!-- Header con título -->
    <div class="pizza-list__header">
      <div>
        <h2 class="pizza-list__title">📜 Catálogo de Pizzas</h2>
        <p class="pizza-list__subtitle">Administra y personaliza tu menú</p>
      </div>
    </div>

    <!-- Buscador -->
    <div class="pizza-list__search-container">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="🔍 Buscar pizza por nombre..."
        class="pizza-list__search"
        @input="handleSearch"
      />
    </div>

    <!-- Contenido Principal -->
    <div class="pizza-list__content">
      <!-- Loading -->
      <div v-if="loading" class="pizza-list__loading">
        <div class="pizza-list__spinner"></div>
        <p>⏳ Cargando pizzas...</p>
      </div>

      <!-- Lista de pizzas -->
      <div v-else-if="paginatedPizzas && hasPizzas">
        <div class="pizza-list__grid">
          <PizzaAdminCard
            v-for="pizza in paginatedPizzas.data"
            :key="pizza.id"
            :pizza="pizza"
            @edit="pizzaStore.setEditingPizza"
            @delete="handleDelete"
          />
        </div>
      </div>

      <!-- Sin resultados -->
      <div v-else class="pizza-list__empty">
        <p>📭 No se encontraron pizzas</p>
        <p class="pizza-list__empty-subtitle">Crea tu primera pizza para comenzar</p>
      </div>
    </div>

    <!-- Paginación -->
    <div v-if="totalPages > 1" class="pizza-list__pagination">
      <div class="pizza-list__pagination-controls">
        <button
          @click="handlePageChange(currentPage - 1)"
          :disabled="currentPage === 1"
          class="btn btn--secondary btn--small"
        >
          ← Anterior
        </button>

        <span class="pizza-list__pagination-info">
          Página <strong>{{ currentPage }}</strong> de <strong>{{ totalPages }}</strong>
        </span>

        <button
          @click="handlePageChange(currentPage + 1)"
          :disabled="currentPage === totalPages"
          class="btn btn--secondary btn--small"
        >
          Siguiente →
        </button>
      </div>

      <select
        v-model.number="perPage"
        @change="pizzaStore.setPerPage(Number(($event.target as HTMLSelectElement).value))"
        class="pizza-list__pagination-select"
      >
        <option :value="5">5 por página</option>
        <option :value="10">10 por página</option>
        <option :value="20">20 por página</option>
        <option :value="50">50 por página</option>
      </select>
    </div>
  </section>
</template>