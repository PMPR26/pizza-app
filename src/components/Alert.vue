<template>
  <div v-if="visible" class="alert" :class="`alert--${type}`" role="alert">
    <div class="alert__icon">
      {{ iconMap[type] }}
    </div>
    <div class="alert__content">
      <p class="alert__message">{{ message }}</p>
      <p v-if="details" class="alert__details">{{ details }}</p>
    </div>
    <button v-if="closable" class="alert__close" @click="close" aria-label="Cerrar notificación">
      ✕
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import '@/assets/styles/theme.css'

interface Props {
  message: string
  type?: 'success' | 'error' | 'warning' | 'info'
  details?: string
  closable?: boolean
  autoClose?: number
}

const props = withDefaults(defineProps<Props>(), {
  type: 'info',
  closable: true,
  autoClose: 5000
})

const emit = defineEmits<{
  close: []
}>()

const visible = ref(true)
let timeoutId: ReturnType<typeof setTimeout>

const iconMap = {
  success: '✓',
  error: '✕',
  warning: '⚠',
  info: 'ⓘ'
}

const isAutoCloseable = computed(() => props.autoClose > 0)

function close() {
  visible.value = false
  emit('close')
}

onMounted(() => {
  if (isAutoCloseable.value) {
    timeoutId = setTimeout(close, props.autoClose)
  }
})

onUnmounted(() => {
  if (timeoutId) {
    clearTimeout(timeoutId)
  }
})
</script>

