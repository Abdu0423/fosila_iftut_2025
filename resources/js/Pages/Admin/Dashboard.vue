<template>
  <Layout role="admin">
    <v-container fluid>
      <!-- Заголовок -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold mb-2">Панель администратора</h1>
              <p class="text-body-1 text-medium-emphasis">Управление системой дистанционного обучения</p>
            </div>
            <v-btn
              color="primary"
              size="large"
              prepend-icon="mdi-plus"
              @click="navigateTo('/admin/users/create')"
            >
              Добавить пользователя
            </v-btn>
          </div>
        </v-col>
      </v-row>

      <!-- Статистика -->
      <v-row class="mb-6">
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="primary" class="mb-4">mdi-account-group</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.users }}</div>
              <div class="text-body-2 text-medium-emphasis">Пользователей</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="success" class="mb-4">mdi-book-open-variant</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.courses }}</div>
              <div class="text-body-2 text-medium-emphasis">Курсов</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="info" class="mb-4">mdi-teach</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.lessons }}</div>
              <div class="text-body-2 text-medium-emphasis">Уроков</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="warning" class="mb-4">mdi-chart-line</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.activeUsers }}</div>
              <div class="text-body-2 text-medium-emphasis">Активных сегодня</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Основной контент -->
      <v-row>
        <!-- Последние действия -->
        <v-col cols="12" lg="8">
          <v-card class="mb-6">
            <v-card-title class="text-h6">
              <v-icon start>mdi-clock</v-icon>
              Последние действия
            </v-card-title>
            <v-card-text>
              <v-list>
                <v-list-item
                  v-for="action in recentActions"
                  :key="action.id"
                  :prepend-avatar="action.user.avatar"
                >
                  <v-list-item-title>{{ action.description }}</v-list-item-title>
                  <v-list-item-subtitle>
                    {{ action.user.name }} • {{ formatDate(action.created_at) }}
                  </v-list-item-subtitle>
                  <template v-slot:append>
                    <v-chip
                      :color="getActionColor(action.type)"
                      size="small"
                      variant="tonal"
                    >
                      {{ getActionText(action.type) }}
                    </v-chip>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- Системные уведомления -->
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-alert</v-icon>
              Системные уведомления
            </v-card-title>
            <v-card-text>
              <v-list>
                <v-list-item
                  v-for="notification in systemNotifications"
                  :key="notification.id"
                >
                  <v-list-item-title>{{ notification.title }}</v-list-item-title>
                  <v-list-item-subtitle>
                    {{ notification.message }} • {{ formatDate(notification.created_at) }}
                  </v-list-item-subtitle>
                  <template v-slot:append>
                    <v-chip
                      :color="getNotificationColor(notification.level)"
                      size="small"
                      variant="tonal"
                    >
                      {{ getNotificationText(notification.level) }}
                    </v-chip>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Боковая панель -->
        <v-col cols="12" lg="4">
          <!-- Быстрые действия -->
          <v-card class="mb-6">
            <v-card-title class="text-h6">
              <v-icon start>mdi-lightning-bolt</v-icon>
              Быстрые действия
            </v-card-title>
            <v-card-text>
              <v-list>
                <v-list-item
                  v-for="action in quickActions"
                  :key="action.title"
                  @click="navigateTo(action.route)"
                  :prepend-icon="action.icon"
                >
                  <v-list-item-title>{{ action.title }}</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- Статистика по ролям -->
          <v-card class="mb-6">
            <v-card-title class="text-h6">
              <v-icon start>mdi-chart-pie</v-icon>
              Пользователи по ролям
            </v-card-title>
            <v-card-text>
              <div v-for="role in roleStats" :key="role.name" class="mb-4">
                <div class="d-flex justify-space-between align-center mb-2">
                  <span class="text-body-2">{{ role.name }}</span>
                  <span class="text-body-2 font-weight-medium">{{ role.count }}</span>
                </div>
                <v-progress-linear
                  :model-value="(role.count / stats.users) * 100"
                  :color="role.color"
                  height="8"
                  rounded
                ></v-progress-linear>
              </div>
            </v-card-text>
          </v-card>

          <!-- Системная информация -->
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-information</v-icon>
              Системная информация
            </v-card-title>
            <v-card-text>
              <div class="mb-3">
                <div class="text-body-2 text-medium-emphasis">Версия системы</div>
                <div class="text-body-1 font-weight-medium">1.0.0</div>
              </div>
              <div class="mb-3">
                <div class="text-body-2 text-medium-emphasis">Последнее обновление</div>
                <div class="text-body-1 font-weight-medium">{{ formatDate(systemInfo.lastUpdate) }}</div>
              </div>
              <div class="mb-3">
                <div class="text-body-2 text-medium-emphasis">Статус системы</div>
                <v-chip color="success" size="small" variant="tonal">Работает</v-chip>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Layout from '../Layout.vue'

