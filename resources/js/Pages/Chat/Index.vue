<template>
  <Layout>
    <v-container fluid>
      <!-- Заголовок -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold mb-2">Чаты</h1>
              <p class="text-body-1 text-medium-emphasis">Общайтесь с пользователями системы</p>
            </div>
            <v-btn
              color="primary"
              variant="elevated"
              @click="showNewChatDialog = true"
              prepend-icon="mdi-plus"
            >
              Новый чат
            </v-btn>
          </div>
        </v-col>
      </v-row>

      <!-- Список чатов -->
      <v-row>
        <v-col cols="12" md="4">
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-chat</v-icon>
              Мои чаты
            </v-card-title>
            <v-card-text class="pa-0">
              <v-list>
                <v-list-item
                  v-for="chat in chats"
                  :key="chat.id"
                  :to="`/chat/${chat.id}`"
                  :active="selectedChatId === chat.id"
                  @click="selectChat(chat.id)"
                  class="chat-item"
                >
                  <template v-slot:prepend>
                    <v-avatar
                      :color="chat.display_avatar ? 'transparent' : 'primary'"
                      size="48"
                    >
                      <v-img
                        v-if="chat.display_avatar"
                        :src="chat.display_avatar"
                        :alt="chat.display_name"
                      ></v-img>
                      <span v-else class="text-white font-weight-bold">
                        {{ getInitials(chat.display_name) }}
                      </span>
                    </v-avatar>
                  </template>

                  <v-list-item-title class="text-subtitle-1">
                    {{ chat.display_name }}
                  </v-list-item-title>
                  
                  <v-list-item-subtitle class="text-body-2">
                    <div v-if="chat.last_message" class="d-flex align-center">
                      <span class="text-truncate mr-2">
                        {{ chat.last_message.user.name }}: {{ chat.last_message.message }}
                      </span>
                      <span class="text-caption text-medium-emphasis">
                        {{ formatTime(chat.last_message.created_at) }}
                      </span>
                    </div>
                    <span v-else class="text-medium-emphasis">Нет сообщений</span>
                  </v-list-item-subtitle>

                  <template v-slot:append>
                    <v-chip
                      v-if="chat.unread_count > 0"
                      color="primary"
                      size="small"
                      class="ml-2"
                    >
                      {{ chat.unread_count }}
                    </v-chip>
                  </template>
                </v-list-item>

                <v-list-item v-if="chats.length === 0" class="text-center">
                  <v-list-item-title class="text-medium-emphasis">
                    У вас пока нет чатов
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Область чата -->
        <v-col cols="12" md="8">
          <v-card v-if="selectedChat" class="chat-container">
            <v-card-title class="d-flex align-center">
              <v-avatar
                :color="selectedChat.display_avatar ? 'transparent' : 'primary'"
                size="40"
                class="mr-3"
              >
                <v-img
                  v-if="selectedChat.display_avatar"
                  :src="selectedChat.display_avatar"
                  :alt="selectedChat.display_name"
                ></v-img>
                <span v-else class="text-white font-weight-bold">
                  {{ getInitials(selectedChat.display_name) }}
                </span>
              </v-avatar>
              <div>
                <div class="text-h6">{{ selectedChat.display_name }}</div>
                <div class="text-caption text-medium-emphasis">
                  {{ selectedChat.type === 'private' ? 'Приватный чат' : 'Групповой чат' }}
                </div>
              </div>
              <v-spacer></v-spacer>
              <v-btn
                icon="mdi-account-minus"
                variant="text"
                @click="leaveChat"
                color="error"
              >
                <v-icon>mdi-exit-to-app</v-icon>
                <v-tooltip activator="parent" location="bottom">
                  Покинуть чат
                </v-tooltip>
              </v-btn>
            </v-card-title>

            <!-- Сообщения -->
            <v-card-text class="chat-messages pa-0" ref="messagesContainer">
              <div v-if="messages.length === 0" class="text-center pa-8">
                <v-icon size="64" color="grey-lighten-1">mdi-chat-outline</v-icon>
                <div class="text-h6 text-medium-emphasis mt-4">Начните общение</div>
                <div class="text-body-2 text-medium-emphasis">
                  Отправьте первое сообщение в этом чате
                </div>
              </div>

              <div v-else class="messages-list">
                <div
                  v-for="message in messages"
                  :key="message.id"
                  class="message-item"
                  :class="{ 'message-own': message.is_from_current_user }"
                >
                  <div class="message-content">
                    <div class="message-header" v-if="!message.is_from_current_user">
                      <span class="message-author">{{ message.user.name }}</span>
                      <span class="message-time">{{ formatTime(message.created_at) }}</span>
                    </div>
                    <div class="message-text">{{ message.message }}</div>
                    <div class="message-status" v-if="message.is_from_current_user">
                      <v-icon
                        size="16"
                        :color="message.status === 'read' ? 'primary' : 'grey'"
                      >
                        {{ message.status === 'read' ? 'mdi-check-all' : 'mdi-check' }}
                      </v-icon>
                    </div>
                  </div>
                </div>
              </div>
            </v-card-text>

            <!-- Поле ввода сообщения -->
            <v-card-actions class="pa-4">
              <v-textarea
                v-model="newMessage"
                placeholder="Введите сообщение..."
                variant="outlined"
                density="compact"
                rows="1"
                auto-grow
                hide-details
                @keydown.enter.prevent="sendMessage"
                class="flex-grow-1 mr-3"
              ></v-textarea>
              <v-btn
                color="primary"
                variant="elevated"
                @click="sendMessage"
                :disabled="!newMessage.trim()"
                icon="mdi-send"
              >
                <v-icon>mdi-send</v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>

          <!-- Заглушка когда чат не выбран -->
          <v-card v-else class="chat-container">
            <v-card-text class="text-center pa-8">
              <v-icon size="64" color="grey-lighten-1">mdi-chat-outline</v-icon>
              <div class="text-h6 text-medium-emphasis mt-4">Выберите чат</div>
              <div class="text-body-2 text-medium-emphasis">
                Выберите чат из списка слева или создайте новый
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Диалог создания нового чата -->
      <v-dialog v-model="showNewChatDialog" max-width="500">
        <v-card>
          <v-card-title class="text-h6">
            <v-icon start>mdi-plus</v-icon>
            Новый чат
          </v-card-title>
          <v-card-text>
            <v-form @submit.prevent="createChat">
              <v-select
                v-model="newChatForm.user_id"
                :items="users"
                item-title="name"
                item-value="id"
                label="Выберите пользователя"
                variant="outlined"
                density="compact"
                required
              >
                <template v-slot:item="{ props, item }">
                  <v-list-item v-bind="props">
                    <v-list-item-title>{{ item.raw.name }}</v-list-item-title>
                    <v-list-item-subtitle>{{ item.raw.email }}</v-list-item-subtitle>
                  </v-list-item>
                </template>
              </v-select>
            </v-form>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="secondary"
              @click="showNewChatDialog = false"
            >
              Отмена
            </v-btn>
            <v-btn
              color="primary"
              @click="createChat"
              :disabled="!newChatForm.user_id"
            >
              Создать чат
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import Layout from '../Layout.vue'

