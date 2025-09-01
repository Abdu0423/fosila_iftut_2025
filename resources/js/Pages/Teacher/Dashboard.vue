<template>
  <Layout role="teacher">
    <v-container fluid>
      <!-- Заголовок -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold mb-2">Добро пожаловать, учитель!</h1>
              <p class="text-body-1 text-medium-emphasis">Управляйте своими курсами и студентами</p>
            </div>
            <v-btn
              color="primary"
              size="large"
              prepend-icon="mdi-plus"
              @click="navigateTo('/teacher/lessons/create')"
            >
              Создать урок
            </v-btn>
          </div>
        </v-col>
      </v-row>

      <!-- Статистика -->
      <v-row class="mb-6">
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="primary" class="mb-4">mdi-book-open-variant</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.courses }}</div>
              <div class="text-body-2 text-medium-emphasis">Мои курсы</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="success" class="mb-4">mdi-teach</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.lessons }}</div>
              <div class="text-body-2 text-medium-emphasis">Мои уроки</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="info" class="mb-4">mdi-account-group</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.students }}</div>
              <div class="text-body-2 text-medium-emphasis">Мои студенты</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="warning" class="mb-4">mdi-star</v-icon>
              <div class="text-h4 font-weight-bold">{{ stats.averageGrade }}</div>
              <div class="text-body-2 text-medium-emphasis">Средний балл</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Основной контент -->
      <v-row>
        <!-- Ближайшие уроки -->
        <v-col cols="12" lg="8">
          <v-card class="mb-6">
            <v-card-title class="text-h6">
              <v-icon start>mdi-calendar-clock</v-icon>
              Ближайшие уроки
            </v-card-title>
            <v-card-text>
              <v-list>
                <v-list-item
                  v-for="lesson in upcomingLessons"
                  :key="lesson.id"
                  :prepend-avatar="lesson.course.avatar"
                >
                  <v-list-item-title>{{ lesson.title }}</v-list-item-title>
                  <v-list-item-subtitle>
                    {{ lesson.course.name }} • {{ formatDate(lesson.date) }} в {{ lesson.time }}
                  </v-list-item-subtitle>
                  <template v-slot:append>
                    <v-chip
                      :color="getStatusColor(lesson.status)"
                      size="small"
                      variant="tonal"
                    >
                      {{ getStatusText(lesson.status) }}
                    </v-chip>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- Последние оценки -->
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-star</v-icon>
              Последние оценки
            </v-card-title>
            <v-card-text>
              <v-list>
                <v-list-item
                  v-for="grade in recentGrades"
                  :key="grade.id"
                >
                  <v-list-item-title>{{ grade.student.name }}</v-list-item-title>
                  <v-list-item-subtitle>
                    {{ grade.lesson.title }} • {{ formatDate(grade.graded_at) }}
                  </v-list-item-subtitle>
                  <template v-slot:append>
                    <v-chip
                      :color="getGradeColor(grade.grade)"
                      size="small"
                      variant="tonal"
                    >
                      {{ grade.grade }}/{{ grade.max_grade }}
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

          <!-- Уведомления -->
          <v-card class="mb-6">
            <v-card-title class="text-h6">
              <v-icon start>mdi-bell</v-icon>
              Уведомления
            </v-card-title>
            <v-card-text>
              <v-list>
                <v-list-item
                  v-for="notification in notifications"
                  :key="notification.id"
                >
                  <v-list-item-title class="text-body-2">{{ notification.message }}</v-list-item-title>
                  <v-list-item-subtitle class="text-caption">{{ notification.time }}</v-list-item-subtitle>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- Прогресс курсов -->
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-chart-line</v-icon>
              Прогресс курсов
            </v-card-title>
            <v-card-text>
              <div v-for="course in courseProgress" :key="course.id" class="mb-4">
                <div class="d-flex justify-space-between align-center mb-2">
                  <span class="text-body-2">{{ course.name }}</span>
                  <span class="text-body-2 font-weight-medium">{{ course.progress }}%</span>
                </div>
                <v-progress-linear
                  :model-value="course.progress"
                  :color="getProgressColor(course.progress)"
                  height="8"
                  rounded
                ></v-progress-linear>
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
  courses: 5,
  lessons: 24,
  students: 87,
  averageGrade: 4.2
})

