<template>
  <Layout role="student">
    <div class="chat-page">
      <v-container fluid class="pa-0">
      <v-row no-gutters style="height: calc(100vh - 64px);">
        <!-- Список чатов -->
        <v-col cols="12" md="4" class="border-right">
          <v-card flat height="100%">
            <v-card-title class="d-flex align-center">
              <v-icon class="mr-2">mdi-chat</v-icon>
              Чаты
              <v-spacer></v-spacer>
              <v-btn icon @click="showNewChatDialog = true">
                <v-icon>mdi-plus</v-icon>
              </v-btn>
            </v-card-title>
            
            <v-divider></v-divider>
            
            <v-list dense>
              <v-list-item
                v-for="chat in chats"
                :key="chat.id"
                :class="{ 'active-chat': selectedChat?.id === chat.id }"
                @click="selectChat(chat)"
              >
                <v-list-item-avatar>
                  <v-img :src="chat.avatar" v-if="chat.avatar"></v-img>
                  <v-icon v-else>mdi-account</v-icon>
                </v-list-item-avatar>
                
                <v-list-item-content>
                  <v-list-item-title>{{ chat.name }}</v-list-item-title>
                  <v-list-item-subtitle>{{ chat.lastMessage }}</v-list-item-subtitle>
                </v-list-item-content>
                
                <v-list-item-action>
                  <v-chip
                    v-if="chat.unreadCount > 0"
                    color="primary"
                    small
                    text-color="white"
                  >
                    {{ chat.unreadCount }}
                  </v-chip>
                  <span class="caption text-grey">{{ chat.lastMessageTime }}</span>
                </v-list-item-action>
              </v-list-item>
            </v-list>
          </v-card>
        </v-col>
        
        <!-- Область сообщений -->
        <v-col cols="12" md="8" class="d-flex flex-column">
          <v-card flat height="100%">
            <!-- Заголовок чата -->
            <v-card-title v-if="selectedChat" class="d-flex align-center">
              <v-avatar size="32" class="mr-3">
                <v-img :src="selectedChat.avatar" v-if="selectedChat.avatar"></v-img>
                <v-icon v-else>mdi-account</v-icon>
              </v-avatar>
              {{ selectedChat.name }}
              <v-spacer></v-spacer>
              <v-btn icon>
                <v-icon>mdi-phone</v-icon>
              </v-btn>
              <v-btn icon>
                <v-icon>mdi-video</v-icon>
              </v-btn>
              <v-btn icon>
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </v-card-title>
            
            <v-divider v-if="selectedChat"></v-divider>
            
            <!-- Сообщения -->
            <div
              v-if="selectedChat"
              class="messages-container"
              ref="messagesContainer"
            >
              <div
                v-for="message in selectedChat.messages"
                :key="message.id"
                class="message-wrapper"
                :class="{ 'message-own': message.isOwn }"
              >
                <v-card
                  class="message"
                  :class="{ 'message-own': message.isOwn }"
                  max-width="70%"
                >
                  <v-card-text class="py-2">
                    <div class="message-content">{{ message.text }}</div>
                    <div class="message-time caption text-grey">
                      {{ message.time }}
                    </div>
                  </v-card-text>
                </v-card>
              </div>
            </div>
            
            <!-- Пустое состояние -->
            <div v-else class="d-flex align-center justify-center" style="height: 100%;">
              <div class="text-center">
                <v-icon size="64" color="grey">mdi-chat-outline</v-icon>
                <h3 class="text-h6 mt-4">Выберите чат для начала общения</h3>
              </div>
            </div>
            
            <!-- Поле ввода -->
            <v-card-actions v-if="selectedChat" class="message-input">
              <v-text-field
                v-model="newMessage"
                placeholder="Введите сообщение..."
                outlined
                dense
                hide-details
                @keyup.enter="sendMessage"
              >
                <template v-slot:append>
                  <v-btn icon @click="sendMessage" :disabled="!newMessage.trim()">
                    <v-icon>mdi-send</v-icon>
                  </v-btn>
                </template>
              </v-text-field>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
      </v-container>
      
      <!-- Диалог нового чата -->
      <v-dialog v-model="showNewChatDialog" max-width="400">
        <v-card>
          <v-card-title>Новый чат</v-card-title>
          <v-card-text>
            <v-text-field
              v-model="newChatName"
              label="Имя чата"
              outlined
              dense
            ></v-text-field>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn text @click="showNewChatDialog = false">Отмена</v-btn>
            <v-btn color="primary" @click="createNewChat">Создать</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
  </Layout>
</template>

<script>
import Layout from '../Layout.vue'

export default {
  name: 'ChatIndex',
  components: {
    App
  },
  data() {
    return {
      selectedChat: null,
      newMessage: '',
      showNewChatDialog: false,
      newChatName: '',
      chats: [
        {
          id: 1,
          name: 'Иванов И.И.',
          lastMessage: 'Добро пожаловать на курс!',
          lastMessageTime: '10:30',
          unreadCount: 2,
          avatar: null,
          messages: [
            {
              id: 1,
              text: 'Добро пожаловать на курс!',
              time: '10:30',
              isOwn: false
            },
            {
              id: 2,
              text: 'Спасибо! Рад быть здесь.',
              time: '10:32',
              isOwn: true
            },
            {
              id: 3,
              text: 'Если у вас есть вопросы, не стесняйтесь спрашивать.',
              time: '10:35',
              isOwn: false
            }
          ]
        },
        {
          id: 2,
          name: 'Петров П.П.',
          lastMessage: 'Когда будет следующая лекция?',
          lastMessageTime: '09:15',
          unreadCount: 0,
          avatar: null,
          messages: [
            {
              id: 1,
              text: 'Когда будет следующая лекция?',
              time: '09:15',
              isOwn: false
            }
          ]
        }
      ]
    }
  },
  methods: {
    selectChat(chat) {
      this.selectedChat = chat
      this.$nextTick(() => {
        this.scrollToBottom()
      })
    },
    
    sendMessage() {
      if (!this.newMessage.trim() || !this.selectedChat) return
      
      const message = {
        id: Date.now(),
        text: this.newMessage,
        time: new Date().toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' }),
        isOwn: true
      }
      
      this.selectedChat.messages.push(message)
      this.selectedChat.lastMessage = this.newMessage
      this.selectedChat.lastMessageTime = message.time
      this.newMessage = ''
      
      this.$nextTick(() => {
        this.scrollToBottom()
      })
    },
    
    scrollToBottom() {
      if (this.$refs.messagesContainer) {
        this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight
      }
    },
    
    createNewChat() {
      if (!this.newChatName.trim()) return
      
      const newChat = {
        id: Date.now(),
        name: this.newChatName,
        lastMessage: '',
        lastMessageTime: '',
        unreadCount: 0,
        avatar: null,
        messages: []
      }
      
      this.chats.unshift(newChat)
      this.newChatName = ''
      this.showNewChatDialog = false
      this.selectChat(newChat)
    }
  }
}
</script>

<style scoped>
.border-right {
  border-right: 1px solid #e0e0e0;
}

.messages-container {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  max-height: calc(100vh - 200px);
}

.message-wrapper {
  margin-bottom: 16px;
  display: flex;
}

.message-wrapper.message-own {
  justify-content: flex-end;
}

.message {
  background-color: #f5f5f5;
}

.message.message-own {
  background-color: #e3f2fd;
}

.message-content {
  margin-bottom: 4px;
}

.message-time {
  text-align: right;
}

.message-input {
  border-top: 1px solid #e0e0e0;
  padding: 16px;
}

.active-chat {
  background-color: #f5f5f5;
}
</style>
