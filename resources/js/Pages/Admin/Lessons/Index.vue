<template>
  <AdminApp>
    <v-container fluid>
      <!-- Заголовок -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold mb-2">Управление уроками</h1>
              <p class="text-body-1 text-medium-emphasis">Создание и управление уроками для курсов</p>
            </div>
            <v-btn
              color="primary"
              size="large"
              prepend-icon="mdi-plus"
              @click="navigateTo('/admin/lessons/create')"
            >
              Создать урок
            </v-btn>
          </div>
        </v-col>
      </v-row>

      <!-- Фильтры и поиск -->
      <v-row>
        <v-col cols="12">
          <v-card class="mb-6">
            <v-card-text>
              <v-row>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="search"
                    label="Поиск урока"
                    prepend-inner-icon="mdi-magnify"
                    variant="outlined"
                    density="compact"
                    clearable
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-select
                    v-model="selectedCourse"
                    :items="courses"
                    item-title="name"
                    item-value="id"
                    label="Курс"
                    variant="outlined"
                    density="compact"
                    clearable
                  ></v-select>
                </v-col>
                <v-col cols="12" md="2">
                  <v-select
                    v-model="selectedType"
                    :items="lessonTypes"
                    label="Тип урока"
                    variant="outlined"
                    density="compact"
                    clearable
                  ></v-select>
                </v-col>
                <v-col cols="12" md="2">
                  <v-select
                    v-model="selectedStatus"
                    :items="statuses"
                    label="Статус"
                    variant="outlined"
                    density="compact"
                    clearable
                  ></v-select>
                </v-col>
                <v-col cols="12" md="2">
                  <v-btn
                    color="primary"
                    variant="outlined"
                    block
                    @click="applyFilters"
                  >
                    Применить
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Таблица уроков -->
      <v-row>
        <v-col cols="12">
          <v-card>
            <v-data-table
              :headers="headers"
              :items="filteredLessons"
              :search="search"
              :loading="loading"
              class="elevation-1"
            >
              <!-- Название и описание -->
              <template v-slot:item.title="{ item }">
                <div>
                  <div class="font-weight-medium">{{ item.title }}</div>
                  <div class="text-caption text-medium-emphasis">{{ item.description }}</div>
                  <div class="text-caption text-medium-emphasis">{{ item.course?.name }} • {{ item.syllabus?.title }}</div>
                </div>
              </template>

              <!-- Тип урока -->
              <template v-slot:item.type="{ item }">
                <v-chip
                  :color="getTypeColor(item.type)"
                  size="small"
                  variant="tonal"
                >
                  {{ getTypeText(item.type) }}
                </v-chip>
              </template>

              <!-- Длительность -->
              <template v-slot:item.duration="{ item }">
                <div class="d-flex align-center">
                  <v-icon size="16" class="mr-1">mdi-clock-outline</v-icon>
                  {{ item.duration }} мин
                </div>
              </template>

              <!-- Материалы -->
              <template v-slot:item.materials_count="{ item }">
                <v-chip
                  color="info"
                  size="small"
                  variant="tonal"
                >
                  {{ item.materials_count }} материалов
                </v-chip>
              </template>

              <!-- Статус -->
              <template v-slot:item.status="{ item }">
                <v-chip
                  :color="getStatusColor(item.status)"
                  size="small"
                  variant="tonal"
                >
                  {{ getStatusText(item.status) }}
                </v-chip>
              </template>

              <!-- Дата создания -->
              <template v-slot:item.created_at="{ item }">
                {{ formatDate(item.created_at) }}
              </template>

              <!-- Действия -->
              <template v-slot:item.actions="{ item }">
                <v-menu>
                  <template v-slot:activator="{ props }">
                    <v-btn
                      icon="mdi-dots-vertical"
                      variant="text"
                      size="small"
                      v-bind="props"
                    ></v-btn>
                  </template>
                  <v-list>
                    <v-list-item
                      @click="viewLesson(item)"
                      prepend-icon="mdi-eye"
                    >
                      <v-list-item-title>Просмотр</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                      @click="editLesson(item)"
                      prepend-icon="mdi-pencil"
                    >
                      <v-list-item-title>Редактировать</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                      @click="manageMaterials(item)"
                      prepend-icon="mdi-file-multiple"
                    >
                      <v-list-item-title>Материалы</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                      @click="addTest(item)"
                      prepend-icon="mdi-help-circle"
                    >
                      <v-list-item-title>Добавить тест</v-list-item-title>
                    </v-list-item>
                    <v-divider></v-divider>
                    <v-list-item
                      @click="duplicateLesson(item)"
                      prepend-icon="mdi-content-copy"
                    >
                      <v-list-item-title>Дублировать</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                      @click="deleteLesson(item)"
                      prepend-icon="mdi-delete"
                      color="error"
                    >
                      <v-list-item-title>Удалить</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </template>
            </v-data-table>
          </v-card>
        </v-col>
      </v-row>

      <!-- Статистика -->
      <v-row class="mt-6">
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="primary" class="mb-4">mdi-teach</v-icon>
              <div class="text-h4 font-weight-bold">{{ lessons.length }}</div>
              <div class="text-body-2 text-medium-emphasis">Всего уроков</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="success" class="mb-4">mdi-check-circle</v-icon>
              <div class="text-h4 font-weight-bold">{{ activeLessons }}</div>
              <div class="text-body-2 text-medium-emphasis">Активных</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="info" class="mb-4">mdi-file-multiple</v-icon>
              <div class="text-h4 font-weight-bold">{{ totalMaterials }}</div>
              <div class="text-body-2 text-medium-emphasis">Материалов</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="warning" class="mb-4">mdi-clock</v-icon>
              <div class="text-h4 font-weight-bold">{{ totalDuration }}</div>
              <div class="text-body-2 text-medium-emphasis">Часов обучения</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Быстрые действия -->
      <v-row class="mt-6">
        <v-col cols="12">
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-lightning-bolt</v-icon>
              Быстрые действия
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="3">
                  <v-btn
                    variant="outlined"
                    block
                    prepend-icon="mdi-import"
                    @click="importLessons"
                  >
                    Импорт уроков
                  </v-btn>
                </v-col>
                <v-col cols="12" md="3">
                  <v-btn
                    variant="outlined"
                    block
                    prepend-icon="mdi-export"
                    @click="exportLessons"
                  >
                    Экспорт уроков
                  </v-btn>
                </v-col>
                <v-col cols="12" md="3">
                  <v-btn
                    variant="outlined"
                    block
                    prepend-icon="mdi-calendar-plus"
                    @click="bulkSchedule"
                  >
                    Массовое планирование
                  </v-btn>
                </v-col>
                <v-col cols="12" md="3">
                  <v-btn
                    variant="outlined"
                    block
                    prepend-icon="mdi-chart-line"
                    @click="analytics"
                  >
                    Аналитика
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </AdminApp>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminApp from '../AdminApp.vue'

