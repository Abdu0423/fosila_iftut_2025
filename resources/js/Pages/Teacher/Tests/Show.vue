<template>
  <Layout role="teacher">
    <v-container fluid>
      <!-- Заголовок -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold mb-2">{{ test.title }}</h1>
              <p class="text-body-1 text-medium-emphasis">{{ test.lesson_title }} - Просмотр теста</p>
            </div>
            <div class="d-flex gap-2">
              <v-btn
                color="primary"
                @click="navigateTo(`/teacher/tests/${test.id}/edit`)"
                prepend-icon="mdi-pencil"
              >
                Редактировать
              </v-btn>
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
        <!-- Информация о тесте -->
        <v-col cols="12" md="4">
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-information</v-icon>
              Информация о тесте
            </v-card-title>
            <v-card-text>
              <v-list>
                <v-list-item>
                  <template v-slot:prepend>
                    <v-icon color="primary">mdi-clipboard-text</v-icon>
                  </template>
                  <v-list-item-title>Название</v-list-item-title>
                  <v-list-item-subtitle>{{ test.title }}</v-list-item-subtitle>
                </v-list-item>
                
                <v-list-item>
                  <template v-slot:prepend>
                    <v-icon color="secondary">mdi-teach</v-icon>
                  </template>
                  <v-list-item-title>Урок</v-list-item-title>
                  <v-list-item-subtitle>{{ test.lesson_title }}</v-list-item-subtitle>
                </v-list-item>
                
                <v-list-item>
                  <template v-slot:prepend>
                    <v-icon color="info">mdi-tag</v-icon>
                  </template>
                  <v-list-item-title>Тип</v-list-item-title>
                  <v-list-item-subtitle>{{ getTypeText(test.type) }}</v-list-item-subtitle>
                </v-list-item>
                
                <v-list-item v-if="test.time_limit">
                  <template v-slot:prepend>
                    <v-icon color="warning">mdi-clock</v-icon>
                  </template>
                  <v-list-item-title>Время</v-list-item-title>
                  <v-list-item-subtitle>{{ test.time_limit }} минут</v-list-item-subtitle>
                </v-list-item>
                
                <v-list-item v-if="test.passing_score">
                  <template v-slot:prepend>
                    <v-icon color="success">mdi-percent</v-icon>
                  </template>
                  <v-list-item-title>Проходной балл</v-list-item-title>
                  <v-list-item-subtitle>{{ test.passing_score }}%</v-list-item-subtitle>
                </v-list-item>
                
                <v-list-item v-if="test.max_attempts">
                  <template v-slot:prepend>
                    <v-icon color="info">mdi-repeat</v-icon>
                  </template>
                  <v-list-item-title>Попытки</v-list-item-title>
                  <v-list-item-subtitle>{{ test.max_attempts }}</v-list-item-subtitle>
                </v-list-item>
                
                <v-list-item>
                  <template v-slot:prepend>
                    <v-icon :color="test.is_active ? 'success' : 'warning'">mdi-toggle-switch</v-icon>
                  </template>
                  <v-list-item-title>Статус</v-list-item-title>
                  <v-list-item-subtitle>
                    <v-chip
                      :color="test.is_active ? 'success' : 'warning'"
                      size="small"
                      variant="tonal"
                    >
                      {{ test.is_active ? 'Активен' : 'Неактивен' }}
                    </v-chip>
                  </v-list-item-subtitle>
                </v-list-item>
                
                <v-list-item>
                  <template v-slot:prepend>
                    <v-icon :color="test.is_public ? 'info' : 'grey'">mdi-earth</v-icon>
                  </template>
                  <v-list-item-title>Доступ</v-list-item-title>
                  <v-list-item-subtitle>
                    <v-chip
                      :color="test.is_public ? 'info' : 'grey'"
                      size="small"
                      variant="tonal"
                    >
                      {{ test.is_public ? 'Публичный' : 'Приватный' }}
                    </v-chip>
                  </v-list-item-subtitle>
                </v-list-item>
                
                <v-list-item v-if="test.start_date">
                  <template v-slot:prepend>
                    <v-icon color="success">mdi-calendar-start</v-icon>
                  </template>
                  <v-list-item-title>Начало</v-list-item-title>
                  <v-list-item-subtitle>{{ test.start_date }}</v-list-item-subtitle>
                </v-list-item>
                
                <v-list-item v-if="test.end_date">
                  <template v-slot:prepend>
                    <v-icon color="error">mdi-calendar-end</v-icon>
                  </template>
                  <v-list-item-title>Окончание</v-list-item-title>
                  <v-list-item-subtitle>{{ test.end_date }}</v-list-item-subtitle>
                </v-list-item>
                
                <v-list-item>
                  <template v-slot:prepend>
                    <v-icon color="grey">mdi-calendar-clock</v-icon>
                  </template>
                  <v-list-item-title>Создан</v-list-item-title>
                  <v-list-item-subtitle>{{ test.created_at }}</v-list-item-subtitle>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>
        
        <!-- Вопросы теста -->
        <v-col cols="12" md="8">
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-help-circle</v-icon>
              Вопросы теста ({{ test.questions.length }})
            </v-card-title>
            <v-card-text>
              <div v-if="test.questions.length === 0" class="text-center py-8">
                <v-icon size="64" color="grey-lighten-1" class="mb-4">mdi-help-circle-outline</v-icon>
                <h3 class="text-h6 text-grey">Нет вопросов</h3>
                <p class="text-body-2 text-grey">В этом тесте пока нет вопросов</p>
                <v-btn
                  color="primary"
                  @click="navigateTo(`/teacher/tests/${test.id}/questions`)"
                  class="mt-4"
                  prepend-icon="mdi-plus"
                >
                  Добавить вопросы
                </v-btn>
              </div>
              
              <div v-else>
                <v-expansion-panels variant="accordion">
                  <v-expansion-panel
                    v-for="(question, index) in test.questions"
                    :key="question.id"
                    class="mb-4"
                  >
                    <v-expansion-panel-title>
                      <div class="d-flex align-center justify-space-between w-100">
                        <div class="d-flex align-center">
                          <v-avatar color="primary" size="32" class="mr-3">
                            <span class="text-white font-weight-bold">{{ index + 1 }}</span>
                          </v-avatar>
                          <div>
                            <div class="font-weight-medium">{{ question.question }}</div>
                            <div class="text-caption text-medium-emphasis">{{ getQuestionTypeText(question.type) }}</div>
                          </div>
                        </div>
                        <div class="d-flex align-center gap-4">
                          <v-chip
                            color="info"
                            size="small"
                            variant="tonal"
                          >
                            {{ question.answers.length }} ответов
                          </v-chip>
                        </div>
                      </div>
                    </v-expansion-panel-title>
                    
                    <v-expansion-panel-text>
                      <div class="mb-4">
                        <h4 class="text-h6 mb-2">Вопрос:</h4>
                        <p class="text-body-1">{{ question.question }}</p>
                        
                        <div v-if="question.explanation" class="mt-3">
                          <h5 class="text-subtitle-1 mb-1">Объяснение:</h5>
                          <p class="text-body-2 text-medium-emphasis">{{ question.explanation }}</p>
                        </div>
                      </div>
                      
                      <div>
                        <h4 class="text-h6 mb-2">Ответы:</h4>
                        <v-list>
                          <v-list-item
                            v-for="(answer, answerIndex) in question.answers"
                            :key="answer.id"
                            class="mb-2"
                          >
                            <template v-slot:prepend>
                              <v-avatar size="24" :color="answer.is_correct ? 'success' : 'grey'">
                                <span class="text-white font-weight-bold text-caption">{{ answerIndex + 1 }}</span>
                              </v-avatar>
                            </template>
                            
                            <v-list-item-title class="text-body-2">
                              {{ answer.answer }}
                            </v-list-item-title>
                            
                            <template v-slot:append>
                              <v-chip
                                v-if="answer.is_correct"
                                color="success"
                                size="small"
                                variant="tonal"
                              >
                                Правильный
                              </v-chip>
                            </template>
                          </v-list-item>
                        </v-list>
                      </div>
                    </v-expansion-panel-text>
                  </v-expansion-panel>
                </v-expansion-panels>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </Layout>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import Layout from '../../Layout.vue'

const page = usePage()

// Props из Inertia
const props = defineProps({
  test: {
    type: Object,
    required: true
  }
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

const getQuestionTypeText = (type) => {
  const types = {
    'multiple_choice': 'Множественный выбор',
    'single_choice': 'Один ответ',
    'true_false': 'Верно/Неверно',
    'matching': 'Сопоставление',
    'text': 'Текстовый ответ'
  }
  return types[type] || type
}
</script>

<style scoped>
.v-card {
  border-radius: 12px;
}

.v-expansion-panel {
  border-radius: 12px;
  border: 1px solid #e0e0e0;
}

.v-list-item {
  border-radius: 8px;
}
</style>

