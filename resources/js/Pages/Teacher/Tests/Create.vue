<template>
  <Layout role="teacher">
    <v-container fluid>
      <!-- Заголовок -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold mb-2">Создать тест</h1>
              <p class="text-body-1 text-medium-emphasis">Создайте новый тест или скопируйте существующий</p>
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

      <v-row>
        <!-- Форма создания теста -->
        <v-col cols="12" md="8">
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-plus</v-icon>
              Создать новый тест
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
                        Создать тест
                      </v-btn>
                      <v-btn
                        color="secondary"
                        variant="outlined"
                        @click="resetForm"
                        prepend-icon="mdi-refresh"
                      >
                        Сбросить
                      </v-btn>
                    </div>
                  </v-col>
                </v-row>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>
        
        <!-- Доступные тесты для копирования -->
        <v-col cols="12" md="4">
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-content-copy</v-icon>
              Скопировать тест
            </v-card-title>
            <v-card-text>
              <p class="text-body-2 text-medium-emphasis mb-4">
                Выберите тест из других уроков вашего предмета для копирования
              </p>
              
              <div v-if="availableTests.length === 0" class="text-center py-4">
                <v-icon size="48" color="grey-lighten-1" class="mb-2">mdi-clipboard-text-outline</v-icon>
                <p class="text-body-2 text-grey">Нет доступных тестов для копирования</p>
              </div>
              
              <div v-else>
                <v-list>
                  <v-list-item
                    v-for="test in availableTests"
                    :key="test.id"
                    class="mb-2"
                  >
                    <template v-slot:prepend>
                      <v-avatar size="32" color="secondary">
                        <v-icon color="white" size="16">mdi-clipboard-text</v-icon>
                      </v-avatar>
                    </template>
                    
                    <v-list-item-title class="text-body-2 font-weight-medium">
                      {{ test.title }}
                    </v-list-item-title>
                    
                    <v-list-item-subtitle class="text-caption">
                      <div>{{ test.lesson_title }}</div>
                      <div>Автор: {{ test.creator_name }}</div>
                      <div>{{ test.questions_count }} вопросов</div>
                    </v-list-item-subtitle>
                    
                    <template v-slot:append>
                      <v-btn
                        color="primary"
                        variant="text"
                        size="small"
                        @click="showCopyDialog(test)"
                        prepend-icon="mdi-content-copy"
                      >
                        Копировать
                      </v-btn>
                    </template>
                  </v-list-item>
                </v-list>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Диалог копирования теста -->
      <v-dialog v-model="copyDialog" max-width="500">
        <v-card>
          <v-card-title class="text-h6">
            <v-icon start>mdi-content-copy</v-icon>
            Копировать тест
          </v-card-title>
          <v-card-text>
            <p class="mb-4">Вы копируете тест: <strong>{{ selectedTest?.title }}</strong></p>
            
            <v-text-field
              v-model="copyForm.title"
              label="Название нового теста"
              variant="outlined"
              :error-messages="copyForm.errors.title"
              required
            ></v-text-field>
            
            <v-select
              v-model="copyForm.lesson_id"
              :items="lessons"
              item-title="title"
              item-value="id"
              label="Урок для нового теста"
              variant="outlined"
              :error-messages="copyForm.errors.lesson_id"
              required
            >
              <template v-slot:item="{ props, item }">
                <v-list-item v-bind="props">
                  <v-list-item-title>{{ item.title }}</v-list-item-title>
                  <v-list-item-subtitle>{{ item.subject_name }}</v-list-item-subtitle>
                </v-list-item>
              </template>
            </v-select>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="secondary"
              variant="text"
              @click="copyDialog = false"
            >
              Отмена
            </v-btn>
            <v-btn
              color="primary"
              @click="copyTest"
              :loading="copyForm.processing"
            >
              Копировать
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </Layout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import Layout from '../../Layout.vue'

const page = usePage()

// Props из Inertia
const props = defineProps({
  lessons: {
    type: Array,
    default: () => []
  },
  availableTests: {
    type: Array,
    default: () => []
  }
})

// Состояние
const copyDialog = ref(false)
const selectedTest = ref(null)

// Форма создания теста
const form = useForm({
  title: '',
  description: '',
  lesson_id: '',
  type: 'quiz',
  time_limit: null,
  passing_score: null,
  max_attempts: null,
  is_active: true,
  is_public: false,
  start_date: '',
  end_date: '',
})

// Форма копирования теста
const copyForm = useForm({
  title: '',
  lesson_id: '',
})

// Методы
const navigateTo = (route) => {
  window.location.href = route
}

const submitForm = () => {
  form.post('/teacher/tests', {
    onSuccess: () => {
      console.log('Тест успешно создан')
    },
    onError: (errors) => {
      console.error('Ошибка при создании теста:', errors)
    }
  })
}

const resetForm = () => {
  form.reset()
  form.clearErrors()
}

const showCopyDialog = (test) => {
  selectedTest.value = test
  copyForm.title = `${test.title} (копия)`
  copyForm.lesson_id = ''
  copyForm.clearErrors()
  copyDialog.value = true
}

const copyTest = () => {
  copyForm.post(`/teacher/tests/${selectedTest.value.id}/copy`, {
    onSuccess: () => {
      copyDialog.value = false
      console.log('Тест успешно скопирован')
    },
    onError: (errors) => {
      console.error('Ошибка при копировании теста:', errors)
    }
  })
}
</script>

<style scoped>
.v-card {
  border-radius: 12px;
}

.v-list-item {
  border-radius: 8px;
}
</style>