// Статистика
const stats = ref({
  users: 156,
  courses: 12,
  lessons: 89,
  activeUsers: 23
})

// Последние действия
const recentActions = ref([
  {
    id: 1,
    description: 'Создан новый курс "Программирование на Python"',
    user: { name: 'Иванов И.И.', avatar: 'https://ui-avatars.com/api/?name=Иван+Иванов' },
    type: 'create',
    created_at: '2024-01-19 14:30'
  },
  {
    id: 2,
    description: 'Добавлен новый пользователь Петрова А.А.',
    user: { name: 'Администратор', avatar: 'https://ui-avatars.com/api/?name=Admin' },
    type: 'user',
    created_at: '2024-01-19 13:15'
  },
  {
    id: 3,
    description: 'Обновлен курс "Веб-разработка"',
    user: { name: 'Петров П.П.', avatar: 'https://ui-avatars.com/api/?name=Петр+Петров' },
    type: 'update',
    created_at: '2024-01-19 12:45'
  }
])

// Системные уведомления
const systemNotifications = ref([
  {
    id: 1,
    title: 'Резервное копирование',
    message: 'Автоматическое резервное копирование выполнено успешно',
    level: 'info',
    created_at: '2024-01-19 02:00'
  },
  {
    id: 2,
    title: 'Обновление системы',
    message: 'Доступно обновление системы до версии 1.1.0',
    level: 'warning',
    created_at: '2024-01-18 18:30'
  },
  {
    id: 3,
    title: 'Высокая нагрузка',
    message: 'Обнаружена высокая нагрузка на сервер',
    level: 'error',
    created_at: '2024-01-18 15:20'
  }
])

// Быстрые действия
const quickActions = ref([
  {
    title: 'Добавить пользователя',
    icon: 'mdi-account-plus',
    route: '/admin/users/create'
  },
  {
    title: 'Создать курс',
    icon: 'mdi-book-plus',
    route: '/admin/courses/create'
  },
  {
    title: 'Просмотр отчетов',
    icon: 'mdi-chart-bar',
    route: '/admin/reports'
  },
  {
    title: 'Настройки системы',
    icon: 'mdi-cog',
    route: '/admin/settings'
  }
])

// Статистика по ролям
const roleStats = ref([
  { name: 'Администраторы', count: 3, color: 'error' },
  { name: 'Учителя', count: 12, color: 'warning' },
  { name: 'Студенты', count: 141, color: 'primary' }
])

// Системная информация
const systemInfo = ref({
  lastUpdate: '2024-01-19 14:30'
})

// Методы
const navigateTo = (route) => {
  router.visit(route)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ru-RU')
}

const getActionColor = (type) => {
  const colors = {
    create: 'success',
    update: 'info',
    delete: 'error',
    user: 'primary'
  }
  return colors[type] || 'grey'
}

const getActionText = (type) => {
  const texts = {
    create: 'Создание',
    update: 'Обновление',
    delete: 'Удаление',
    user: 'Пользователь'
  }
  return texts[type] || type
}

const getNotificationColor = (level) => {
  const colors = {
    info: 'info',
    warning: 'warning',
    error: 'error',
    success: 'success'
  }
  return colors[level] || 'grey'
}

const getNotificationText = (level) => {
  const texts = {
    info: 'Информация',
    warning: 'Предупреждение',
    error: 'Ошибка',
    success: 'Успех'
  }
  return texts[level] || level
}

onMounted(() => {
  console.log('Страница администратора загружена')
})
</script>

<style scoped>
.v-card {
  border-radius: 12px;
}
</style>
