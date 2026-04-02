<template>
  <header class="app-header">
    <nav class="app-header__nav">
      <div class="app-header__brand">
        <router-link to="/" class="app-header__logo">
          🍕 Pizza App
        </router-link>
      </div>

      <div class="app-header__menu">
        <router-link to="/" class="app-header__link">
          🏠 Inicio
        </router-link>

        <router-link to="/menu" class="app-header__link">
          🍕 Menú
        </router-link>

        <router-link
          v-if="auth.isAuthenticated"
          to="/my-orders"
          class="app-header__link"
        >
          📦 Mis Pedidos
        </router-link>

        <router-link
          v-if="auth.isAdmin"
          to="/admin"
          class="app-header__link"
        >
          ⚙️ Admin
        </router-link>
      </div>

      <div class="app-header__auth">
        <template v-if="auth.isAuthenticated">
          <span class="app-header__user">
            👤 {{ auth.user?.name }}
          </span>
          <button @click="logout" class="app-header__logout">
            🚪 Salir
          </button>
        </template>
        <template v-else>
          <router-link to="/login" class="app-header__link">
            🔐 Iniciar Sesión
          </router-link>
          <router-link to="/register" class="app-header__link">
            📝 Registrarse
          </router-link>
        </template>
      </div>
    </nav>
  </header>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

async function logout() {
  await auth.logout()
  router.push('/login')
}
</script>

<style scoped>
.app-header {
  background-color: var(--color-primary-pastel);
  border-bottom: 2px solid var(--color-primary-pastel-dark);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.app-header__nav {
  max-width: 1200px;
  margin: 0 auto;
  padding: var(--space-md) var(--space-lg);
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: var(--space-lg);
}

.app-header__brand {
  flex-shrink: 0;
}

.app-header__logo {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  text-decoration: none;
  transition: color 0.2s;
}

.app-header__logo:hover {
  color: var(--color-primary-pastel-darker);
}

.app-header__menu {
  display: flex;
  gap: var(--space-lg);
  align-items: center;
}

.app-header__link {
  color: #374151;
  text-decoration: none;
  font-weight: 500;
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--border-radius);
  transition: all 0.2s;
}

.app-header__link:hover,
.app-header__link.router-link-active {
  background-color: var(--color-primary-pastel-dark);
  color: #1f2937;
}

.app-header__auth {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  flex-shrink: 0;
}

.app-header__user {
  color: #374151;
  font-weight: 500;
}

.app-header__logout {
  background-color: var(--color-rose-pastel);
  color: #7f1d1d;
  border: none;
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--border-radius);
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.app-header__logout:hover {
  background-color: var(--color-rose-pastel-dark);
  color: #991b1b;
}

/* Responsive */
@media (max-width: 768px) {
  .app-header__nav {
    flex-direction: column;
    gap: var(--space-md);
  }

  .app-header__menu {
    order: 2;
    flex-wrap: wrap;
    justify-content: center;
  }

  .app-header__auth {
    order: 3;
    flex-wrap: wrap;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .app-header__menu {
    flex-direction: column;
    gap: var(--space-sm);
  }

  .app-header__auth {
    flex-direction: column;
    gap: var(--space-sm);
  }
}
</style>