// Состояние
const loading = ref(false)
const search = ref('')
const selectedCourse = ref(null)
const selectedType = ref(null)
const selectedStatus = ref(null)

// Данные
const lessons = ref([
  {
    id: 1,
    title: 'Введение в Python',
    description: 'Основы синтаксиса и структуры языка Python',
    course: { id: 1, name: 'Программирование' },
    syllabus: { id: 1, title: 'Основы программирования на Python' },
    type: 'lecture',
    duration: 90,
    materials_count: 5,
    status: 'active',
    created_at: '2024-01-15'
  },
  {
    id: 2,
    title: 'Переменные и типы данных',
    description: 'Изучение различных типов данных в Python',
    course: { id: 1, name: 'Программирование' },
    syllabus: { id: 1, title: 'Основы программирования на Python' },
    type: 'practice',
    duration: 120,
    materials_count: 8,
    status: 'active',
    created_at: '2024-01-16'
  },
  {
    id: 3,
    title: 'Управляющие конструкции',
    description: 'Условные операторы и циклы',
    course: { id: 1, name: 'Программирование' },
    syllabus: { id: 1, title: 'Основы программирования на Python' },
    type: 'mixed',
    duration: 150,
    materials_count: 12,
    status: 'active',
    created_at: '2024-01-17'
  },
  {
    id: 4,
    title: 'Функции и модули',
    description: 'Создание и использование функций',
    course: { id: 1, name: 'Программирование' },
    syllabus: { id: 1, title: 'Основы программирования на Python' },
    type: 'lecture',
    duration: 100,
    materials_count: 6,
    status: 'draft',
    created_at: '2024-01-18'
  }
])

