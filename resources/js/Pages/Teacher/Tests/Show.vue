<template>
  <Layout role="teacher">
    <v-container fluid>
      <!-- Заголовок -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold mb-2">{{ test.title }}</h1>
              <p class="text-body-1 text-medium-emphasis">
                {{ test.schedule.subject_name }} - {{ test.schedule.group_name }}
              </p>
            </div>
            <div class="d-flex gap-2">
              <v-btn
                :color="test.is_active ? 'warning' : 'success'"
                @click="toggleStatus"
                variant="tonal"
              >
                <v-icon start>mdi-toggle-switch</v-icon>
                {{ test.is_active ? 'Деактивировать' : 'Активировать' }}
              </v-btn>
              <v-btn
                color="secondary"
                variant="outlined"
                @click="navigateTo('/teacher/tests')"
                prepend-icon="mdi-arrow-left"
              >
                К списку
              </v-btn>
            </div>
          </div>
        </v-col>
      </v-row>

      <!-- Уведомления -->
      <v-row v-if="page.props.flash?.success">
        <v-col cols="12">
          <v-alert type="success" variant="tonal" closable>
            {{ page.props.flash.success }}
          </v-alert>
        </v-col>
      </v-row>

      <v-row>
        <!-- Настройки теста -->
        <v-col cols="12" md="4">
          <v-card class="mb-4">
            <v-card-title class="text-h6">
              <v-icon start>mdi-cog</v-icon>
              Настройки теста
            </v-card-title>
            <v-card-text>
              <v-form @submit.prevent="saveSettings">
                <v-text-field
                  v-model="settingsForm.title"
                  label="Название теста"
                  variant="outlined"
                  density="compact"
                  class="mb-3"
                ></v-text-field>

                <v-textarea
                  v-model="settingsForm.description"
                  label="Описание"
                  variant="outlined"
                  density="compact"
                  rows="3"
                  class="mb-3"
                ></v-textarea>

                <v-text-field
                  v-model.number="settingsForm.time_limit"
                  label="Время (минуты)"
                  variant="outlined"
                  density="compact"
                  type="number"
                  hint="Оставьте пустым для безлимитного"
                  class="mb-3"
                ></v-text-field>

                <v-text-field
                  v-model.number="settingsForm.passing_score"
                  label="Проходной балл (%)"
                  variant="outlined"
                  density="compact"
                  type="number"
                  min="0"
                  max="100"
                  class="mb-3"
                ></v-text-field>

                <v-text-field
                  v-model.number="settingsForm.max_attempts"
                  label="Максимум попыток"
                  variant="outlined"
                  density="compact"
                  type="number"
                  min="1"
                  class="mb-3"
                ></v-text-field>

                <v-switch
                  v-model="settingsForm.is_active"
                  label="Активен"
                  color="success"
                  class="mb-3"
                ></v-switch>

                <v-btn
                  type="submit"
                  color="primary"
                  block
                  :loading="settingsForm.processing"
                >
                  Сохранить настройки
                </v-btn>
              </v-form>
            </v-card-text>
          </v-card>

          <!-- Статистика -->
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-chart-bar</v-icon>
              Статистика
            </v-card-title>
            <v-card-text>
              <v-list>
                <v-list-item>
                  <template v-slot:prepend>
                    <v-icon color="primary">mdi-help-circle</v-icon>
                  </template>
                  <v-list-item-title>Вопросов</v-list-item-title>
                  <v-list-item-subtitle>{{ test.questions.length }}</v-list-item-subtitle>
                </v-list-item>
                <v-list-item>
                  <template v-slot:prepend>
                    <v-icon :color="test.is_active ? 'success' : 'warning'">
                      mdi-{{ test.is_active ? 'check-circle' : 'alert-circle' }}
                    </v-icon>
                  </template>
                  <v-list-item-title>Статус</v-list-item-title>
                  <v-list-item-subtitle>
                    {{ test.is_active ? 'Активен' : 'Неактивен' }}
                  </v-list-item-subtitle>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Вопросы -->
        <v-col cols="12" md="8">
          <v-card>
            <v-card-title class="text-h6 d-flex justify-space-between align-center">
              <div>
                <v-icon start>mdi-help-circle</v-icon>
                Вопросы ({{ test.questions.length }})
              </div>
              <v-btn
                color="primary"
                @click="showQuestionDialog = true; editingQuestion = null; resetQuestionForm()"
                prepend-icon="mdi-plus"
                size="small"
              >
                Добавить вопрос
              </v-btn>
            </v-card-title>
            <v-card-text>
              <div v-if="test.questions.length === 0" class="text-center py-8">
                <v-icon size="64" color="grey-lighten-1" class="mb-4">mdi-help-circle-outline</v-icon>
                <h3 class="text-h6 text-grey">Нет вопросов</h3>
                <p class="text-body-2 text-grey">Добавьте первый вопрос для теста</p>
                <v-btn
                  color="primary"
                  @click="showQuestionDialog = true; editingQuestion = null; resetQuestionForm()"
                  class="mt-4"
                  prepend-icon="mdi-plus"
                >
                  Добавить вопрос
                </v-btn>
              </div>

              <v-expansion-panels v-else variant="accordion">
                <v-expansion-panel
                  v-for="(question, index) in test.questions"
                  :key="question.id"
                >
                  <v-expansion-panel-title>
                    <div class="d-flex align-center w-100">
                      <v-chip size="small" color="primary" class="mr-3">
                        {{ index + 1 }}
                      </v-chip>
                      <span class="flex-grow-1">{{ question.question }}</span>
                      <v-chip size="small" variant="tonal" class="mr-2">
                        {{ getQuestionTypeText(question.type) }}
                      </v-chip>
                      <v-chip size="small" color="info" variant="tonal">
                        {{ question.answers.length }} ответов
                      </v-chip>
                    </div>
                  </v-expansion-panel-title>
                  <v-expansion-panel-text>
                    <!-- Ответы -->
                    <v-list density="compact">
                      <v-list-item
                        v-for="(answer, ansIndex) in question.answers"
                        :key="answer.id"
                      >
                        <template v-slot:prepend>
                          <v-icon :color="answer.is_correct ? 'success' : 'grey'">
                            {{ answer.is_correct ? 'mdi-check-circle' : 'mdi-circle-outline' }}
                          </v-icon>
                        </template>
                        <v-list-item-title :class="{ 'text-success font-weight-bold': answer.is_correct }">
                          {{ ansIndex + 1 }}. {{ answer.answer }}
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>

                    <v-divider class="my-3"></v-divider>

                    <div v-if="question.explanation" class="text-caption text-medium-emphasis mb-3">
                      <strong>Объяснение:</strong> {{ question.explanation }}
                    </div>

                    <div class="d-flex gap-2">
                      <v-btn
                        size="small"
                        color="primary"
                        variant="tonal"
                        @click="editQuestion(question)"
                        prepend-icon="mdi-pencil"
                      >
                        Редактировать
                      </v-btn>
                      <v-btn
                        size="small"
                        color="error"
                        variant="tonal"
                        @click="deleteQuestion(question)"
                        prepend-icon="mdi-delete"
                      >
                        Удалить
                      </v-btn>
                    </div>
                  </v-expansion-panel-text>
                </v-expansion-panel>
              </v-expansion-panels>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Диалог добавления/редактирования вопроса -->
      <v-dialog v-model="showQuestionDialog" max-width="800" scrollable>
        <v-card>
          <v-card-title>
            <v-icon start>mdi-{{ editingQuestion ? 'pencil' : 'plus' }}</v-icon>
            {{ editingQuestion ? 'Редактировать вопрос' : 'Добавить вопрос' }}
          </v-card-title>
          <v-card-text>
            <v-form @submit.prevent="saveQuestion">
              <v-textarea
                v-model="questionForm.question"
                label="Текст вопроса *"
                variant="outlined"
                rows="3"
                class="mb-3"
                :rules="[v => !!v || 'Обязательное поле']"
              ></v-textarea>

              <v-select
                v-model="questionForm.type"
                :items="[
                  { title: 'Одиночный выбор', value: 'single_choice' },
                  { title: 'Множественный выбор', value: 'multiple_choice' },
                  { title: 'Сопоставление', value: 'matching' }
                ]"
                item-title="title"
                item-value="value"
                label="Тип вопроса *"
                variant="outlined"
                class="mb-3"
                :rules="[v => !!v || 'Обязательное поле']"
              ></v-select>

              <v-textarea
                v-model="questionForm.explanation"
                label="Объяснение (опционально)"
                variant="outlined"
                rows="2"
                class="mb-3"
                hint="Будет показано студентам после прохождения"
              ></v-textarea>

              <v-divider class="my-4"></v-divider>

              <div class="d-flex justify-space-between align-center mb-3">
                <h3 class="text-h6">Ответы (минимум 3)</h3>
                <v-btn
                  size="small"
                  color="primary"
                  variant="tonal"
                  @click="addAnswer"
                  prepend-icon="mdi-plus"
                >
                  Добавить ответ
                </v-btn>
              </div>

              <div
                v-for="(answer, index) in questionForm.answers"
                :key="index"
                class="mb-3 pa-3 border rounded"
              >
                <div class="d-flex align-center gap-2">
                  <v-checkbox
                    v-model="answer.is_correct"
                    label="Правильный"
                    hide-details
                    density="compact"
                  ></v-checkbox>
                  <v-text-field
                    v-model="answer.answer"
                    :label="`Ответ ${index + 1} *`"
                    variant="outlined"
                    density="compact"
                    hide-details
                    class="flex-grow-1"
                  ></v-text-field>
                  <v-btn
                    v-if="questionForm.answers.length > 3"
                    icon="mdi-delete"
                    size="small"
                    color="error"
                    variant="text"
                    @click="removeAnswer(index)"
                  ></v-btn>
                </div>
              </div>

              <v-alert
                v-if="questionForm.answers.length < 3"
                type="warning"
                variant="tonal"
                class="mt-3"
              >
                Необходимо минимум 3 ответа
              </v-alert>
            </v-form>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn @click="showQuestionDialog = false">Отмена</v-btn>
            <v-btn
              color="primary"
              @click="saveQuestion"
              :disabled="questionForm.answers.length < 3 || !questionForm.question || !questionForm.type"
              :loading="questionForm.processing"
            >
              {{ editingQuestion ? 'Сохранить' : 'Добавить' }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </Layout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import Layout from '../../Layout.vue'

const page = usePage()

const props = defineProps({
  test: {
    type: Object,
    required: true
  }
})

// Состояние
const showQuestionDialog = ref(false)
const editingQuestion = ref(null)

// Форма настроек
const settingsForm = useForm({
  title: props.test.title,
  description: props.test.description,
  time_limit: props.test.time_limit,
  passing_score: props.test.passing_score,
  max_attempts: props.test.max_attempts,
  is_active: props.test.is_active
})

// Форма вопроса
const questionForm = reactive({
  question: '',
  type: 'single_choice',
  explanation: '',
  answers: [
    { answer: '', is_correct: true },
    { answer: '', is_correct: false },
    { answer: '', is_correct: false }
  ],
  processing: false
})

// Методы
const navigateTo = (route) => {
  window.location.href = route
}

const saveSettings = () => {
  settingsForm.put(`/teacher/tests/${props.test.id}`, {
    preserveScroll: true
  })
}

const toggleStatus = () => {
  const form = useForm({})
  form.post(`/teacher/tests/${props.test.id}/toggle-status`, {
    preserveScroll: true
  })
}

const resetQuestionForm = () => {
  questionForm.question = ''
  questionForm.type = 'single_choice'
  questionForm.explanation = ''
  questionForm.answers = [
    { answer: '', is_correct: true },
    { answer: '', is_correct: false },
    { answer: '', is_correct: false }
  ]
}

const addAnswer = () => {
  questionForm.answers.push({ answer: '', is_correct: false })
}

const removeAnswer = (index) => {
  if (questionForm.answers.length > 3) {
    questionForm.answers.splice(index, 1)
  }
}

const editQuestion = (question) => {
  editingQuestion.value = question
  questionForm.question = question.question
  questionForm.type = question.type
  questionForm.explanation = question.explanation || ''
  questionForm.answers = question.answers.map(a => ({
    id: a.id,
    answer: a.answer,
    is_correct: a.is_correct
  }))
  showQuestionDialog.value = true
}

const saveQuestion = () => {
  if (questionForm.answers.length < 3) return

  questionForm.processing = true
  const form = useForm(questionForm)

  if (editingQuestion.value) {
    // Обновление
    form.put(`/teacher/tests/${props.test.id}/questions/${editingQuestion.value.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        showQuestionDialog.value = false
        resetQuestionForm()
        editingQuestion.value = null
      },
      onFinish: () => {
        questionForm.processing = false
      }
    })
  } else {
    // Создание
    form.post(`/teacher/tests/${props.test.id}/questions`, {
      preserveScroll: true,
      onSuccess: () => {
        showQuestionDialog.value = false
        resetQuestionForm()
      },
      onFinish: () => {
        questionForm.processing = false
      }
    })
  }
}

const deleteQuestion = (question) => {
  if (confirm('Вы уверены, что хотите удалить этот вопрос?')) {
    const form = useForm({})
    form.delete(`/teacher/tests/${props.test.id}/questions/${question.id}`, {
      preserveScroll: true
    })
  }
}

const getQuestionTypeText = (type) => {
  const types = {
    'single_choice': 'Одиночный выбор',
    'multiple_choice': 'Множественный выбор',
    'matching': 'Сопоставление'
  }
  return types[type] || type
}
</script>

<style scoped>
.border {
  border: 1px solid #e0e0e0;
}
</style>
