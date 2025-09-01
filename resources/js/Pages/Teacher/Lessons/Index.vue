<template>
  <TeacherApp>
    <v-container fluid>
      <!-- Заголовок -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold mb-2">Мои уроки</h1>
              <p class="text-body-1 text-medium-emphasis">Просмотр ваших уроков и студентов</p>
            </div>
            <div class="d-flex gap-2">
              <v-btn
                color="info"
                @click="navigateTo('/teacher/students')"
                prepend-icon="mdi-account-group"
              >
                Мои студенты
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
              <div class="text-body-2 text-medium-emphasis">Всего уроков</div>
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
                    v-model="selectedSubject"
                    :items="subjects"
                    item-title="name"
                    item-value="id"
                    label="Предмет"
                    variant="outlined"
                    density="compact"
                    clearable
                  ></v-select>
                </v-col>
                <v-col cols="12" md="3">
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

      <!-- Список уроков -->
      <v-row>
        <v-col cols="12">
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-teach</v-icon>
              Уроки ({{ filteredLessons.length }})
            </v-card-title>
            <v-card-text>
              <div v-if="filteredLessons.length === 0" class="text-center py-8">
                <v-icon size="64" color="grey-lighten-1" class="mb-4">mdi-teach-outline</v-icon>
                <h3 class="text-h6 text-grey">Нет уроков</h3>
                <p class="text-body-2 text-grey">У вас пока нет запланированных уроков</p>
              </div>
              
              <div v-else>
                <v-list>
                  <v-list-item
                    v-for="lesson in filteredLessons"
                    :key="lesson.id"
                    class="mb-4"
                  >
                    <template v-slot:prepend>
                      <v-avatar color="primary" size="40">
                        <v-icon color="white">mdi-teach</v-icon>
                      </v-avatar>
                    </template>
                    
                    <v-list-item-title class="font-weight-medium mb-2">
                      {{ lesson.title }}
                    </v-list-item-title>
                    
                                         <v-list-item-subtitle>
                       <div class="d-flex align-center gap-4 mb-2">
                         <v-chip
                           color="primary"
                           size="small"
                           variant="tonal"
                         >
                           {{ lesson.subject_name }}
                         </v-chip>
                         
                         <v-chip
                           color="secondary"
                           size="small"
                           variant="tonal"
                         >
                           {{ lesson.group_name }}
                         </v-chip>
                         
                         <v-chip
                           color="info"
                           size="small"
                           variant="tonal"
                         >
                           {{ getDayOfWeekText(lesson.day_of_week) }}
                         </v-chip>
                         
                         <v-chip
                           :color="lesson.is_active ? 'success' : 'warning'"
                           size="small"
                           variant="tonal"
                         >
                           {{ lesson.is_active ? 'Активен' : 'Неактивен' }}
                         </v-chip>
                       </div>
                       
                       <p v-if="lesson.description" class="text-body-2 mb-2">
                         {{ lesson.description }}
                       </p>
                       
                       <div class="d-flex align-center gap-4 text-caption text-medium-emphasis">
                         <span v-if="lesson.start_time && lesson.end_time">
                           <v-icon size="16" class="mr-1">mdi-clock</v-icon>
                           {{ lesson.start_time }} - {{ lesson.end_time }}
                         </span>
                         <span v-if="lesson.room">
                           <v-icon size="16" class="mr-1">mdi-map-marker</v-icon>
                           {{ lesson.room }}
                         </span>
                         <span v-if="lesson.semester">
                           <v-icon size="16" class="mr-1">mdi-school</v-icon>
                           Семестр {{ lesson.semester }}
                         </span>
                         <span v-if="lesson.credits">
                           <v-icon size="16" class="mr-1">mdi-credit-card</v-icon>
                           {{ lesson.credits }} кр.
                         </span>
                         <span>
                           <v-icon size="16" class="mr-1">mdi-calendar</v-icon>
                           {{ lesson.created_at }}
                         </span>
                       </div>
                     </v-list-item-subtitle>
                    
                    <template v-slot:append>
                      <v-btn
                        color="primary"
                        variant="text"
                        size="small"
                        @click="navigateTo(`/teacher/lessons/${lesson.id}`)"
                        prepend-icon="mdi-eye"
                      >
                        Просмотр
                      </v-btn>
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
import { ref, computed, onMounted } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import TeacherApp from '../TeacherApp.vue'

const page = usePage()

// Props из Inertia
const props = defineProps({
  lessons: {
    type: Array,
    default: () => []
  },
  subjects: {
    type: Array,
    default: () => []
  },
  groups: {
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
const selectedSubject = ref(null)
const selectedGroup = ref(null)
const selectedStatus = ref('')

// Вычисляемые свойства
const filteredLessons = computed(() => {
  let filtered = props.lessons

  // Поиск по названию
  if (search.value) {
    filtered = filtered.filter(lesson =>
      lesson.title.toLowerCase().includes(search.value.toLowerCase())
    )
  }

  // Фильтр по предмету
  if (selectedSubject.value) {
    filtered = filtered.filter(lesson => lesson.subject_id === selectedSubject.value)
  }

  // Фильтр по группе
  if (selectedGroup.value) {
    filtered = filtered.filter(lesson => lesson.group_id === selectedGroup.value)
  }

  // Фильтр по статусу
  if (selectedStatus.value === 'active') {
    filtered = filtered.filter(lesson => lesson.is_active)
  } else if (selectedStatus.value === 'inactive') {
    filtered = filtered.filter(lesson => !lesson.is_active)
  }

  return filtered
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
