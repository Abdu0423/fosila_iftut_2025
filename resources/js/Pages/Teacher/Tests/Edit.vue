<template>
  <Layout role="teacher">
    <v-container fluid>
      <!-- Заголовок -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold mb-2">Редактировать тест</h1>
              <p class="text-body-1 text-medium-emphasis">Измените параметры теста</p>
            </div>
            <div class="d-flex gap-2">
              <v-btn
                color="secondary"
                variant="outlined"
                @click="navigateTo('/teacher/tests')"
                prepend-icon="mdi-arrow-left"
              >
                Назад к тестам
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

      <!-- Форма редактирования -->
      <v-row>
        <v-col cols="12">
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-pencil</v-icon>
              Редактировать тест: {{ test.title }}
            </v-card-title>
            <v-card-text>
              <v-form @submit.prevent="submitForm">
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      v-model="form.title"
                      label="Название теста"
                      variant="outlined"
                      :error-messages="form.errors.title"
                      required
                    ></v-text-field>
                  </v-col>
                  
                  <v-col cols="12">
                    <v-textarea
                      v-model="form.description"
                      label="Описание теста"
                      variant="outlined"
                      :error-messages="form.errors.description"
                      rows="3"
                    ></v-textarea>
                  </v-col>
                  
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="form.lesson_id"
                      :items="lessons"
                      item-title="title"
                      item-value="id"
                      label="Урок"
                      variant="outlined"
                      :error-messages="form.errors.lesson_id"
                      required
                    >
                      <template v-slot:item="{ props, item }">
                        <v-list-item v-bind="props">
                          <v-list-item-title>{{ item.title }}</v-list-item-title>
                          <v-list-item-subtitle>{{ item.subject_name }}</v-list-item-subtitle>
                        </v-list-item>
                      </template>
                    </v-select>
                  </v-col>
                  
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="form.type"
                      :items="[
                        { title: 'Тест', value: 'quiz' },
                        { title: 'Экзамен', value: 'exam' },
                        { title: 'Домашнее задание', value: 'homework' }
                      ]"
                      item-title="title"
                      item-value="value"
                      label="Тип теста"
                      variant="outlined"
                      :error-messages="form.errors.type"
                      required
                    ></v-select>
                  </v-col>
                  
                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model.number="form.time_limit"
                      label="Временное ограничение (минуты)"
                      variant="outlined"
                      type="number"
                      :error-messages="form.errors.time_limit"
                      min="1"
                    ></v-text-field>
                  </v-col>
                  
                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model.number="form.passing_score"
                      label="Проходной балл (%)"
                      variant="outlined"
                      type="number"
                      :error-messages="form.errors.passing_score"
                      min="0"
                      max="100"
                    ></v-text-field>
                  </v-col>
                  
                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model.number="form.max_attempts"
                      label="Максимум попыток"
                      variant="outlined"
                      type="number"
                      :error-messages="form.errors.max_attempts"
                      min="1"
                    ></v-text-field>
                  </v-col>
                  
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="form.start_date"
                      label="Дата начала"
                      variant="outlined"
                      type="datetime-local"
                      :error-messages="form.errors.start_date"
                    ></v-text-field>
                  </v-col>
                  
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="form.end_date"
                      label="Дата окончания"
                      variant="outlined"
                      type="datetime-local"
                      :error-messages="form.errors.end_date"
                    ></v-text-field>
                  </v-col>
                  
                  <v-col cols="12" md="6">
                    <v-switch
                      v-model="form.is_active"
                      label="Активный"
                      color="success"
                      :error-messages="form.errors.is_active"
                    ></v-switch>
                  </v-col>
                  
                  <v-col cols="12" md="6">
                    <v-switch
                      v-model="form.is_public"
                      label="Публичный (доступен другим учителям)"
                      color="info"
                      :error-messages="form.errors.is_public"
                    ></v-switch>
                  </v-col>
                </v-row>
                
                <v-row>
                  <v-col cols="12">
                    <div class="d-flex gap-2">
                      <v-btn
                        type="submit"
                        color="primary"
                        :loading="form.processing"
                        prepend-icon="mdi-content-save"
                      >
                        Сохранить изменения
                      </v-btn>
                      <v-btn
                        color="secondary"
                        variant="outlined"
                        @click="resetForm"
                        prepend-icon="mdi-refresh"
                      >
                        Сбросить
                      </v-btn>
                      <v-btn
                        color="error"
                        variant="outlined"
                        @click="deleteTest"
                        prepend-icon="mdi-delete"
                      >
                        Удалить тест
                      </v-btn>
                    </div>
                  </v-col>
                </v-row>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </Layout>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import Layout from '../../Layout.vue'

const page = usePage()

// Props из Inertia
const props = defineProps({
  test: {
    type: Object,
    required: true
  },
  lessons: {
    type: Array,
    default: () => []
  }
})

// Форма редактирования теста
const form = useForm({
  title: props.test.title,
  description: props.test.description,
  lesson_id: props.test.lesson_id,
  type: props.test.type,
  time_limit: props.test.time_limit,
  passing_score: props.test.passing_score,
  max_attempts: props.test.max_attempts,
  is_active: props.test.is_active,
  is_public: props.test.is_public,
  start_date: props.test.start_date,
  end_date: props.test.end_date,
})

// Методы
const navigateTo = (route) => {
  window.location.href = route
}

const submitForm = () => {
  form.put(`/teacher/tests/${props.test.id}`, {
    onSuccess: () => {
      console.log('Тест успешно обновлен')
    },
    onError: (errors) => {
      console.error('Ошибка при обновлении теста:', errors)
    }
  })
}

const resetForm = () => {
  form.reset()
  form.clearErrors()
}

const deleteTest = () => {
  if (confirm('Вы уверены, что хотите удалить этот тест?')) {
    form.delete(`/teacher/tests/${props.test.id}`, {
      onSuccess: () => {
        console.log('Тест успешно удален')
      },
      onError: (errors) => {
        console.error('Ошибка при удалении теста:', errors)
      }
    })
  }
}
</script>

<style scoped>
.v-card {
  border-radius: 12px;
}
</style>
