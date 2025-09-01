<template>
  <AdminApp>
    <v-container fluid>
      <!-- Заголовок -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold mb-2">Управление расписанием</h1>
              <p class="text-body-1 text-medium-emphasis">Создание и управление расписанием занятий</p>
            </div>
            <v-btn
              color="primary"
              size="large"
              prepend-icon="mdi-plus"
              @click="navigateTo('/admin/schedules/create')"
            >
              Создать расписание
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
                    label="Поиск по уроку"
                    prepend-inner-icon="mdi-magnify"
                    variant="outlined"
                    density="compact"
                    clearable
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="2">
                  <v-select
                    v-model="selectedLesson"
                    :items="lessons"
                    item-title="name"
                    item-value="id"
                    label="Урок"
                    variant="outlined"
                    density="compact"
                    clearable
                  ></v-select>
                </v-col>
                <v-col cols="12" md="2">
                  <v-select
                    v-model="selectedTeacher"
                    :items="teachers"
                    item-title="name"
                    item-value="id"
                    label="Преподаватель"
                    variant="outlined"
                    density="compact"
                    clearable
                  ></v-select>
                </v-col>
                <v-col cols="12" md="2">
                  <v-select
                    v-model="selectedGroup"
                    :items="groups"
                    item-title="name"
                    item-value="id"
                    label="Группа"
                    variant="outlined"
                    density="compact"
                    clearable
                  ></v-select>
                </v-col>
                <v-col cols="12" md="2">
                  <v-select
                    v-model="selectedSemester"
                    :items="semesters"
                    item-title="text"
                    item-value="value"
                    label="Семестр"
                    variant="outlined"
                    density="compact"
                    clearable
                  ></v-select>
                </v-col>
                <v-col cols="12" md="1">
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

      <!-- Таблица расписания -->
      <v-row>
        <v-col cols="12">
          <v-card>
            <v-data-table
              :headers="headers"
              :items="filteredSchedules"
              :search="search"
              :loading="loading"
              class="elevation-1"
            >
              <!-- Урок -->
              <template v-slot:item.lesson_name="{ item }">
                <div class="font-weight-medium">{{ item.lesson_name }}</div>
              </template>

              <!-- Преподаватель -->
              <template v-slot:item.teacher_name="{ item }">
                <div>{{ item.teacher_name }}</div>
              </template>

              <!-- Группа -->
              <template v-slot:item.group_name="{ item }">
                <div>{{ item.group_name }}</div>
              </template>

              <!-- Семестр -->
              <template v-slot:item.semester="{ item }">
                <v-chip
                  :color="item.semester === 1 ? 'primary' : 'secondary'"
                  size="small"
                  variant="tonal"
                >
                  {{ item.semester }} семестр
                </v-chip>
              </template>

              <!-- Кредиты -->
              <template v-slot:item.credits="{ item }">
                <v-chip
                  color="info"
                  size="small"
                  variant="tonal"
                >
                  {{ item.credits }} кредитов
                </v-chip>
              </template>

              <!-- Год обучения -->
              <template v-slot:item.study_year="{ item }">
                <div>{{ item.study_year }}-{{ item.study_year + 1 }}</div>
              </template>

              <!-- Порядок -->
              <template v-slot:item.order="{ item }">
                <v-chip
                  color="warning"
                  size="small"
                  variant="tonal"
                >
                  {{ item.order }}
                </v-chip>
              </template>

              <!-- Запланированная дата -->
              <template v-slot:item.scheduled_at="{ item }">
                <div v-if="item.scheduled_at !== 'Не указано'">
                  <div class="font-weight-medium">{{ item.scheduled_at }}</div>
                </div>
                <div v-else class="text-medium-emphasis">
                  {{ item.scheduled_at }}
                </div>
              </template>

              <!-- Статус -->
              <template v-slot:item.is_active="{ item }">
                <v-chip
                  :color="item.is_active ? 'success' : 'grey'"
                  size="small"
                  variant="tonal"
                >
                  {{ item.is_active ? 'Активно' : 'Неактивно' }}
                </v-chip>
              </template>

              <!-- Дата создания -->
              <template v-slot:item.created_at="{ item }">
                {{ item.created_at }}
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
                      @click="viewSchedule(item)"
                      prepend-icon="mdi-eye"
                    >
                      <v-list-item-title>Просмотр</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                      @click="editSchedule(item)"
                      prepend-icon="mdi-pencil"
                    >
                      <v-list-item-title>Редактировать</v-list-item-title>
                    </v-list-item>
                    <v-divider></v-divider>
                    <v-list-item
                      @click="toggleStatus(item)"
                      prepend-icon="mdi-toggle-switch"
                    >
                      <v-list-item-title>{{ item.is_active ? 'Деактивировать' : 'Активировать' }}</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                      @click="deleteSchedule(item)"
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
              <v-icon size="48" color="primary" class="mb-4">mdi-calendar-clock</v-icon>
              <div class="text-h4 font-weight-bold">{{ schedules.length }}</div>
              <div class="text-body-2 text-medium-emphasis">Всего записей</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="success" class="mb-4">mdi-check-circle</v-icon>
              <div class="text-h4 font-weight-bold">{{ activeSchedules }}</div>
              <div class="text-body-2 text-medium-emphasis">Активных</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="info" class="mb-4">mdi-teach</v-icon>
              <div class="text-h4 font-weight-bold">{{ uniqueTeachers }}</div>
              <div class="text-body-2 text-medium-emphasis">Преподавателей</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="warning" class="mb-4">mdi-account-group</v-icon>
              <div class="text-h4 font-weight-bold">{{ uniqueGroups }}</div>
              <div class="text-body-2 text-medium-emphasis">Групп</div>
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
                    @click="importSchedule"
                  >
                    Импорт расписания
                  </v-btn>
                </v-col>
                <v-col cols="12" md="3">
                  <v-btn
                    variant="outlined"
                    block
                    prepend-icon="mdi-file-download"
                    @click="exportSchedulePost"
                  >
                  Экспорт расписания
                  </v-btn>
                </v-col>
                <v-col cols="12" md="3">
                  <v-btn
                    variant="outlined"
                    block
                    prepend-icon="mdi-calendar-plus"
                    @click="bulkCreate"
                  >
                    Массовое создание
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