// Ближайшие уроки
const upcomingLessons = ref([
  {
    id: 1,
    title: 'Введение в Python',
    course: { name: 'Программирование', avatar: 'https://ui-avatars.com/api/?name=Python' },
    date: '2024-01-20',
    time: '10:00',
    status: 'scheduled'
  },
  {
    id: 2,
    title: 'Переменные и типы данных',
    course: { name: 'Программирование', avatar: 'https://ui-avatars.com/api/?name=Python' },
    date: '2024-01-22',
    time: '14:00',
    status: 'scheduled'
  },
  {
    id: 3,
    title: 'Основы HTML',
    course: { name: 'Веб-разработка', avatar: 'https://ui-avatars.com/api/?name=HTML' },
    date: '2024-01-23',
    time: '16:00',
    status: 'scheduled'
  }
])

// Последние оценки
const recentGrades = ref([
  {
    id: 1,
    student: { name: 'Иванов Иван' },
    lesson: { title: 'Введение в Python' },
    grade: 5,
    max_grade: 5,
    graded_at: '2024-01-19'
  },
  {
    id: 2,
    student: { name: 'Петрова Анна' },
    lesson: { title: 'Переменные и типы данных' },
    grade: 4,
    max_grade: 5,
    graded_at: '2024-01-18'
  },
  {
    id: 3,
    student: { name: 'Сидоров Петр' },
    lesson: { title: 'Основы HTML' },
    grade: 3,
    max_grade: 5,
    graded_at: '2024-01-17'
  }
])

// Быстрые действия
const quickActions = ref([
  {
    title: 'Создать урок',
    icon: 'mdi-plus',
    route: '/teacher/lessons/create'
  },
  {
    title: 'Создать тест',
    icon: 'mdi-help-circle',
    route: '/teacher/tests/create'
  },
  {
    title: 'Добавить материал',
    icon: 'mdi-file-plus',
    route: '/teacher/materials/create'
  },
  {
    title: 'Проверить задания',
    icon: 'mdi-check-circle',
    route: '/teacher/grades'
  }
])

// Уведомления
const notifications = ref([
  { id: 1, message: 'Новый студент записался на курс "Программирование"', time: '5 мин назад' },
  { id: 2, message: 'Получена новая оценка от Иванова И.', time: '15 мин назад' },
  { id: 3, message: 'Напоминание: урок "Введение в Python" завтра в 10:00', time: '1 час назад' }
])

// Прогресс курсов
const courseProgress = ref([
  { id: 1, name: 'Программирование', progress: 75 },
  { id: 2, name: 'Веб-разработка', progress: 45 },
  { id: 3, name: 'Базы данных', progress: 90 }
])

// Методы
const navigateTo = (route) => {
  router.visit(route)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ru-RU')
}

const getStatusColor = (status) => {
  const colors = {
    scheduled: 'primary',
    completed: 'success',
    cancelled: 'error'
  }
  return colors[status] || 'grey'
}

const getStatusText = (status) => {
  const texts = {
    scheduled: 'Запланирован',
    completed: 'Завершен',
    cancelled: 'Отменен'
  }
  return texts[status] || status
}

const getGradeColor = (grade) => {
  if (grade >= 4.5) return 'success'
  if (grade >= 3.5) return 'info'
  if (grade >= 2.5) return 'warning'
  return 'error'
}

const getProgressColor = (progress) => {
  if (progress >= 80) return 'success'
  if (progress >= 60) return 'info'
  if (progress >= 40) return 'warning'
  return 'error'
}

onMounted(() => {
  console.log('Страница учителя загружена')
})
</script>

<style scoped>
.v-card {
  border-radius: 12px;
}
</style>
