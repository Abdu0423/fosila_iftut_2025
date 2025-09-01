<template>
  <TeacherApp>
    <v-container fluid>
      <!-- Заголовок -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold mb-2">{{ lesson.title }}</h1>
              <p class="text-body-1 text-medium-emphasis">{{ lesson.subject_name }} - {{ lesson.group_name }} - Просмотр урока</p>
            </div>
            <div class="d-flex gap-2">
              <v-btn
                color="secondary"
                variant="outlined"
                @click="navigateTo('/teacher/lessons')"
                prepend-icon="mdi-arrow-left"
              >
                Назад к урокам
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
        <!-- Информация об уроке -->
        <v-col cols="12" md="8">
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-information</v-icon>
              Информация об уроке
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <v-list>
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="primary">mdi-book</v-icon>
                      </template>
                      <v-list-item-title>Предмет</v-list-item-title>
                      <v-list-item-subtitle>{{ lesson.subject_name }}</v-list-item-subtitle>
                    </v-list-item>
                    
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="secondary">mdi-account-group</v-icon>
                      </template>
                      <v-list-item-title>Группа</v-list-item-title>
                      <v-list-item-subtitle>{{ lesson.group_name }}</v-list-item-subtitle>
                    </v-list-item>
                    
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="info">mdi-calendar</v-icon>
                      </template>
                      <v-list-item-title>День недели</v-list-item-title>
                      <v-list-item-subtitle>{{ getDayOfWeekText(lesson.day_of_week) }}</v-list-item-subtitle>
                    </v-list-item>
                    
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="warning">mdi-clock</v-icon>
                      </template>
                      <v-list-item-title>Время</v-list-item-title>
                      <v-list-item-subtitle>{{ lesson.start_time }} - {{ lesson.end_time }}</v-list-item-subtitle>
                    </v-list-item>
                  </v-list>
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-list>
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="success">mdi-map-marker</v-icon>
                      </template>
                      <v-list-item-title>Аудитория</v-list-item-title>
                      <v-list-item-subtitle>{{ lesson.room || 'Не указана' }}</v-list-item-subtitle>
                    </v-list-item>
                    
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="primary">mdi-school</v-icon>
                      </template>
                      <v-list-item-title>Семестр</v-list-item-title>
                      <v-list-item-subtitle>{{ lesson.semester || 'Не указан' }}</v-list-item-subtitle>
                    </v-list-item>
                    
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="info">mdi-credit-card</v-icon>
                      </template>
                      <v-list-item-title>Кредиты</v-list-item-title>
                      <v-list-item-subtitle>{{ lesson.credits || 'Не указаны' }}</v-list-item-subtitle>
                    </v-list-item>
                    
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="warning">mdi-calendar-year</v-icon>
                      </template>
                      <v-list-item-title>Год обучения</v-list-item-title>
                      <v-list-item-subtitle>{{ lesson.study_year || 'Не указан' }}</v-list-item-subtitle>
                    </v-list-item>
                    
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="secondary">mdi-order-numeric-ascending</v-icon>
                      </template>
                      <v-list-item-title>Порядок</v-list-item-title>
                      <v-list-item-subtitle>{{ lesson.order || 'Не указан' }}</v-list-item-subtitle>
                    </v-list-item>
                    
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon :color="lesson.is_active ? 'success' : 'warning'">mdi-toggle-switch</v-icon>
                      </template>
                      <v-list-item-title>Статус</v-list-item-title>
                      <v-list-item-subtitle>
                        <v-chip
                          :color="lesson.is_active ? 'success' : 'warning'"
                          size="small"
                          variant="tonal"
                        >
                          {{ lesson.is_active ? 'Активен' : 'Неактивен' }}
                        </v-chip>
                      </v-list-item-subtitle>
                    </v-list-item>
                    
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="success">mdi-calendar-check</v-icon>
                      </template>
                      <v-list-item-title>Запланирован</v-list-item-title>
                      <v-list-item-subtitle>{{ lesson.scheduled_at || 'Не указана' }}</v-list-item-subtitle>
                    </v-list-item>
                    
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="grey">mdi-calendar-clock</v-icon>
                      </template>
                      <v-list-item-title>Создан</v-list-item-title>
                      <v-list-item-subtitle>{{ lesson.created_at }}</v-list-item-subtitle>
                    </v-list-item>
                  </v-list>
                </v-col>
              </v-row>
              
              <v-divider class="my-4"></v-divider>
              
              <div v-if="lesson.description">
                <h3 class="text-h6 mb-2">Описание урока</h3>
                <p class="text-body-1">{{ lesson.description }}</p>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
        
        <!-- Студенты группы -->
        <v-col cols="12" md="4">
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-account-group</v-icon>
              Студенты группы ({{ students.length }})
            </v-card-title>
            <v-card-text>
              <div v-if="students.length === 0" class="text-center py-4">
                <v-icon size="48" color="grey-lighten-1" class="mb-2">mdi-account-group-outline</v-icon>
                <p class="text-body-2 text-grey">В группе нет студентов</p>
              </div>
              
              <div v-else>
                <v-list>
                  <v-list-item
                    v-for="student in students"
                    :key="student.id"
                    class="mb-2"
                  >
                    <template v-slot:prepend>
                      <v-avatar size="32" color="primary">
                        <v-icon color="white" size="16">mdi-account</v-icon>
                      </v-avatar>
                    </template>
                    
                    <v-list-item-title class="text-body-2 font-weight-medium">
                      {{ student.full_name }}
                    </v-list-item-title>
                    
                    <v-list-item-subtitle class="text-caption">
                      <div>{{ student.email }}</div>
                      <div v-if="student.phone">{{ student.phone }}</div>
                    </v-list-item-subtitle>
                    
                    <template v-slot:append>
                      <v-btn
                        icon="mdi-eye"
                        variant="text"
                        size="small"
                        @click="viewStudent(student)"
                        color="primary"
                      ></v-btn>
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
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import TeacherApp from '../TeacherApp.vue'

const page = usePage()

// Props из Inertia
const props = defineProps({
  lesson: {
    type: Object,
    required: true
  },
  students: {
    type: Array,
    default: () => []
  }
})

// Методы
const navigateTo = (route) => {
  window.location.href = route
}

const getDayOfWeekText = (day) => {
  const days = {
    'monday': 'Понедельник',
    'tuesday': 'Вторник',
    'wednesday': 'Среда',
    'thursday': 'Четверг',
    'friday': 'Пятница',
    'saturday': 'Суббота',
    'sunday': 'Воскресенье'
  }
  return days[day] || day
}

const viewStudent = (student) => {
  // Переход на страницу просмотра студента
  navigateTo(`/teacher/students/student/${student.id}`)
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