// Props из Inertia
const props = defineProps({
  schedules: {
    type: Array,
    default: () => []
  },
  lessons: {
    type: Array,
    default: () => []
  },
  groups: {
    type: Array,
    default: () => []
  },
  teachers: {
    type: Array,
    default: () => []
  }
})

// Состояние
const loading = ref(false)
const search = ref('')
const selectedLesson = ref(null)
const selectedTeacher = ref(null)
const selectedGroup = ref(null)
const selectedSemester = ref(null)

// Данные
const schedules = ref(props.schedules)
const lessons = ref(props.lessons)
const groups = ref(props.groups)
const teachers = ref(props.teachers)

const semesters = ref([
  { value: 1, text: '1 семестр' },
  { value: 2, text: '2 семестр' },
  { value: 3, text: '3 семестр' },
  { value: 4, text: '4 семестр' },
  { value: 5, text: '5 семестр' },
  { value: 6, text: '6 семестр' },
  { value: 7, text: '7 семестр' },
  { value: 8, text: '8 семестр' },
])

const headers = ref([
  { title: 'Урок', key: 'lesson_name', sortable: true },
  { title: 'Преподаватель', key: 'teacher_name', sortable: true },
  { title: 'Группа', key: 'group_name', sortable: true },
  { title: 'Семестр', key: 'semester', sortable: true },
  { title: 'Кредиты', key: 'credits', sortable: true },
  { title: 'Год обучения', key: 'study_year', sortable: true },
  { title: 'Порядок', key: 'order', sortable: true },
  { title: 'Запланировано', key: 'scheduled_at', sortable: true },
  { title: 'Статус', key: 'is_active', sortable: true },
  { title: 'Дата создания', key: 'created_at', sortable: true },
  { title: 'Действия', key: 'actions', sortable: false }
])

// Вычисляемые свойства
const filteredSchedules = computed(() => {
  let filtered = schedules.value

  if (selectedLesson.value) {
    filtered = filtered.filter(s => {
      const lesson = lessons.value.find(l => l.id === selectedLesson.value)
      return lesson && s.lesson_name === lesson.name
    })
  }

  if (selectedTeacher.value) {
    filtered = filtered.filter(s => {
      const teacher = teachers.value.find(t => t.id === selectedTeacher.value)
      return teacher && s.teacher_name === teacher.name
    })
  }

  if (selectedGroup.value) {
    filtered = filtered.filter(s => {
      const group = groups.value.find(g => g.id === selectedGroup.value)
      return group && s.group_name === group.name
    })
  }

  if (selectedSemester.value) {
    filtered = filtered.filter(s => s.semester === selectedSemester.value)
  }

  return filtered
})

const activeSchedules = computed(() => 
  schedules.value.filter(s => s.is_active).length
)

