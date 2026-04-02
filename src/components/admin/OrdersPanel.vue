<script setup lang="ts">
import { ref, computed } from 'vue'
import Alert from '@/components/Alert.vue'
import type { Order } from '@/types'
import { groupOrdersByCheckout, formatBs } from '@/utils/orderGroups'
import '@/assets/styles/orders.css'

const props = defineProps<{ orders: Order[] }>()

const search = ref('')
const notificationMessage = ref('')
const notificationType = ref<'success' | 'error' | 'warning' | 'info'>('success')
const showNotification = ref(false)

const allGroups = computed(() => groupOrdersByCheckout(props.orders ?? []))

const filteredGroups = computed(() => {
  const term = search.value.trim().toLowerCase()
  if (!term) return allGroups.value

  return allGroups.value.filter((group) => {
    const customerName = group.lines[0]?.user?.name ?? 'Anónimo'
    const customerMatch = customerName.toLowerCase().includes(term)

    const pizzaMatch = group.lines.some((line) =>
      (line.pizza?.name ?? '').toLowerCase().includes(term),
    )

    const idMatch = group.lines.some((line) => String(line.id).includes(term))

    return customerMatch || pizzaMatch || idMatch
  })
})

function formatDate(dt: string | Date) {
  const d = new Date(dt)
  if (isNaN(d.getTime())) return '-'
  return `${d.toLocaleDateString()} ${d.toLocaleTimeString()}`
}
</script>

<template>
  <section class="orders-panel">
    <transition name="fade">
      <Alert
        v-if="showNotification"
        :message="notificationMessage"
        :type="notificationType"
        :auto-close="4000"
        @close="showNotification = false"
        class="orders-panel__alert-fixed"
      />
    </transition>

    <div class="orders-panel__header">
      <div>
        <h2 class="orders-panel__title">📦 Historial de Pedidos</h2>
        <p class="orders-panel__subtitle">Revisa y administra los pedidos de tus clientes</p>
      </div>
    </div>

    <div class="orders-panel__search-container">
      <input
        v-model="search"
        type="text"
        placeholder="🔍 Buscar por cliente o pizza..."
        class="orders-panel__search"
      />
    </div>

    <div v-if="filteredGroups.length === 0" class="orders-panel__empty">
      <p>📭 No hay pedidos</p>
      <p class="orders-panel__empty-subtitle">Los pedidos aparecerán aquí cuando los clientes realicen órdenes</p>
    </div>

    <div v-else class="orders-panel__grid">
      <div
        v-for="group in filteredGroups"
        :key="group.key"
        class="order-card"
      >
        <div class="order-card__header">
          <div>
            <span class="order-card__id">
              Pedido #{{ group.lines[0]?.id ?? '—' }}
              <span v-if="group.lines.length > 1" class="order-card__id-suffix">
                ({{ group.lines.length }} ítems)
              </span>
            </span>
            <span class="order-card__date">{{ formatDate(group.orderedAt) }}</span>
          </div>
          <span class="order-card__total">{{ formatBs(group.grandTotal) }}</span>
        </div>

        <div class="order-card__divider"></div>

        <div class="order-card__info">
          <div class="order-card__field">
            <span class="order-card__label">👤 Cliente:</span>
            <span class="order-card__value">{{ group.lines[0]?.user?.name ?? 'Anónimo' }}</span>
          </div>

          <div
            v-for="line in group.lines"
            :key="line.id"
            class="order-card__field order-card__field--line"
          >
            <span class="order-card__label">🍕 {{ line.pizza?.name ?? '—' }}</span>
            <span class="order-card__value">
              {{ line.quantity }} × — {{ formatBs(line.total) }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.order-card__id-suffix {
  font-size: 0.85rem;
  font-weight: 500;
  color: #6b7280;
}

.order-card__field--line {
  margin-top: 0.35rem;
}
</style>
