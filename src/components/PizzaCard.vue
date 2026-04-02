<template>
  <article class="pizza-card">
    <img :src="pizza.image" :alt="pizza.name" class="pizza-card__image" />
    <div class="pizza-card__content">
      <h3 class="pizza-card__title">{{ pizza.name }}</h3>
      <p class="pizza-card__description">{{ pizza.description }}</p>
      <p class="pizza-card__price">Bs {{ Number(pizza.price).toFixed(2) }}</p>
      <button @click="emit('add', pizza)" class="pizza-card__button">
        Agregar al carrito
      </button>
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
  add: [pizza: Pizza]
}>()
</script>