// Props
const props = defineProps({
  chats: {
    type: Array,
    default: () => []
  },
  users: {
    type: Array,
    default: () => []
  }
})

// Состояние
const selectedChatId = ref(null)
const selectedChat = ref(null)
const messages = ref([])
const newMessage = ref('')
const showNewChatDialog = ref(false)
const messagesContainer = ref(null)

// Форма для нового чата
const newChatForm = useForm({
  user_id: null
})

// Вычисляемые свойства
const currentChat = computed(() => {
  return props.chats.find(chat => chat.id === selectedChatId.value)
})

// Методы
const selectChat = (chatId) => {
  selectedChatId.value = chatId
  selectedChat.value = props.chats.find(chat => chat.id === chatId)
  loadMessages()
}

const loadMessages = async () => {
  if (!selectedChatId.value) return

  try {
    const response = await fetch(`/chat/${selectedChatId.value}/messages`)
    const data = await response.json()
    messages.value = data.data || []
    
    // Прокручиваем к последнему сообщению
    await nextTick()
    scrollToBottom()
  } catch (error) {
    console.error('Ошибка при загрузке сообщений:', error)
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedChatId.value) return

  try {
    const response = await fetch(`/chat/${selectedChatId.value}/messages`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        message: newMessage.value.trim()
      })
    })

    const data = await response.json()
    
    if (data.success) {
      messages.value.push(data.message)
      newMessage.value = ''
      await nextTick()
      scrollToBottom()
    }
  } catch (error) {
    console.error('Ошибка при отправке сообщения:', error)
  }
}

const createChat = () => {
  if (!newChatForm.user_id) return

  newChatForm.post('/chat', {
    onSuccess: () => {
      showNewChatDialog.value = false
      newChatForm.reset()
    }
  })
}

const leaveChat = () => {
  if (!selectedChatId.value) return

  if (confirm('Вы уверены, что хотите покинуть этот чат?')) {
    router.delete(`/chat/${selectedChatId.value}/leave`)
  }
}

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const formatTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString('ru-RU', { 
    hour: '2-digit', 
    minute: '2-digit' 
  })
}

const getInitials = (name) => {
  if (!name) return '??'
  return name.split(' ').map(word => word.charAt(0)).join('').toUpperCase().slice(0, 2)
}

// Жизненный цикл
onMounted(() => {
  // Если есть чаты, выбираем первый
  if (props.chats.length > 0) {
    selectChat(props.chats[0].id)
  }
})
</script>

<style scoped>
.chat-container {
  height: 600px;
  display: flex;
  flex-direction: column;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  max-height: 400px;
}

.messages-list {
  padding: 16px;
}

.message-item {
  margin-bottom: 16px;
  display: flex;
}

.message-item.message-own {
  justify-content: flex-end;
}

.message-content {
  max-width: 70%;
  padding: 12px 16px;
  border-radius: 18px;
  background-color: rgb(var(--v-theme-surface-variant));
}

.message-item.message-own .message-content {
  background-color: rgb(var(--v-theme-primary));
  color: white;
}

.message-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 4px;
}

.message-author {
  font-weight: 600;
  font-size: 0.875rem;
}

.message-time {
  font-size: 0.75rem;
  opacity: 0.7;
}

.message-text {
  line-height: 1.4;
}

.message-status {
  display: flex;
  justify-content: flex-end;
  margin-top: 4px;
}

.chat-item {
  border-bottom: 1px solid rgb(var(--v-theme-outline-variant));
}

.chat-item:last-child {
  border-bottom: none;
}
</style>