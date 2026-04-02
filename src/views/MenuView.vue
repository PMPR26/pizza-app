<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import PizzaCard from '@/components/PizzaCard.vue'
import { useShopStore } from '@/stores/shop'
import { useAuthStore } from '@/stores/auth'
import type { Pizza } from '@/types'

const shop = useShopStore()
const auth = useAuthStore()
const router = useRouter()
const message = ref('')
const error = ref('')

onMounted(() => {
  shop.fetchPizzas()
})

function addToCart(pizza: Pizza) {
  shop.addToCart(pizza)
  message.value = `${pizza.name} agregada al carrito`
  setTimeout(() => {
    message.value = ''
  }, 1800)
}

async function checkout() {
  error.value = ''
  if (!auth.isAuthenticated) {
    router.push('/login')
    return
  }
  try {
    await shop.placeOrder()
    message.value = 'Pedido creado correctamente'
  } catch (err: any) {
    error.value = err?.response?.data?.message ?? 'No se pudo procesar el pedido'
  }
}
</script>

<template>
  <section class="mx-auto grid max-w-6xl gap-8 px-4 py-10 lg:grid-cols-[1fr_340px]">
    <div>
      <h1 class="mb-2 text-3xl font-bold tracking-tight text-gray-900 md:text-4xl">Nuestro menú</h1>
      <p class="mb-6 text-gray-600">Elige tu pizza favorita y agrégala al carrito.</p>

      <p
        v-if="message"
        class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
      >
        {{ message }}
      </p>
      <p
        v-if="error"
        class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
      >
        {{ error }}
      </p>

      <div v-if="shop.loading" class="text-gray-600">Cargando pizzas...</div>
      <div v-else class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
        <PizzaCard v-for="pizza in shop.pizzas" :key="pizza.id" :pizza="pizza" @add="addToCart" />
      </div>
    </div>

    <aside class="lg:sticky lg:top-6 lg:self-start">
      <div class="cart-panel">
        <h2 class="cart-panel__title">Carrito</h2>

        <p v-if="!shop.cart.length" class="cart-panel__empty">
          Aún no hay pizzas. Agrega alguna desde el menú.
        </p>

        <ul v-else class="space-y-3">
          <li
            v-for="item in shop.cart"
            :key="item.pizza.id"
            class="cart-panel__item"
          >
            <div class="cart-panel__item-header">
              <span class="cart-panel__item-name">{{ item.pizza.name }}</span>
              <button
                type="button"
                class="cart-panel__remove-btn"
                @click="shop.removeFromCart(item.pizza.id)"
              >
                Quitar
              </button>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-2">
              <div class="cart-panel__quantity-control">
                <button
                  type="button"
                  class="cart-panel__quantity-btn"
                  aria-label="Menos"
                  @click="shop.decrementQuantity(item.pizza.id)"
                >
                  −
                </button>
                <span class="cart-panel__quantity-number">{{ item.quantity }}</span>
                <button
                  type="button"
                  class="cart-panel__quantity-btn"
                  aria-label="Más"
                  @click="shop.incrementQuantity(item.pizza.id)"
                >
                  +
                </button>
              </div>
              <span class="cart-panel__item-total">
                {{ (item.pizza.price * item.quantity).toFixed(2) }} Bs
              </span>
            </div>
          </li>
        </ul>

        <div v-if="shop.cart.length" class="cart-panel__total mt-4 flex items-center justify-between">
          <span>Total ({{ shop.cartCount }} {{ shop.cartCount === 1 ? 'artículo' : 'artículos' }})</span>
          <span class="cart-panel__total-value">{{ shop.cartTotal.toFixed(2) }} Bs</span>
        </div>

        <button
          v-if="shop.cart.length"
          type="button"
          class="cart-panel__checkout-btn mt-4"
          :disabled="shop.loading"
          @click="checkout"
        >
          {{ auth.isAuthenticated ? 'Confirmar pedido' : 'Iniciar sesión para pedir' }}
        </button>
      </div>
    </aside>
  </section>
</template>
