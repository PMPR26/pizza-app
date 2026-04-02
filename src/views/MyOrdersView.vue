<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useShopStore } from '@/stores/shop'
import { useAuthStore } from '@/stores/auth'
import { groupOrdersByCheckout, formatBs } from '@/utils/orderGroups'

const shop = useShopStore()
const auth = useAuthStore()

const { myOrders, adminOrders } = storeToRefs(shop)

const search = ref('')
const currentPage = ref(1)
const perPage = 6

const allGroups = computed(() => {
  const source = auth.isAdmin ? adminOrders.value : myOrders.value
  return groupOrdersByCheckout(source)
})

const filteredGroups = computed(() => {
  if (!search.value.trim()) return allGroups.value

  const term = search.value.toLowerCase()

  return allGroups.value.filter((group) => {
    const user = group.lines[0]?.user?.name?.toLowerCase() ?? ''
    const idStr = group.lines.map((l) => String(l.id)).join(' ')
    const pizzas = group.lines.map((l) => l.pizza?.name?.toLowerCase() ?? '').join(' ')

    return user.includes(term) || pizzas.includes(term) || idStr.includes(term)
  })
})

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredGroups.value.length / perPage)),
)

const visibleGroups = computed(() => {
  const start = (currentPage.value - 1) * perPage
  const end = start + perPage
  return filteredGroups.value.slice(start, end)
})

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++
}

function prevPage() {
  if (currentPage.value > 1) currentPage.value--
}

function formatDate(date?: string) {
  if (!date) return 'Sin fecha'

  return new Date(date).toLocaleString('es-BO', {
    dateStyle: 'medium',
    timeStyle: 'short',
  })
}

watch(search, () => {
  currentPage.value = 1
})

onMounted(async () => {
  if (auth.isAdmin) {
    await shop.fetchAllOrders()
  } else {
    await shop.fetchMyOrders()
  }
})
</script>

<template>
  <section class="orders-page">
    <div class="orders-page__header">
      <div>
        <h1 class="orders-page__title">
          {{ auth.isAdmin ? 'Todos los pedidos' : 'Mis pedidos' }}
        </h1>
        <p class="orders-page__subtitle">
          {{ auth.isAdmin ? 'Consulta pedidos de todos los clientes.' : 'Tu historial de pedidos.' }}
        </p>
      </div>

      <input
        v-model="search"
        type="text"
        placeholder="Buscar por cliente, pizza o número..."
        class="orders-page__search"
      />
    </div>

    <div v-if="visibleGroups.length" class="orders-grid">
      <article
        v-for="group in visibleGroups"
        :key="group.key"
        class="order-card"
      >
        <div class="order-card__header">
          <h2>
            Pedido #{{ group.lines[0]?.id ?? '—' }}
            <span v-if="group.lines.length > 1" class="order-card__badge">
              ({{ group.lines.length }} ítems)
            </span>
          </h2>
          <span class="order-card__date">{{ formatDate(group.orderedAt) }}</span>
        </div>

        <p v-if="auth.isAdmin" class="order-card__user">
          Cliente: {{ group.lines[0]?.user?.name ?? 'Sin nombre' }}
        </p>

        <ul class="order-card__lines">
          <li v-for="line in group.lines" :key="line.id" class="order-card__line">
            <span class="order-card__pizza">{{ line.pizza?.name }}</span>
            <span class="order-card__quantity">× {{ line.quantity }}</span>
            <span class="order-card__line-total">{{ formatBs(line.total) }}</span>
          </li>
        </ul>

        <p class="order-card__total order-card__total--grand">
          Total: {{ formatBs(group.grandTotal) }}
        </p>
      </article>
    </div>

    <div v-if="totalPages > 1" class="orders-pagination">
      <button type="button" @click="prevPage" :disabled="currentPage === 1">
        Anterior
      </button>

      <span>Página {{ currentPage }} de {{ totalPages }}</span>

      <button type="button" @click="nextPage" :disabled="currentPage === totalPages">
        Siguiente
      </button>
    </div>

    <p v-else-if="!visibleGroups.length" class="orders-empty">
      No hay pedidos para mostrar.
    </p>
  </section>
</template>

<style scoped>
.order-card__badge {
  font-size: 0.85rem;
  font-weight: 500;
  color: #6b7280;
}

.order-card__lines {
  list-style: none;
  margin: 0.75rem 0 0;
  padding: 0;
}

.order-card__line {
  display: flex;
  flex-wrap: wrap;
  align-items: baseline;
  gap: 0.5rem 1rem;
  padding: 0.5rem 0;
  border-bottom: 1px solid #e5e7eb;
  font-size: 0.95rem;
}

.order-card__line:last-child {
  border-bottom: none;
}

.order-card__line-total {
  margin-left: auto;
  font-weight: 600;
  color: #b91c1c;
}

.order-card__total--grand {
  margin-top: 0.75rem;
  padding-top: 0.75rem;
  border-top: 2px solid #e5e7eb;
  font-weight: 700;
  font-size: 1.05rem;
}
</style>
