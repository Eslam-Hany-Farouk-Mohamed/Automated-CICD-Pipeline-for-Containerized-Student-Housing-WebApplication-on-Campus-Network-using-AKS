<template>
  <div class="auth-page">
    <div class="auth-card">
      <h1>{{ $t('auth.login.title') }}</h1>
      
      <form @submit.prevent="handleLogin" class="auth-form">
        <div class="form-group">
          <label class="form-label">{{ $t('auth.login.email') }}</label>
          <input 
            type="email" 
            v-model="form.email" 
            class="form-input"
            required
          />
        </div>

        <div class="form-group">
          <label class="form-label">{{ $t('auth.login.password') }}</label>
          <input 
            type="password" 
            v-model="form.password" 
            class="form-input"
            required
          />
        </div>

        <div v-if="authStore.error" class="error-message">
          {{ authStore.error }}
        </div>

        <button 
          type="submit" 
          class="btn btn-primary btn-full"
          :disabled="authStore.loading"
        >
          <span v-if="authStore.loading" class="loader"></span>
          <span v-else>{{ $t('auth.login.submit') }}</span>
        </button>
      </form>

      <p class="auth-footer">
        {{ $t('auth.login.noAccount') }}
        <router-link to="/register">{{ $t('auth.login.register') }}</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: '',
})

const handleLogin = async () => {
  try {
    await authStore.login(form)
    const redirect = route.query.redirect || '/'
    router.push(redirect)
  } catch (error) {
    console.error('Login failed:', error)
  }
}
</script>

<style scoped>
.auth-page {
  min-height: calc(100vh - 80px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--spacing-xl);
  background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
}

.auth-card {
  width: 100%;
  max-width: 420px;
  background: var(--bg-primary);
  padding: var(--spacing-xl);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-xl);
}

.auth-card h1 {
  text-align: center;
  margin-bottom: var(--spacing-xl);
  color: var(--text-primary);
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-md);
}

.btn-full {
  width: 100%;
  padding: var(--spacing-md);
  font-size: 1rem;
}

.error-message {
  padding: var(--spacing-sm) var(--spacing-md);
  background: rgba(239, 68, 68, 0.1);
  color: var(--danger);
  border-radius: var(--radius-md);
  font-size: 0.9rem;
}

.auth-footer {
  text-align: center;
  margin-top: var(--spacing-lg);
  color: var(--text-secondary);
}

.auth-footer a {
  color: var(--primary);
  font-weight: 500;
}
</style>
