<template>
  <Layout role="admin">
    <v-row>
      <v-col cols="12">
        <div class="d-flex justify-space-between align-center mb-4">
          <h1 class="text-h4">{{ lesson.title }}</h1>
          <div class="d-flex gap-2">
            <v-btn
              color="warning"
              :href="route('admin.lessons.edit', lesson.id)"
              prepend-icon="mdi-pencil"
            >
              Редактировать
            </v-btn>
            <v-btn
              variant="outlined"
              :href="route('admin.lessons.index')"
              prepend-icon="mdi-arrow-left"
            >
              Назад к списку
            </v-btn>
          </div>
        </div>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12" md="8">
        <v-card>
          <v-card-title>Информация об уроке</v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="12" md="6">
                <v-list>
                  <v-list-item>
                    <v-list-item-title class="font-weight-bold">Название</v-list-item-title>
                    <v-list-item-subtitle>{{ lesson.title }}</v-list-item-subtitle>
                  </v-list-item>
                  
                  <v-list-item>
                    <v-list-item-title class="font-weight-bold">Курс</v-list-item-title>
                    <v-list-item-subtitle>
                      <v-chip color="primary" size="small">
                        {{ lesson.course }} курс
                      </v-chip>
                    </v-list-item-subtitle>
                  </v-list-item>
                  
                  <v-list-item>
                    <v-list-item-title class="font-weight-bold">Кафедра</v-list-item-title>
                    <v-list-item-subtitle>
                      {{ lesson.department?.name || 'Не указана' }}
                    </v-list-item-subtitle>
                  </v-list-item>
                  
                  <v-list-item>
                    <v-list-item-title class="font-weight-bold">Статус</v-list-item-title>
                    <v-list-item-subtitle>
                      <v-chip
                        :color="lesson.is_active ? 'success' : 'error'"
                        size="small"
                      >
                        {{ lesson.is_active ? 'Активен' : 'Неактивен' }}
                      </v-chip>
                    </v-list-item-subtitle>
                  </v-list-item>
                </v-list>
              </v-col>
              
              <v-col cols="12" md="6">
                <v-list>
                  <v-list-item>
                    <v-list-item-title class="font-weight-bold">ID урока</v-list-item-title>
                    <v-list-item-subtitle>{{ lesson.id }}</v-list-item-subtitle>
                  </v-list-item>
                  
                  <v-list-item>
                    <v-list-item-title class="font-weight-bold">Дата создания</v-list-item-title>
                    <v-list-item-subtitle>{{ formatDate(lesson.created_at) }}</v-list-item-subtitle>
                  </v-list-item>
                  
                  <v-list-item>
                    <v-list-item-title class="font-weight-bold">Последнее обновление</v-list-item-title>
                    <v-list-item-subtitle>{{ formatDate(lesson.updated_at) }}</v-list-item-subtitle>
                  </v-list-item>
                </v-list>
              </v-col>
            </v-row>
            
            <v-divider class="my-4"></v-divider>
            
            <div v-if="lesson.content">
              <h3 class="text-h6 mb-2">Описание урока</h3>
              <p class="text-body-1">{{ lesson.content }}</p>
            </div>
            <div v-else>
              <v-alert type="info">
                Описание урока не указано
              </v-alert>
            </div>
          </v-card-text>
        </v-card>

        <!-- Расписание -->
        <v-card class="mt-4" v-if="lesson.schedules && lesson.schedules.length">
          <v-card-title>Расписание занятий</v-card-title>
          <v-card-text>
            <v-data-table
              :headers="scheduleHeaders"
              :items="lesson.schedules"
              :items-per-page="5"
            >
              <template v-slot:item.teacher="{ item }">
                {{ item.raw.teacher?.name || 'Не назначен' }}
              </template>
              
              <template v-slot:item.group="{ item }">
                {{ item.raw.group?.name || 'Не указана' }}
              </template>
              
              <template v-slot:item.day_of_week="{ item }">
                {{ getDayName(item.raw.day_of_week) }}
              </template>
              
              <template v-slot:item.time="{ item }">
                {{ item.raw.time }}
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card>
          <v-card-title>Действия</v-card-title>
          <v-card-text>
            <v-btn
              block
              color="warning"
              :href="route('admin.lessons.edit', lesson.id)"
              prepend-icon="mdi-pencil"
              class="mb-2"
            >
              Редактировать урок
            </v-btn>
            
            <v-btn
              block
              color="info"
              :href="route('admin.schedules.create')"
              prepend-icon="mdi-calendar-plus"
              class="mb-2"
            >
              Добавить в расписание
            </v-btn>
            
            <v-btn
              block
              color="error"
              variant="outlined"
              prepend-icon="mdi-delete"
              @click="deleteLesson"
            >
              Удалить урок
            </v-btn>
          </v-card-text>
        </v-card>

        <v-card class="mt-4">
          <v-card-title>Статистика</v-card-title>
          <v-card-text>
            <v-list>
              <v-list-item>
                <v-list-item-title>Занятий в расписании</v-list-item-title>
                <v-list-item-subtitle>
                  {{ lesson.schedules?.length || 0 }}
                </v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Диалог подтверждения удаления -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card>
        <v-card-title>Подтверждение удаления</v-card-title>
        <v-card-text>
          Вы действительно хотите удалить урок "{{ lesson.title }}"?
          Это действие нельзя отменить.
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="deleteDialog = false">Отмена</v-btn>
          <v-btn color="error" @click="confirmDelete">Удалить</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </Layout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Layout from '../../Layout.vue'

const props = defineProps({
  lesson: Object,
})

const deleteDialog = ref(false)

const scheduleHeaders = [
  { title: 'Учитель', key: 'teacher' },
  { title: 'Группа', key: 'group' },
  { title: 'День недели', key: 'day_of_week' },
  { title: 'Время', key: 'time' },
]

const deleteLesson = () => {
  deleteDialog.value = true
}

const confirmDelete = () => {
  router.delete(route('admin.lessons.destroy', props.lesson.id), {
    onSuccess: () => {
      deleteDialog.value = false
    }
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ru-RU', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getDayName = (dayNumber) => {
  const days = [
    'Понедельник',
    'Вторник',
    'Среда',
    'Четверг',
    'Пятница',
    'Суббота',
    'Воскресенье'
  ]
  return days[dayNumber - 1] || 'Неизвестно'
}
</script>
