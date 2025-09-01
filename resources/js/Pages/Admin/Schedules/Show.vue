<template>
  <AdminApp>
    <v-container>
      <v-row>
        <v-col cols="12">
          <div class="d-flex align-center justify-space-between mb-6">
            <h1 class="text-h4 font-weight-bold">Детали расписания</h1>
            <div class="d-flex gap-2">
              <v-btn
                variant="outlined"
                prepend-icon="mdi-arrow-left"
                @click="navigateTo('/admin/schedules')"
              >
                Назад к списку
              </v-btn>
              <v-btn
                color="primary"
                prepend-icon="mdi-pencil"
                @click="navigateTo(`/admin/schedules/${schedule.id}/edit`)"
              >
                Редактировать
              </v-btn>
            </div>
          </div>

          <v-card>
            <v-card-title class="text-h5">
              Информация о расписании
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col cols="12" md="6">
                  <v-list>
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="primary">mdi-book-open-variant</v-icon>
                      </template>
                      <v-list-item-title>Урок</v-list-item-title>
                      <v-list-item-subtitle>{{ schedule.lesson_name }}</v-list-item-subtitle>
                    </v-list-item>

                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="primary">mdi-account-tie</v-icon>
                      </template>
                      <v-list-item-title>Преподаватель</v-list-item-title>
                      <v-list-item-subtitle>{{ schedule.teacher_name }}</v-list-item-subtitle>
                    </v-list-item>

                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="primary">mdi-account-group</v-icon>
                      </template>
                      <v-list-item-title>Группа</v-list-item-title>
                      <v-list-item-subtitle>{{ schedule.group_name }}</v-list-item-subtitle>
                    </v-list-item>
                  </v-list>
                </v-col>

                <v-col cols="12" md="6">
                  <v-list>
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="primary">mdi-calendar</v-icon>
                      </template>
                      <v-list-item-title>Семестр</v-list-item-title>
                      <v-list-item-subtitle>{{ schedule.semester }}</v-list-item-subtitle>
                    </v-list-item>

                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="primary">mdi-star</v-icon>
                      </template>
                      <v-list-item-title>Кредиты</v-list-item-title>
                      <v-list-item-subtitle>{{ schedule.credits }}</v-list-item-subtitle>
                    </v-list-item>

                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="primary">mdi-calendar-year</v-icon>
                      </template>
                      <v-list-item-title>Год обучения</v-list-item-title>
                      <v-list-item-subtitle>{{ schedule.study_year }}</v-list-item-subtitle>
                    </v-list-item>

                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="primary">mdi-sort-numeric-ascending</v-icon>
                      </template>
                      <v-list-item-title>Порядок</v-list-item-title>
                      <v-list-item-subtitle>{{ schedule.order }}</v-list-item-subtitle>
                    </v-list-item>

                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="primary">mdi-clock</v-icon>
                      </template>
                      <v-list-item-title>Запланированная дата</v-list-item-title>
                      <v-list-item-subtitle>{{ schedule.scheduled_at }}</v-list-item-subtitle>
                    </v-list-item>

                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon :color="schedule.is_active ? 'success' : 'error'">
                          {{ schedule.is_active ? 'mdi-check-circle' : 'mdi-close-circle' }}
                        </v-icon>
                      </template>
                      <v-list-item-title>Статус</v-list-item-title>
                      <v-list-item-subtitle>
                        <v-chip
                          :color="schedule.is_active ? 'success' : 'error'"
                          size="small"
                        >
                          {{ schedule.is_active ? 'Активно' : 'Неактивно' }}
                        </v-chip>
                      </v-list-item-subtitle>
                    </v-list-item>
                  </v-list>
                </v-col>
              </v-row>

              <v-divider class="my-4"></v-divider>

              <v-row>
                <v-col cols="12" md="6">
                  <v-list>
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="grey">mdi-calendar-plus</v-icon>
                      </template>
                      <v-list-item-title>Дата создания</v-list-item-title>
                      <v-list-item-subtitle>{{ schedule.created_at }}</v-list-item-subtitle>
                    </v-list-item>
                  </v-list>
                </v-col>

                <v-col cols="12" md="6">
                  <v-list>
                    <v-list-item>
                      <template v-slot:prepend>
                        <v-icon color="grey">mdi-calendar-edit</v-icon>
                      </template>
                      <v-list-item-title>Дата обновления</v-list-item-title>
                      <v-list-item-subtitle>{{ schedule.updated_at }}</v-list-item-subtitle>
                    </v-list-item>
                  </v-list>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </AdminApp>
</template>

<script setup>
import AdminApp from '../AdminApp.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  schedule: {
    type: Object,
    required: true
  }
})

const navigateTo = (url) => {
  router.visit(url)
}
</script>
