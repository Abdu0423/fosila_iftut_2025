<template>
  <Layout role="teacher">
    <v-container fluid>
      <!-- Заголовок -->
      <v-row>
        <v-col cols="12">
          <div class="d-flex justify-space-between align-center mb-6">
            <div>
              <h1 class="text-h4 font-weight-bold mb-2">Мое расписание</h1>
              <p class="text-body-1 text-medium-emphasis">Управляйте своими расписаниями, силлабусами и уроками</p>
            </div>
            
          </div>
        </v-col>
      </v-row>

      <!-- Статистика -->
      <v-row class="mb-6">
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="primary" class="mb-4">mdi-calendar-clock</v-icon>
              <div class="text-h4 font-weight-bold">{{ schedules.length }}</div>
              <div class="text-body-2 text-medium-emphasis">Расписаний</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="success" class="mb-4">mdi-file-document-multiple</v-icon>
              <div class="text-h4 font-weight-bold">{{ totalSyllabuses }}</div>
              <div class="text-body-2 text-medium-emphasis">Силлабусов</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="info" class="mb-4">mdi-teach</v-icon>
              <div class="text-h4 font-weight-bold">{{ totalLessons }}</div>
              <div class="text-body-2 text-medium-emphasis">Уроков</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <v-icon size="48" color="warning" class="mb-4">mdi-account-group</v-icon>
              <div class="text-h4 font-weight-bold">{{ totalGroups }}</div>
              <div class="text-body-2 text-medium-emphasis">Групп</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Список расписаний -->
      <v-row>
        <v-col cols="12">
          <v-card>
            <v-card-title class="text-h6">
              <v-icon start>mdi-calendar-clock</v-icon>
              Мои расписания
            </v-card-title>
            <v-card-text class="pa-0">
              <v-list>
                <v-list-item
                  v-for="schedule in schedules"
                  :key="schedule.id"
                  class="schedule-item"
                >
                  <template v-slot:prepend>
                    <v-avatar color="primary" size="48">
                      <v-icon color="white">mdi-calendar-clock</v-icon>
                    </v-avatar>
                  </template>

                  <v-list-item-title class="text-h6">
                    {{ schedule.subject?.name || 'Без предмета' }}
                  </v-list-item-title>
                  
                  <v-list-item-subtitle class="text-body-2">
                    <div class="d-flex align-center mb-1">
                      <v-icon size="16" class="mr-1">mdi-account-group</v-icon>
                      <span>{{ schedule.group?.name || 'Без группы' }}</span>
                    </div>
                    <div class="d-flex align-center mb-1">
                      <v-icon size="16" class="mr-1">mdi-calendar-range</v-icon>
                      <span>{{ formatDateRange(schedule.start_date, schedule.end_date) }}</span>
                    </div>
                    <div class="d-flex align-center">
                      <v-icon size="16" class="mr-1">mdi-file-document-multiple</v-icon>
                      <span>{{ schedule.syllabuses?.length || 0 }} силлабусов</span>
                      <v-icon size="16" class="mr-1 ml-4">mdi-teach</v-icon>
                      <span>{{ schedule.lessons?.length || 0 }} уроков</span>
                    </div>
                  </v-list-item-subtitle>

                  <template v-slot:append>
                    <div class="d-flex align-center">
                      <v-chip
                        :color="schedule.is_active ? 'success' : 'grey'"
                        size="small"
                        class="mr-2"
                      >
                        {{ schedule.is_active ? 'Активно' : 'Неактивно' }}
                      </v-chip>
                      
                      <v-menu>
                        <template v-slot:activator="{ props }">
                          <v-btn
                            icon="mdi-dots-vertical"
                            variant="text"
                            v-bind="props"
                          ></v-btn>
                        </template>
                        <v-list>
                          <v-list-item @click="viewSchedule(schedule)">
                            <template v-slot:prepend>
                              <v-icon>mdi-eye</v-icon>
                            </template>
                            <v-list-item-title>Просмотр</v-list-item-title>
                          </v-list-item>
                          <v-list-item @click="manageSyllabuses(schedule)">
                            <template v-slot:prepend>
                              <v-icon>mdi-file-document-multiple</v-icon>
                            </template>
                            <v-list-item-title>Силлабусы</v-list-item-title>
                          </v-list-item>
                          <v-list-item @click="editSchedule(schedule)">
                            <template v-slot:prepend>
                              <v-icon>mdi-pencil</v-icon>
                            </template>
                            <v-list-item-title>Редактировать</v-list-item-title>
                          </v-list-item>
                        </v-list>
                      </v-menu>
                    </div>
                  </template>
                </v-list-item>

                <v-list-item v-if="schedules.length === 0" class="text-center">
                  <v-list-item-title class="text-medium-emphasis">
                    У вас пока нет расписаний
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </Layout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import Layout from '../../Layout.vue'

// Props
const props = defineProps({
  schedules: {
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
  syllabuses: {
    type: Array,
    default: () => []
  },
  lessons: {
    type: Array,
    default: () => []
  }
})

// Состояние
const showCreateDialog = ref(false)
const selectedSchedule = ref(null)

// Форма для нового расписания
const newScheduleForm = useForm({
  subject_id: null,
  group_id: null,
  start_date: '',
  end_date: '',
  description: ''
})

// Вычисляемые свойства
const totalSyllabuses = computed(() => {
  return props.schedules.reduce((total, schedule) => total + (schedule.syllabuses?.length || 0), 0)
})

const totalLessons = computed(() => {
  return props.schedules.reduce((total, schedule) => total + (schedule.lessons?.length || 0), 0)
})

const totalGroups = computed(() => {
  const uniqueGroups = new Set(props.schedules.map(schedule => schedule.group_id).filter(Boolean))
  return uniqueGroups.size
})

// Методы
const createSchedule = () => {
  newScheduleForm.post('/teacher/schedule', {
    onSuccess: () => {
      showCreateDialog.value = false
      newScheduleForm.reset()
    }
  })
}

const viewSchedule = (schedule) => {
  router.visit(`/teacher/schedule/${schedule.id}`)
}

const editSchedule = (schedule) => {
  router.visit(`/teacher/schedule/${schedule.id}`)
}

const manageSyllabuses = (schedule) => {
  router.visit(`/teacher/schedule/${schedule.id}`)
}

const formatDateRange = (startDate, endDate) => {
  const start = new Date(startDate).toLocaleDateString('ru-RU')
  const end = new Date(endDate).toLocaleDateString('ru-RU')
  return `${start} - ${end}`
}

// Жизненный цикл
onMounted(() => {
  console.log('Страница расписания учителя загружена')
})
</script>

<style scoped>
.schedule-item {
  border-bottom: 1px solid rgb(var(--v-theme-outline-variant));
}

.schedule-item:last-child {
  border-bottom: none;
}
</style>
