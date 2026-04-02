<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const { isAuthenticated, isAdmin } = storeToRefs(authStore)
</script>

<template>
  <section
    class="mx-auto grid max-w-6xl gap-10 px-4 py-14 md:grid-cols-2 md:items-center"
  >
    <!-- Texto -->
    <div class="space-y-5">
      <span
        class="inline-flex rounded-full border border-yellow-200 bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-900"
      >
        Pizza artesanal en minutos
      </span>

      <h1
        class="text-4xl font-bold leading-tight tracking-tight text-gray-900 md:text-5xl"
      >
        Pide tus pizzas favoritas con una experiencia moderna y rápida
      </h1>

      <p class="text-lg leading-relaxed text-gray-600">
        Explora nuestro menú, agrega pizzas al carrito y recibe tus pedidos
        con una experiencia fluida y profesional.
      </p>

      <!-- CTA dinámico -->
      <div class="flex flex-wrap gap-3">
        <router-link
          :to="{ name: 'menu' }"
          class="rounded-xl bg-red-600 px-5 py-3 font-semibold text-white shadow-sm transition hover:bg-red-700"
        >
          Ver menú
        </router-link>

        <!-- Invitado -->
        <template v-if="!isAuthenticated">
          <router-link
            :to="{ name: 'register' }"
            class="rounded-xl border-2 border-gray-300 bg-white px-5 py-3 font-semibold text-gray-800 transition hover:bg-gray-50"
          >
            Crear cuenta
          </router-link>

          <router-link
            :to="{ name: 'login' }"
            class="rounded-xl border-2 border-gray-300 bg-white px-5 py-3 font-semibold text-gray-800 transition hover:bg-gray-50"
          >
            Iniciar sesión
          </router-link>
        </template>

        <!-- Usuario -->
        <router-link
          v-else-if="!isAdmin"
          :to="{ name: 'my-orders' }"
          class="rounded-xl border-2 border-gray-300 bg-white px-5 py-3 font-semibold text-gray-800 transition hover:bg-gray-50"
        >
          Mis pedidos
        </router-link>

        <!-- Admin -->
        <router-link
          v-else
          :to="{ name: 'admin' }"
          class="rounded-xl border-2 border-gray-300 bg-white px-5 py-3 font-semibold text-gray-800 transition hover:bg-gray-50"
        >
          Panel admin
        </router-link>
      </div>
    </div>

    <!-- Imagen -->
    <div
      class="overflow-hidden rounded-3xl border border-gray-200 bg-white p-4 shadow-lg"
    >
      <img
        class="h-80 w-full rounded-2xl object-cover"
        src="https://images.unsplash.com/photo-1541745537411-b8046dc6d66c?q=80&w=1200&auto=format&fit=crop"
        alt="Pizza artesanal"
        loading="lazy"
      />
    </div>
  </section>
</template>