<template>
  <TeacherApp>
    <v-container fluid>
      <!-- Заголовок -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold mb-2">Мои тесты</h1>
              <p class="text-body-1 text-medium-emphasis">Управление вашими тестами</p>
            </div>
            <div class="d-flex gap-2">
              <v-btn
                color="primary"
                @click="navigateTo('/teacher/tests/create')"
                prepend-icon="mdi-plus"
              >
                Создать тест
              </v-btn>
            </div>
          </div>
        </v-col>
      </v-row>

      <!-- Уведомления -->
      <v-row v-if="page.props.flash?.success">
        <v-col cols="12">
          <v-alert
            type="success"
            variant="tonal"
            closable
          >
            {{ page.props.flash.success }}
          </v-alert>
        </v-col>
      </v-row>

      <!-- Статистика -->
      <v-row>
        <v-col cols="12" md="4">
          <v-card variant="outlined">
            <v-card-text class="text-center">
              <div class="text-h4 font-weight-bold text-primary">{{ stats.total }}</div>
              <div class="text-body-2 text-medium-emphasis">Всего тестов</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="4">
          <v-card variant="outlined">
            <v-card-text class="text-center">
              <div class="text-h4 font-weight-bold text-success">{{ stats.active }}</div>
              <div class="text-body-2 text-medium-emphasis">Активных</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="4">
          <v-card variant="outlined">
            <v-card-text class="text-center">
              <div class="text-h4 font-weight-bold text-warning">{{ stats.inactive }}</div>
              <div class="text-body-2 text-medium-emphasis">Неактивных</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Фильтры -->
      <v-row>
        <v-col cols="12">
          <v-card variant="outlined">
            <v-card-text>
              <v-row>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="search"
                    label="Поиск по названию"
                    variant="outlined"
                    density="compact"
                    prepend-inner-icon="mdi-magnify"
                    clearable
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-select
                    v-model="selectedType"
                    :items="[
                      { title: 'Все типы', value: '' },
                      { title: 'Тест', value: 'quiz' },
                      { title: 'Экзамен', value: 'exam' },
                      { title: 'Домашнее задание', value: 'homework' }
                    ]"
                    item-title="title"
                    item-value="value"
                    label="Тип теста"
                    variant="outlined"
                    density="compact"
                  ></v-select>
                </v-col>
                <v-col cols="12" md="3">
                  <v-select
                    v-model="selectedLesson"
                    :items="lessons"
                    item-title="title"
                    item-value="id"
                    label="Урок"
                    variant="outlined"
                    density="compact"
                    clearable
                  ></v-select>
                </v-col>
                <v-col cols="12" md="2">
                  <v-select
                    v-model="selectedStatus"
                    :items="[
                      { title: 'Все', value: '' },
                      { title: 'Активные', value: 'active' },
                      { title: 'Неактивные', value: 'inactive' }
                    ]"
                    item-title="title"
                    item-value="value"
                    label="Статус"
                    variant="outlined"
                    density="compact"
                  ></v-select>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Список тестов -->
      <v-row>
        <v-col cols="12">
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-clipboard-text</v-icon>
              Тесты ({{ filteredTests.length }})
            </v-card-title>
            <v-card-text>
              <div v-if="filteredTests.length === 0" class="text-center py-8">
                <v-icon size="64" color="grey-lighten-1" class="mb-4">mdi-clipboard-text-outline</v-icon>
                <h3 class="text-h6 text-grey">Нет тестов</h3>
                <p class="text-body-2 text-grey">Создайте первый тест для ваших студентов</p>
                <v-btn
                  color="primary"
                  @click="navigateTo('/teacher/tests/create')"
                  class="mt-4"
                  prepend-icon="mdi-plus"
                >
                  Создать тест
                </v-btn>
              </div>
              
              <div v-else>
                <v-list>
                  <v-list-item
                    v-for="test in filteredTests"
                    :key="test.id"
                    class="mb-4"
                  >
                    <template v-slot:prepend>
                      <v-avatar color="primary" size="40">
                        <v-icon color="white">mdi-clipboard-text</v-icon>
                      </v-avatar>
                    </template>
                    
                    <v-list-item-title class="font-weight-medium mb-2">
                      {{ test.title }}
                    </v-list-item-title>
                    
                    <v-list-item-subtitle>
                      <div class="d-flex align-center gap-4 mb-2">
                        <v-chip
                          color="primary"
                          size="small"
                          variant="tonal"
                        >
                          {{ getTypeText(test.type) }}
                        </v-chip>
                        
                        <v-chip
                          color="secondary"
                          size="small"
                          variant="tonal"
                        >
                          {{ test.lesson_title }}
                        </v-chip>
                        
                        <v-chip
                          :color="test.is_active ? 'success' : 'warning'"
                          size="small"
                          variant="tonal"
                        >
                          {{ test.is_active ? 'Активен' : 'Неактивен' }}
                        </v-chip>
                        
                        <v-chip
                          color="info"
                          size="small"
                          variant="tonal"
                        >
                          {{ test.questions_count }} вопросов
                        </v-chip>
                      </div>
                      
                      <p v-if="test.description" class="text-body-2 mb-2">
                        {{ test.description }}
                      </p>
                      
                      <div class="d-flex align-center gap-4 text-caption text-medium-emphasis">
                        <span v-if="test.time_limit">
                          <v-icon size="16" class="mr-1">mdi-clock</v-icon>
                          {{ test.time_limit }} мин.
                        </span>
                        <span v-if="test.passing_score">
                          <v-icon size="16" class="mr-1">mdi-percent</v-icon>
                          {{ test.passing_score }}% для прохождения
                        </span>
                        <span v-if="test.max_attempts">
                          <v-icon size="16" class="mr-1">mdi-repeat</v-icon>
                          {{ test.max_attempts }} попыток
                        </span>
                        <span>
                          <v-icon size="16" class="mr-1">mdi-calendar</v-icon>
                          {{ test.created_at }}
                        </span>
                      </div>
                    </v-list-item-subtitle>
                    
                    <template v-slot:append>
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
                            @click="navigateTo(`/teacher/tests/${test.id}`)"
                            prepend-icon="mdi-eye"
                          >
                            <v-list-item-title>Просмотр</v-list-item-title>
                          </v-list-item>
                          <v-list-item
                            @click="navigateTo(`/teacher/tests/${test.id}/edit`)"
                            prepend-icon="mdi-pencil"
                          >
                            <v-list-item-title>Редактировать</v-list-item-title>
                          </v-list-item>
                          <v-list-item
                            @click="toggleTestStatus(test)"
                            prepend-icon="mdi-toggle-switch"
                          >
                            <v-list-item-title>
                              {{ test.is_active ? 'Деактивировать' : 'Активировать' }}
                            </v-list-item-title>
                          </v-list-item>
                          <v-divider></v-divider>
                          <v-list-item
                            @click="deleteTest(test)"
                            prepend-icon="mdi-delete"
                            color="error"
                          >
                            <v-list-item-title>Удалить</v-list-item-title>
                          </v-list-item>
                        </v-list>
                      </v-menu>
                    </template>
                  </v-list-item>
                </v-list>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </TeacherApp>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import TeacherApp from '../TeacherApp.vue'