const uniqueTeachers = computed(() => {
  const teacherNames = [...new Set(schedules.value.map(s => s.teacher_name))]
  return teacherNames.filter(name => name !== 'Не указан').length
})

const uniqueGroups = computed(() => {
  const groupNames = [...new Set(schedules.value.map(s => s.group_name))]
  return groupNames.filter(name => name !== 'Не указана').length
})

// Методы
const navigateTo = (route) => {
  router.visit(route)
}

const applyFilters = () => {
  console.log('Применение фильтров:', { 
    search: search.value, 
    lesson: selectedLesson.value, 
    teacher: selectedTeacher.value, 
    group: selectedGroup.value,
    semester: selectedSemester.value
  })
}

const viewSchedule = (schedule) => {
  navigateTo(`/admin/schedules/${schedule.id}`)
}

const editSchedule = (schedule) => {
  navigateTo(`/admin/schedules/${schedule.id}/edit`)
}

const toggleStatus = (schedule) => {
  console.log('Переключение статуса расписания:', schedule)
  // Здесь будет логика переключения статуса
}

const deleteSchedule = (schedule) => {
  console.log('Удаление расписания:', schedule)
  // Здесь будет подтверждение удаления
}

const importSchedule = () => {
  // Создаем скрытый input для выбора файла
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = '.csv,.xlsx,.xls'
  input.onchange = async (event) => {
    const file = event.target.files[0]
    if (!file) return

    // Показываем индикатор загрузки
    loading.value = true

    const formData = new FormData()
    formData.append('file', file)

    try {
      const response = await fetch('/admin/schedules/import', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })

      const result = await response.json()
      
      if (result.success) {
        // Показываем уведомление об успехе
        showSuccessNotification(`Импорт завершен! ${result.message}`)
        // Перезагружаем страницу для обновления данных
        setTimeout(() => {
          window.location.reload()
        }, 1500)
      } else {
        showErrorNotification('Ошибка при импорте: ' + result.message)
      }
    } catch (error) {
      showErrorNotification('Ошибка при импорте: ' + error.message)
    } finally {
      loading.value = false
    }
  }
  input.click()
}

const exportSchedule = async () => {
  try {
    loading.value = true
    console.log('Начинаем экспорт...')
    
    // Получаем CSRF токен
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    console.log('CSRF токен:', token ? 'найден' : 'не найден')
    
    // Запрос с CSRF токеном и правильными заголовками
    const response = await fetch('/admin/schedules/export', {
      method: 'GET',
      headers: {
        'Accept': 'text/csv, application/json',
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin'
    })
    console.log('Ответ получен:', response.status, response.statusText)
    
    if (response.ok) {
      console.log('Ответ успешный, создаем blob...')
      const blob = await response.blob()
      console.log('Blob создан:', blob.size, 'байт')
      
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.download = `schedules_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.csv`
      
      console.log('Скачиваем файл...')
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      window.URL.revokeObjectURL(url)
      
      showSuccessNotification('Экспорт завершен! Файл скачивается...')
    } else {
      console.log('Ошибка ответа:', response.status, response.statusText)
      
      // Пытаемся получить текст ошибки
      let errorMessage = response.statusText
      try {
        const errorText = await response.text()
        console.log('Текст ошибки:', errorText)
        if (errorText) {
          errorMessage = errorText
        }
      } catch (e) {
        console.log('Не удалось получить текст ошибки:', e)
      }
      
      showErrorNotification('Ошибка при экспорте: ' + errorMessage)
    }
    
  } catch (error) {
    console.error('Ошибка в exportSchedule:', error)
    showErrorNotification('Ошибка при экспорте: ' + error.message)
  } finally {
    loading.value = false
  }
}

const bulkCreate = () => {
  navigateTo('/admin/schedules/bulk-create')
}

const analytics = () => {
  navigateTo('/admin/schedules/analytics')
}

const testExport = async () => {
  try {
    loading.value = true
    
    // Тестируем простой экспорт
    const response = await fetch('/admin/test-export', {
      method: 'GET'
    })
    
    if (response.ok) {
      const blob = await response.blob()
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.download = 'test.txt'
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      window.URL.revokeObjectURL(url)
      
      showSuccessNotification('Тестовый экспорт успешен!')
    } else {
      showErrorNotification('Ошибка тестового экспорта: ' + response.statusText)
    }
    
  } catch (error) {
    showErrorNotification('Ошибка тестового экспорта: ' + error.message)
  } finally {
    loading.value = false
  }
}

const testScheduleExport = async () => {
  try {
    loading.value = true
    console.log('Тестируем данные расписания...')

    const response = await fetch('/test-schedule-export', {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
      }
    })

    const data = await response.json()
    console.log('Ответ сервера:', data)

    if (response.ok) {
      showSuccessNotification(`Данные найдены: ${data.count} записей`)
    } else {
      showErrorNotification('Ошибка получения данных: ' + (data.error || response.statusText))
    }

  } catch (error) {
    console.error('Ошибка тестирования данных:', error)
    showErrorNotification('Ошибка тестирования данных: ' + error.message)
  } finally {
    loading.value = false
  }
}

