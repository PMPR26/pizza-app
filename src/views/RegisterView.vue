<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const showPassword = ref(false)
const error = ref('')

async function onSubmit() {
  error.value = ''
  try {
    await auth.register(name.value, email.value, password.value)
    router.push('/menu')
  } catch (err: any) {
    error.value = err?.response?.data?.message ?? 'No se pudo completar el registro'
  }
}
</script>

<template>
  <section class="mx-auto max-w-md px-4 py-12">
    <div class="rounded-3xl border border-gray-200 bg-white p-8 shadow-[0_4px_6px_-1px_rgb(0_0_0/0.08)]">
      <h1 class="mb-1 text-2xl font-bold text-gray-900">Crear cuenta</h1>
      <p class="mb-6 text-sm text-gray-600">Regístrate para comenzar a pedir.</p>

      <form class="space-y-4" @submit.prevent="onSubmit">
        <input
          v-model="name"
          type="text"
          placeholder="Nombre"
          class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 outline-none ring-[#D32F2F] focus:ring-2"
        />
        <input
          v-model="email"
          type="email"
          placeholder="Correo"
          class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 outline-none ring-[#D32F2F] focus:ring-2"
        />
        <div class="relative">
          <input
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Contraseña"
            autocomplete="new-password"
            class="w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-4 pr-12 text-gray-900 outline-none ring-[#D32F2F] focus:ring-2"
          />
          <button
            type="button"
            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 transition hover:text-gray-800"
            :aria-pressed="showPassword"
            :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
            @click="showPassword = !showPassword"
          >
            <svg
              v-if="!showPassword"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="h-5 w-5"
              aria-hidden="true"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
              />
            </svg>
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="h-5 w-5"
              aria-hidden="true"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.182 4.182L4.5 5.5"
              />
            </svg>
          </button>
        </div>
        <button
          :disabled="auth.loading"
          class="w-full rounded-xl bg-[#D32F2F] px-4 py-3 font-semibold text-white shadow-sm hover:bg-[#B71C1C] disabled:opacity-50"
        >
          {{ auth.loading ? 'Creando...' : 'Crear cuenta' }}
        </button>
      </form>

      <p v-if="error" class="mt-4 text-sm text-red-600">{{ error }}</p>
    </div>
  </section>
</template>