const page = usePage()

// Props из Inertia
const props = defineProps({
  tests: {
    type: Array,
    default: () => []
  },
  lessons: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({})
  }
})

// Состояние
const search = ref('')
const selectedType = ref('')
const selectedLesson = ref(null)
const selectedStatus = ref('')

// Вычисляемые свойства
const filteredTests = computed(() => {
  let filtered = props.tests

  // Поиск по названию
  if (search.value) {
    filtered = filtered.filter(test =>
      test.title.toLowerCase().includes(search.value.toLowerCase()) ||
      test.description?.toLowerCase().includes(search.value.toLowerCase())
    )
  }

  // Фильтр по типу
  if (selectedType.value) {
    filtered = filtered.filter(test => test.type === selectedType.value)
  }

  // Фильтр по уроку
  if (selectedLesson.value) {
    filtered = filtered.filter(test => test.lesson_id === selectedLesson.value)
  }

  // Фильтр по статусу
  if (selectedStatus.value === 'active') {
    filtered = filtered.filter(test => test.is_active)
  } else if (selectedStatus.value === 'inactive') {
    filtered = filtered.filter(test => !test.is_active)
  }

  return filtered
})

// Методы
const navigateTo = (route) => {
  window.location.href = route
}

const getTypeText = (type) => {
  const types = {
    'quiz': 'Тест',
    'exam': 'Экзамен',
    'homework': 'Домашнее задание'
  }
  return types[type] || type
}

const toggleTestStatus = (test) => {
  const form = useForm({})
  form.post(`/teacher/tests/${test.id}/toggle-status`, {
    onSuccess: () => {
      console.log('Статус теста изменен')
    },
    onError: (errors) => {
      console.error('Ошибка при изменении статуса:', errors)
    }
  })
}

const deleteTest = (test) => {
  if (confirm('Вы уверены, что хотите удалить этот тест?')) {
    const form = useForm({})
    form.delete(`/teacher/tests/${test.id}`, {
      onSuccess: () => {
        console.log('Тест успешно удален')
      },
      onError: (errors) => {
        console.error('Ошибка при удалении:', errors)
      }
    })
  }
}
</script>

<style scoped>
.v-card {
  border-radius: 12px;
}

.v-list-item {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  margin-bottom: 8px;
}
</style>