const courses = ref([
  { id: 1, name: 'Программирование' },
  { id: 2, name: 'Веб-разработка' },
  { id: 3, name: 'Базы данных' },
  { id: 4, name: 'Фронтенд разработка' }
])

const lessonTypes = ref([
  { value: 'lecture', text: 'Лекция' },
  { value: 'practice', text: 'Практика' },
  { value: 'mixed', text: 'Смешанный' },
  { value: 'lab', text: 'Лабораторная' },
  { value: 'seminar', text: 'Семинар' }
])

const statuses = ref([
  { value: 'active', text: 'Активный' },
  { value: 'draft', text: 'Черновик' },
  { value: 'archived', text: 'Архивный' }
])

const headers = ref([
  { title: 'Название', key: 'title', sortable: true },
  { title: 'Тип', key: 'type', sortable: true },
  { title: 'Длительность', key: 'duration', sortable: true },
  { title: 'Материалы', key: 'materials_count', sortable: true },
  { title: 'Статус', key: 'status', sortable: true },
  { title: 'Дата создания', key: 'created_at', sortable: true },
  { title: 'Действия', key: 'actions', sortable: false }
])

// Вычисляемые свойства
const filteredLessons = computed(() => {
  let filtered = lessons.value

  if (selectedCourse.value) {
    filtered = filtered.filter(l => l.course.id === selectedCourse.value)
  }

  if (selectedType.value) {
    filtered = filtered.filter(l => l.type === selectedType.value)
  }

  if (selectedStatus.value) {
    filtered = filtered.filter(l => l.status === selectedStatus.value)
  }

  return filtered
})

const activeLessons = computed(() => 
  lessons.value.filter(l => l.status === 'active').length
)

const totalMaterials = computed(() => 
  lessons.value.reduce((sum, l) => sum + l.materials_count, 0)
)

const totalDuration = computed(() => {
  const totalMinutes = lessons.value.reduce((sum, l) => sum + l.duration, 0)
  return Math.round(totalMinutes / 60 * 10) / 10
})

// Методы
const navigateTo = (route) => {
  router.visit(route)
}

const applyFilters = () => {
  console.log('Применение фильтров:', { 
    search: search.value, 
    course: selectedCourse.value, 
    type: selectedType.value, 
    status: selectedStatus.value 
  })
}

const getTypeColor = (type) => {
  const colors = {
    lecture: 'primary',
    practice: 'success',
    mixed: 'warning',
    lab: 'info',
    seminar: 'purple'
  }
  return colors[type] || 'grey'
}

const getTypeText = (type) => {
  const typeObj = lessonTypes.value.find(t => t.value === type)
  return typeObj ? typeObj.text : type
}

const getStatusColor = (status) => {
  const colors = {
    active: 'success',
    draft: 'warning',
    archived: 'grey'
  }
  return colors[status] || 'grey'
}

const getStatusText = (status) => {
  const statusObj = statuses.value.find(s => s.value === status)
  return statusObj ? statusObj.text : status
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ru-RU')
}

const viewLesson = (lesson) => {
  console.log('Просмотр урока:', lesson)
  // Здесь будет переход на страницу просмотра
}

const editLesson = (lesson) => {
  navigateTo(`/admin/lessons/${lesson.id}/edit`)
}

const manageMaterials = (lesson) => {
  navigateTo(`/admin/lessons/${lesson.id}/materials`)
}

const addTest = (lesson) => {
  console.log('Добавление теста для урока:', lesson)
  // Здесь будет переход на создание теста
}

const duplicateLesson = (lesson) => {
  console.log('Дублирование урока:', lesson)
  // Здесь будет логика дублирования
}

const deleteLesson = (lesson) => {
  console.log('Удаление урока:', lesson)
  // Здесь будет подтверждение удаления
}

const importLessons = () => {
  console.log('Импорт уроков')
  // Здесь будет логика импорта
}

const exportLessons = () => {
  console.log('Экспорт уроков')
  // Здесь будет логика экспорта
}

const bulkSchedule = () => {
  console.log('Массовое планирование')
  // Здесь будет логика массового планирования
}

const analytics = () => {
  console.log('Аналитика уроков')
  // Здесь будет переход на аналитику
}

onMounted(() => {
  console.log('Страница управления уроками загружена')
})
</script>

<style scoped>
.v-data-table {
  border-radius: 8px;
}

.v-card {
  border-radius: 12px;
}
</style>
