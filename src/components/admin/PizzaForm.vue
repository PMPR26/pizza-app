<template>
  <form @submit.prevent="handleSubmit" class="pizza-form">

    <div class="pizza-form__field">
      <label class="pizza-form__label">Nombre</label>
      <input v-model="pizza.name" type="text" class="pizza-form__input" required />
    </div>

    <div class="pizza-form__field">
      <label class="pizza-form__label">Descripción</label>
      <textarea v-model="pizza.description" class="pizza-form__textarea" required></textarea>
    </div>

    <div class="pizza-form__field">
      <label class="pizza-form__label">Precio</label>
      <input v-model.number="pizza.price" type="number" step="0.01" class="pizza-form__input" required />
    </div>

    <div class="pizza-form__field">
      <label class="pizza-form__label">Ingredientes</label>
      <select v-model="pizza.ingredientIds" multiple class="pizza-form__select">
        <option v-for="ingredient in ingredients" :key="ingredient.id" :value="ingredient.id">
          {{ ingredient.name }}
        </option>
      </select>
    </div>

    <div class="pizza-form__field">
      <label class="pizza-form__label">Imagen</label>
      <div
        class="pizza-form__dropzone"
        :class="{ dragover: isDragOver }"
        @click="triggerFileInput"
        @dragover.prevent="onDragOver"
        @dragleave.prevent="onDragLeave"
        @drop.prevent="onDrop"
      >
        <div v-if="!pizza.imageFile && !existingImage" class="pizza-form__dropzone-content">
          <div class="pizza-form__dropzone-icon">📷</div>
          <p class="pizza-form__dropzone-text">
            Arrastra y suelta una imagen aquí, o haz clic para seleccionar
          </p>
        </div>
        <div v-else class="pizza-form__preview">
          <img :src="imagePreview || existingImage" alt="Preview" class="pizza-form__preview-img" />
          <p class="pizza-form__preview-name">{{ pizza.imageFile?.name || 'Imagen actual' }}</p>
        </div>
      </div>
      <input
        ref="fileInput"
        type="file"
        accept="image/*"
        @change="handleFileChange"
        class="pizza-form__file-input"
      />
    </div>

    <div class="pizza-form__actions">
      <button type="submit" class="pizza-form__button pizza-form__button--primary">
        {{ isEditing ? 'Actualizar' : 'Crear' }}
      </button>
      <button type="button" @click="clearForm" class="pizza-form__button pizza-form__button--secondary">
        Limpiar
      </button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import type { EditingPizza } from '@/types'
import '@/assets/styles/pizzaForm.css'

const props = defineProps<{
  ingredients: { id: number; name: string }[],
  editingPizza: EditingPizza | null
}>()

const emit = defineEmits<{
  (e: 'submit', formData: { name: string; description: string; price: number; ingredientIds: number[]; imageFile: File | null }): void
}>()

const pizza = ref({
  id: null as number | null,
  name: '',
  description: '',
  price: 0,
  ingredientIds: [] as number[],
  imageFile: null as File | null
})

const fileInput = ref<HTMLInputElement | null>(null)
const isDragOver = ref(false)
const imagePreview = ref<string>('')
const existingImage = ref<string>('')

const isEditing = computed(() => !!props.editingPizza)

watch(
  () => props.editingPizza,
  (newPizza) => {
    if (newPizza) {
      pizza.value = { ...newPizza, imageFile: null }
      existingImage.value = newPizza.image
      imagePreview.value = ''
    } else clearForm()
  },
  { immediate: true }
)

function triggerFileInput() {
  fileInput.value?.click()
}

function onDragOver() {
  isDragOver.value = true
}

function onDragLeave() {
  isDragOver.value = false
}

function onDrop(event: DragEvent) {
  isDragOver.value = false
  const files = event.dataTransfer?.files
  if (files && files[0]) {
    handleFile(files[0])
  }
}

function handleFileChange(e: Event) {
  const target = e.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) {
    handleFile(file)
  }
}

function handleFile(file: File) {
  if (file.type.startsWith('image/')) {
    pizza.value.imageFile = file
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  } else {
    alert('Por favor, selecciona un archivo de imagen válido.')
  }
}

function clearForm() {
  pizza.value = { id: null, name: '', description: '', price: 0, ingredientIds: [], imageFile: null }
  imagePreview.value = ''
  existingImage.value = ''
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

function handleSubmit() {
  emit('submit', {
    name: pizza.value.name,
    description: pizza.value.description,
    price: pizza.value.price,
    ingredientIds: pizza.value.ingredientIds,
    imageFile: pizza.value.imageFile
  })
}
</script>