const testExportSchedule = async () => {
  try {
    loading.value = true
    console.log('Тестируем экспорт расписания без middleware...')

    const response = await fetch('/test-export-schedule', {
      method: 'GET',
      headers: {
        'Accept': 'text/csv, application/json',
      }
    })

    if (response.ok) {
      const blob = await response.blob()
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.download = `test_schedules_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.csv`
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      window.URL.revokeObjectURL(url)

      showSuccessNotification('Тестовый экспорт расписания успешен!')
    } else {
      const errorData = await response.json().catch(() => ({ error: 'Неизвестная ошибка' }))
      showErrorNotification('Ошибка тестового экспорта: ' + (errorData.error || response.statusText))
    }

  } catch (error) {
    console.error('Ошибка тестового экспорта:', error)
    showErrorNotification('Ошибка тестового экспорта: ' + error.message)
  } finally {
    loading.value = false
  }
}

const exportSchedulePost = async () => {
  try {
    loading.value = true
    console.log('Начинаем экспорт через POST...')
    
    // Получаем CSRF токен
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    console.log('CSRF токен:', token ? 'найден' : 'не найден')
    
    // Создаем FormData для POST запроса
    const formData = new FormData()
    
    // POST запрос с CSRF токеном
    const response = await fetch('/admin/schedules/export', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    })
    console.log('Ответ получен:', response.status, response.statusText)
    
    if (response.ok) {
      console.log('Ответ успешный, создаем blob...')
      const blob = await response.blob()
      console.log('Blob создан:', blob.size, 'байт')
      
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.download = `schedules_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.csv`
      
      console.log('Скачиваем файл...')
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      window.URL.revokeObjectURL(url)
      
      showSuccessNotification('Экспорт через POST завершен! Файл скачивается...')
    } else {
      console.log('Ошибка ответа:', response.status, response.statusText)
      
      // Пытаемся получить текст ошибки
      let errorMessage = response.statusText
      try {
        const errorText = await response.text()
        console.log('Текст ошибки:', errorText)
        if (errorText) {
          errorMessage = errorText
        }
      } catch (e) {
        console.log('Не удалось получить текст ошибки:', e)
      }
      
      showErrorNotification('Ошибка при экспорте через POST: ' + errorMessage)
    }
    
  } catch (error) {
    console.error('Ошибка в exportSchedulePost:', error)
    showErrorNotification('Ошибка при экспорте через POST: ' + error.message)
  } finally {
    loading.value = false
  }
}

// Функции уведомлений
const showSuccessNotification = (message) => {
  // Создаем временное уведомление
  const notification = document.createElement('div')
  notification.className = 'success-notification'
  notification.textContent = message
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: #4caf50;
    color: white;
    padding: 16px 24px;
    border-radius: 8px;
    z-index: 9999;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    animation: slideIn 0.3s ease;
  `
  
  document.body.appendChild(notification)
  
  setTimeout(() => {
    notification.style.animation = 'slideOut 0.3s ease'
    setTimeout(() => {
      document.body.removeChild(notification)
    }, 300)
  }, 3000)
}

const showErrorNotification = (message) => {
  // Создаем временное уведомление об ошибке
  const notification = document.createElement('div')
  notification.className = 'error-notification'
  notification.textContent = message
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: #f44336;
    color: white;
    padding: 16px 24px;
    border-radius: 8px;
    z-index: 9999;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    animation: slideIn 0.3s ease;
  `
  
  document.body.appendChild(notification)
  
  setTimeout(() => {
    notification.style.animation = 'slideOut 0.3s ease'
    setTimeout(() => {
      document.body.removeChild(notification)
    }, 300)
  }, 4000)
}

onMounted(() => {
  console.log('Страница управления расписанием загружена')
  
  // Добавляем CSS анимации для уведомлений
  const style = document.createElement('style')
  style.textContent = `
    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
      from { transform: translateX(0); opacity: 1; }
      to { transform: translateX(100%); opacity: 0; }
    }
  `
  document.head.appendChild(style)
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
