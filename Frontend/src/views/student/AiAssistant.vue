<template>
  <div class="ai-page page">
    <div class="container">
      <div class="ai-layout">
        <!-- Chat Area -->
        <div class="chat-area">
          <div class="chat-header">
            <span class="ai-icon">🤖</span>
            <h1>{{ $t('ai.title') }}</h1>
          </div>

          <div class="chat-messages" ref="messagesContainer">
            <div 
              v-for="(msg, index) in messages" 
              :key="index" 
              class="message"
              :class="msg.type"
            >
              <div class="message-content">{{ msg.text }}</div>
            </div>
          </div>

          <form class="chat-input" @submit.prevent="sendMessage">
            <input 
              type="text" 
              v-model="input" 
              :placeholder="$t('ai.placeholder')"
              :disabled="loading"
            />
            <button type="submit" class="btn btn-primary" :disabled="loading || !input.trim()">
              {{ loading ? '...' : $t('chat.send') }}
            </button>
          </form>
        </div>

        <!-- Suggestions Sidebar -->
        <div class="suggestions-area" v-if="suggestedListings.length > 0">
          <h3>{{ $t('ai.suggestions') }}</h3>
          <div class="suggestions-list">
            <div 
              v-for="listing in suggestedListings" 
              :key="listing.id" 
              class="suggestion-card"
              @click="goToListing(listing.id)"
            >
              <h4>{{ listing.title }}</h4>
              <p>{{ formatPrice(listing.price) }}/شهر</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import api from '@/services/api'

const router = useRouter()
const { locale } = useI18n()

const input = ref('')
const loading = ref(false)
const messages = ref([
  { type: 'ai', text: 'مرحباً! أنا مساعدك الذكي. كيف يمكنني مساعدتك في البحث عن سكن؟' }
])
const suggestedListings = ref([])
const messagesContainer = ref(null)

const formatPrice = (price) => {
  return new Intl.NumberFormat(locale.value === 'ar' ? 'ar-EG' : 'en-EG', {
    style: 'currency',
    currency: 'EGP',
    minimumFractionDigits: 0
  }).format(price)
}

const goToListing = (id) => {
  router.push(`/listings/${id}`)
}

const scrollToBottom = async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const sendMessage = async () => {
  if (!input.value.trim() || loading.value) return

  const userMessage = input.value
  messages.value.push({ type: 'user', text: userMessage })
  input.value = ''
  loading.value = true
  scrollToBottom()

  try {
    const response = await api.post('/ai/chat', {
      message: userMessage,
      context: {}
    })

    messages.value.push({ type: 'ai', text: response.data.response })
    suggestedListings.value = response.data.recommended_listings || []
    scrollToBottom()
  } catch (error) {
    messages.value.push({ 
      type: 'ai', 
      text: 'عذراً، حدث خطأ. يرجى المحاولة مرة أخرى.' 
    })
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.ai-page {
  background: var(--bg-secondary);
}

.ai-layout {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: var(--spacing-lg);
  height: calc(100vh - 150px);
}

.chat-area {
  display: flex;
  flex-direction: column;
  background: var(--bg-primary);
  border-radius: var(--radius-lg);
  overflow: hidden;
}

.chat-header {
  display: flex;
  align-items: center;
  gap: var(--spacing-md);
  padding: var(--spacing-lg);
  border-bottom: 1px solid var(--border-color);
}

.ai-icon {
  font-size: 2rem;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: var(--spacing-lg);
  display: flex;
  flex-direction: column;
  gap: var(--spacing-md);
}

.message {
  max-width: 80%;
}

.message.user {
  align-self: flex-end;
}

.message.ai {
  align-self: flex-start;
}

.message-content {
  padding: var(--spacing-sm) var(--spacing-md);
  border-radius: var(--radius-lg);
  line-height: 1.6;
}

.message.user .message-content {
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  color: white;
  border-bottom-right-radius: 4px;
}

[dir="rtl"] .message.user .message-content {
  border-bottom-right-radius: var(--radius-lg);
  border-bottom-left-radius: 4px;
}

.message.ai .message-content {
  background: var(--gray-100);
  color: var(--text-primary);
  border-bottom-left-radius: 4px;
}

[dir="rtl"] .message.ai .message-content {
  border-bottom-left-radius: var(--radius-lg);
  border-bottom-right-radius: 4px;
}

.chat-input {
  display: flex;
  gap: var(--spacing-sm);
  padding: var(--spacing-md);
  border-top: 1px solid var(--border-color);
}

.chat-input input {
  flex: 1;
  padding: var(--spacing-sm) var(--spacing-md);
  border: 1px solid var(--border-color);
  border-radius: var(--radius-md);
}

.suggestions-area {
  background: var(--bg-primary);
  border-radius: var(--radius-lg);
  padding: var(--spacing-lg);
  overflow-y: auto;
}

.suggestions-area h3 {
  margin-bottom: var(--spacing-md);
}

.suggestions-list {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-sm);
}

.suggestion-card {
  padding: var(--spacing-md);
  background: var(--gray-50);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--transition-fast);
}

.suggestion-card:hover {
  background: var(--gray-100);
  transform: translateX(-4px);
}

[dir="rtl"] .suggestion-card:hover {
  transform: translateX(4px);
}

.suggestion-card h4 {
  font-size: 0.95rem;
  margin-bottom: var(--spacing-xs);
}

.suggestion-card p {
  color: var(--primary);
  font-weight: 600;
}

@media (max-width: 1024px) {
  .ai-layout {
    grid-template-columns: 1fr;
    height: auto;
  }

  .chat-area {
    min-height: 500px;
  }

  .suggestions-area {
    order: -1;
  }
}
</style>
