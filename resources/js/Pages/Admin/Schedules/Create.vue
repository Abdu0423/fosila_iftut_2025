<template>
  <AdminApp>
    <v-container fluid>
      <!-- Заголовок -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold mb-2">Создание расписания</h1>
              <p class="text-body-1 text-medium-emphasis">Добавление новой записи в расписание</p>
            </div>
            <v-btn
              color="secondary"
              variant="outlined"
              @click="navigateTo('/admin/schedules')"
              prepend-icon="mdi-arrow-left"
            >
              Назад к списку
            </v-btn>
          </div>
        </v-col>
      </v-row>

      <!-- Форма создания -->
      <v-row>
        <v-col cols="12" md="8">
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-calendar-plus</v-icon>
              Информация о расписании
            </v-card-title>
            <v-card-text>
              <v-form @submit.prevent="submitForm">
                <v-row>
                  <!-- Урок -->
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="form.lesson_id"
                      :items="lessons"
                      item-title="name"
                      item-value="id"
                      label="Урок *"
                      variant="outlined"
                      :error-messages="form.errors.lesson_id"
                      required
                    ></v-select>
                  </v-col>

                  <!-- Преподаватель -->
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="form.teacher_id"
                      :items="teachers"
                      item-title="name"
                      item-value="id"
                      label="Преподаватель *"
                      variant="outlined"
                      :error-messages="form.errors.teacher_id"
                      required
                    ></v-select>
                  </v-col>

                  <!-- Группа -->
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="form.group_id"
                      :items="groups"
                      item-title="name"
                      item-value="id"
                      label="Группа *"
                      variant="outlined"
                      :error-messages="form.errors.group_id"
                      required
                    ></v-select>
                  </v-col>

                  <!-- Семестр -->
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="form.semester"
                      :items="semesterOptions"
                      item-title="text"
                      item-value="value"
                      label="Семестр *"
                      variant="outlined"
                      :error-messages="form.errors.semester"
                      required
                    ></v-select>
                  </v-col>

                  <!-- Кредиты -->
                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model.number="form.credits"
                      label="Количество кредитов *"
                      type="number"
                      min="1"
                      max="10"
                      variant="outlined"
                      :error-messages="form.errors.credits"
                      required
                    ></v-text-field>
                  </v-col>

                  <!-- Год обучения -->
                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model.number="form.study_year"
                      label="Год обучения *"
                      type="number"
                      min="2020"
                      max="2030"
                      variant="outlined"
                      :error-messages="form.errors.study_year"
                      required
                    ></v-text-field>
                  </v-col>

                  <!-- Порядок -->
                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model.number="form.order"
                      label="Порядок урока *"
                      type="number"
                      min="1"
                      variant="outlined"
                      :error-messages="form.errors.order"
                      required
                    ></v-text-field>
                  </v-col>

                  <!-- Запланированная дата -->
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="form.scheduled_at"
                      label="Запланированная дата и время"
                      type="datetime-local"
                      variant="outlined"
                      :error-messages="form.errors.scheduled_at"
                    ></v-text-field>
                  </v-col>

                  <!-- Статус активности -->
                  <v-col cols="12" md="6">
                    <v-switch
                      v-model="form.is_active"
                      label="Активно"
                      color="success"
                      :error-messages="form.errors.is_active"
                    ></v-switch>
                  </v-col>
                </v-row>

                <!-- Кнопки действий -->
                <v-row class="mt-6">
                  <v-col cols="12">
                    <div class="d-flex gap-3">
                      <v-btn
                        type="submit"
                        color="primary"
                        size="large"
                        :loading="form.processing"
                        prepend-icon="mdi-content-save"
                      >
                        Создать расписание
                      </v-btn>
                      
                      <v-btn
                        color="secondary"
                        variant="outlined"
                        size="large"
                        @click="resetForm"
                        prepend-icon="mdi-refresh"
                      >
                        Сбросить
                      </v-btn>
                      
                      <v-btn
                        color="secondary"
                        variant="outlined"
                        size="large"
                        @click="navigateTo('/admin/schedules')"
                        prepend-icon="mdi-close"
                      >
                        Отмена
                      </v-btn>
                    </div>
                  </v-col>
                </v-row>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Информационная панель -->
        <v-col cols="12" md="4">
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-information</v-icon>
              Информация
            </v-card-title>
            <v-card-text>
              <div class="text-body-2 mb-4">
                <strong>Урок:</strong> Выберите урок из списка доступных уроков.
              </div>
              
              <div class="text-body-2 mb-4">
                <strong>Преподаватель:</strong> Выберите преподавателя, который будет вести урок.
              </div>
              
              <div class="text-body-2 mb-4">
                <strong>Группа:</strong> Выберите группу студентов для проведения урока.
              </div>
              
              <div class="text-body-2 mb-4">
                <strong>Семестр:</strong> Укажите семестр (1 или 2) для данного урока.
              </div>
              
              <div class="text-body-2 mb-4">
                <strong>Кредиты:</strong> Количество кредитов за данный урок (от 1 до 10).
              </div>
              
              <div class="text-body-2 mb-4">
                <strong>Год обучения:</strong> Учебный год, в котором проводится урок.
              </div>
              
              <div class="text-body-2 mb-4">
                <strong>Порядок:</strong> Порядковый номер урока в расписании.
              </div>
              
              <div class="text-body-2">
                <strong>Запланированная дата:</strong> Конкретная дата и время проведения урока (необязательно).
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </AdminApp>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminApp from '../AdminApp.vue'

// Props из Inertia
const props = defineProps({
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

// Данные
const lessons = ref(props.lessons)
const groups = ref(props.groups)
const teachers = ref(props.teachers)

const semesterOptions = ref([
  { value: 1, text: '1 семестр' },
  { value: 2, text: '2 семестр' },
  { value: 3, text: '3 семестр' },
  { value: 4, text: '4 семестр' },
  { value: 5, text: '5 семестр' },
  { value: 6, text: '6 семестр' },
  { value: 7, text: '7 семестр' },
  { value: 8, text: '8 семестр' },
])

// Форма
const form = useForm({
  lesson_id: '',
  teacher_id: '',
  group_id: '',
  semester: 1,
  credits: 3,
  study_year: new Date().getFullYear(),
  order: 1,
  scheduled_at: '',
  is_active: true
})

// Методы
const navigateTo = (route) => {
  window.location.href = route
}

const submitForm = () => {
  form.post('/admin/schedules', {
    onSuccess: () => {
      console.log('Расписание успешно создано')
    },
    onError: (errors) => {
      console.error('Ошибки валидации:', errors)
    }
  })
}

const resetForm = () => {
  form.reset()
  form.semester = 1
  form.credits = 3
  form.study_year = new Date().getFullYear()
  form.order = 1
  form.is_active = true
}
</script>

<style scoped>
.v-card {
  border-radius: 12px;
}
</style>
