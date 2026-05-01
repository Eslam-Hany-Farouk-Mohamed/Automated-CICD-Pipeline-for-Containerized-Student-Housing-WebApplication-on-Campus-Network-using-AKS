<template>
  <div class="chats-page page">
    <div class="container">
      <h1>{{ $t('chat.title') }}</h1>

      <div class="chats-layout">
        <!-- Chats List -->
        <div class="chats-list">
          <div v-if="loading" class="loading">
            <div class="loader"></div>
          </div>

          <div v-else-if="chats.length === 0" class="no-chats">
            <p>{{ $t('chat.noChats') }}</p>
          </div>

          <div 
            v-else
            v-for="chat in chats" 
            :key="chat.id"
            class="chat-item"
            :class="{ active: selectedChat?.id === chat.id }"
            @click="selectChat(chat)"
          >
            <div class="chat-avatar">
              {{ getOtherUser(chat)?.name?.charAt(0) }}
            </div>
            <div class="chat-info">
              <p class="chat-name">{{ getOtherUser(chat)?.name }}</p>
              <p class="chat-preview">{{ chat.latest_message?.content || 'لا توجد رسائل' }}</p>
            </div>
            <span v-if="chat.unread_messages_count" class="unread-badge">
              {{ chat.unread_messages_count }}
            </span>
          </div>
        </div>

        <!-- Chat Messages -->
        <div class="chat-messages-area">
          <template v-if="selectedChat">
            <div class="chat-header">
              <div class="chat-avatar">
                {{ getOtherUser(selectedChat)?.name?.charAt(0) }}
              </div>
              <div>
                <p class="chat-name">{{ getOtherUser(selectedChat)?.name }}</p>
                <p class="chat-listing" v-if="selectedChat.listing">
                  {{ selectedChat.listing.title }}
                </p>
              </div>
            </div>

            <div class="messages-container" ref="messagesRef">
              <div 
                v-for="msg in selectedChat.messages" 
                :key="msg.id"
                class="message"
                :class="{ mine: msg.sender_id === authStore.user?.id }"
              >
                <div class="message-content">{{ msg.content }}</div>
                <span class="message-time">
                  {{ formatTime(msg.created_at) }}
                </span>
              </div>
            </div>

            <form class="message-input" @submit.prevent="sendMessage">
              <input 
                type="text" 
                v-model="newMessage" 
                :placeholder="$t('chat.placeholder')"
              />
              <button type="submit" class="btn btn-primary">
                {{ $t('chat.send') }}
              </button>
            </form>
          </template>

          <div v-else class="no-chat-selected">
            <p>اختر محادثة للبدء</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const authStore = useAuthStore()

const loading = ref(true)
const chats = ref([])
const selectedChat = ref(null)
const newMessage = ref('')
const messagesRef = ref(null)

const getOtherUser = (chat) => {
  return authStore.user?.id === chat.student_id ? chat.owner : chat.student
}

const formatTime = (date) => {
  return new Date(date).toLocaleTimeString('ar-EG', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const selectChat = async (chat) => {
  try {
    const response = await api.get(`/chats/${chat.id}`)
    selectedChat.value = response.data.chat
    await nextTick()
    scrollToBottom()
  } catch (error) {
    console.error('Failed to load chat:', error)
  }
}

const scrollToBottom = () => {
  if (messagesRef.value) {
    messagesRef.value.scrollTop = messagesRef.value.scrollHeight
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedChat.value) return

  try {
    const response = await api.post('/messages', {
      chat_id: selectedChat.value.id,
      content: newMessage.value
    })

    selectedChat.value.messages.push(response.data.data)
    newMessage.value = ''
    await nextTick()
    scrollToBottom()
  } catch (error) {
    console.error('Failed to send message:', error)
  }
}

const fetchChats = async () => {
  try {
    const response = await api.get('/chats')
    chats.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch chats:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchChats()
})
</script>

<style scoped>
.chats-page h1 {
  margin-bottom: var(--spacing-lg);
}

.chats-layout {
  display: grid;
  grid-template-columns: 350px 1fr;
  gap: var(--spacing-lg);
  height: calc(100vh - 200px);
  background: var(--bg-primary);
  border-radius: var(--radius-lg);
  overflow: hidden;
}

.chats-list {
  border-inline-end: 1px solid var(--border-color);
  overflow-y: auto;
}

.chat-item {
  display: flex;
  align-items: center;
  gap: var(--spacing-md);
  padding: var(--spacing-md);
  cursor: pointer;
  transition: background var(--transition-fast);
}

.chat-item:hover,
.chat-item.active {
  background: var(--gray-100);
}

.chat-avatar {
  width: 45px;
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  color: white;
  font-weight: 600;
  border-radius: var(--radius-full);
  flex-shrink: 0;
}

.chat-info {
  flex: 1;
  min-width: 0;
}

.chat-name {
  font-weight: 600;
  margin-bottom: 2px;
}

.chat-preview {
  color: var(--text-secondary);
  font-size: 0.85rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.unread-badge {
  background: var(--primary);
  color: white;
  font-size: 0.75rem;
  padding: 2px 8px;
  border-radius: var(--radius-full);
}

.chat-messages-area {
  display: flex;
  flex-direction: column;
}

.chat-header {
  display: flex;
  align-items: center;
  gap: var(--spacing-md);
  padding: var(--spacing-md);
  border-bottom: 1px solid var(--border-color);
}

.chat-listing {
  font-size: 0.85rem;
  color: var(--text-secondary);
}

.messages-container {
  flex: 1;
  overflow-y: auto;
  padding: var(--spacing-md);
  display: flex;
  flex-direction: column;
  gap: var(--spacing-sm);
}

.message {
  max-width: 70%;
  align-self: flex-start;
}

.message.mine {
  align-self: flex-end;
}

.message-content {
  padding: var(--spacing-sm) var(--spacing-md);
  border-radius: var(--radius-lg);
  background: var(--gray-100);
}

.message.mine .message-content {
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  color: white;
}

.message-time {
  display: block;
  font-size: 0.7rem;
  color: var(--text-secondary);
  margin-top: 2px;
  text-align: end;
}

.message-input {
  display: flex;
  gap: var(--spacing-sm);
  padding: var(--spacing-md);
  border-top: 1px solid var(--border-color);
}

.message-input input {
  flex: 1;
  padding: var(--spacing-sm) var(--spacing-md);
  border: 1px solid var(--border-color);
  border-radius: var(--radius-md);
}

.no-chat-selected,
.no-chats,
.loading {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: var(--text-secondary);
}

@media (max-width: 768px) {
  .chats-layout {
    grid-template-columns: 1fr;
  }

  .chats-list {
    display: none;
  }
}
</style>
