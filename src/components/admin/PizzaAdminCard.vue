<template>
  <article class="pizza-admin-card">
    <img :src="pizza.image" :alt="pizza.name" class="pizza-admin-card__image" />
    <div class="pizza-admin-card__content">
      <h3 class="pizza-admin-card__title">{{ pizza.name }}</h3>
      <p class="pizza-admin-card__description">{{ pizza.description }}</p>
      <p class="pizza-admin-card__price">Bs {{ Number(pizza.price).toFixed(2) }}</p>
      <div class="pizza-admin-card__actions">
        <button @click="emit('edit', pizza)" class="btn btn--edit btn--small">
          ✏️ Editar
        </button>
        <button @click="emit('delete', pizza.id)" class="btn btn--delete btn--small">
          🗑️ Eliminar
        </button>
      </div>
    </div>
  </article>
</template>

<script setup lang="ts">
import type { Pizza } from '@/types'
import '@/assets/styles/pizza-card.css'

const props = defineProps<{
  pizza: Pizza
}>()

const emit = defineEmits<{
  edit: [pizza: Pizza]
  delete: [id: number]
}>()
</script>

