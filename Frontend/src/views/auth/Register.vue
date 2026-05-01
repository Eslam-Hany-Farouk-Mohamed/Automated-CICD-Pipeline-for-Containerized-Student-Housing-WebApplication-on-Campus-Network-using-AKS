<template>
  <div class="auth-page">
    <div class="auth-card">
      <h1>{{ $t('auth.register.title') }}</h1>
      
      <form @submit.prevent="handleRegister" class="auth-form">
        <div class="form-group">
          <label class="form-label">{{ $t('auth.register.name') }}</label>
          <input 
            type="text" 
            v-model="form.name" 
            class="form-input"
            :class="{ 'input-error': errors.name.length }"
            required
          />
          <div v-if="errors.name.length" class="field-error">
            {{ errors.name[0] }}
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">{{ $t('auth.register.email') }}</label>
          <input 
            type="email" 
            v-model="form.email" 
            class="form-input"
            :class="{ 'input-error': errors.email.length }"
            required
          />
          <div v-if="errors.email.length" class="field-error">
            {{ errors.email[0] }}
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">{{ $t('auth.register.phone') }}</label>
          <input 
            type="tel" 
            v-model="form.phone" 
            class="form-input"
            :class="{ 'input-error': errors.phone.length }"
          />
          <div v-if="errors.phone.length" class="field-error">
            {{ errors.phone[0] }}
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">{{ $t('auth.register.role') }}</label>
          <div class="role-selector">
            <label 
              v-for="role in roles" 
              :key="role.id"
              class="role-option"
              :class="{ selected: form.role_id === role.id }"
            >
              <input 
                type="radio" 
                v-model="form.role_id" 
                :value="role.id"
              />
              <span class="role-icon">{{ role.icon }}</span>
              <span class="role-name">{{ $t(`auth.register.roles.${role.name}`) }}</span>
            </label>
          </div>
          <div v-if="errors.role_id.length" class="field-error">
            {{ errors.role_id[0] }}
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">{{ $t('auth.register.password') }}</label>
          <input 
            type="password" 
            v-model="form.password" 
            class="form-input"
            :class="{ 'input-error': errors.password.length }"
            required
          />
          <div v-if="errors.password.length" class="field-error">
            {{ errors.password[0] }}
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">{{ $t('auth.register.confirmPassword') }}</label>
          <input 
            type="password" 
            v-model="form.password_confirmation" 
            class="form-input"
            :class="{ 'input-error': errors.password_confirmation.length }"
            required
          />
          <div v-if="errors.password_confirmation.length" class="field-error">
            {{ errors.password_confirmation[0] }}
          </div>
        </div>

        <button 
          type="submit" 
          class="btn btn-primary btn-full"
          :disabled="authStore.loading"
        >
          <span v-if="authStore.loading" class="loader"></span>
          <span v-else>{{ $t('auth.register.submit') }}</span>
        </button>
      </form>

      <p class="auth-footer">
        {{ $t('auth.register.hasAccount') }}
        <router-link to="/login">{{ $t('auth.register.login') }}</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const roles = [
  { id: 1, name: 'student', icon: '🎓' },
  { id: 2, name: 'owner', icon: '🏠' },
  { id: 3, name: 'broker', icon: '🤝' },
]

const form = reactive({
  name: '',
  email: '',
  phone: '',
  role_id: 1,
  password: '',
  password_confirmation: '',
})

const errors = reactive({
  name: [],
  email: [],
  phone: [],
  role_id: [],
  password: [],
  password_confirmation: [],
})

const parseErrors = (errorMessage) => {
  // Parse field-specific errors from the error message format
  // "email: The email has already been taken.\npassword: The password confirmation..."
  Object.keys(errors).forEach(field => {
    errors[field] = []
  })

  const lines = errorMessage.split('\n')
  lines.forEach(line => {
    const match = line.match(/^([a-z_]+):\s*(.+)$/)
    if (match) {
      const [, field, message] = match
      if (errors.hasOwnProperty(field)) {
        errors[field].push(message)
      }
    }
  })
}

const handleRegister = async () => {
  Object.keys(errors).forEach(field => {
    errors[field] = []
  })
  
  try {
    await authStore.register(form)
    router.push('/')
  } catch (error) {
    // Parse validation errors
    if (authStore.error) {
      parseErrors(authStore.error)
    }
    console.error('Registration failed:', error)
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
  max-width: 480px;
  background: var(--bg-primary);
  padding: var(--spacing-xl);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-xl);
}

.auth-card h1 {
  text-align: center;
  margin-bottom: var(--spacing-xl);
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-md);
}

.role-selector {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--spacing-sm);
}

.role-option {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: var(--spacing-md);
  border: 2px solid var(--border-color);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--transition-fast);
}

.role-option input {
  display: none;
}

.role-option.selected {
  border-color: var(--primary);
  background: rgba(99, 102, 241, 0.1);
}

.role-icon {
  font-size: 2rem;
  margin-bottom: var(--spacing-xs);
}

.role-name {
  font-size: 0.85rem;
  font-weight: 500;
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

.field-error {
  color: var(--danger);
  font-size: 0.85rem;
  margin-top: 0.25rem;
}

.input-error {
  border-color: var(--danger) !important;
  background-color: rgba(239, 68, 68, 0.05);
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
