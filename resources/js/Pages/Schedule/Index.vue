<template>
  <App>
    <div class="schedule-page">
      <v-row>
        <v-col cols="12">
          <h1 class="text-h4 mb-6">Мое расписание</h1>
        </v-col>
      </v-row>
      
      <v-row>
        <v-col cols="12">
          <v-card>
            <v-card-title>
              Расписание на неделю
              <v-spacer></v-spacer>
              <v-btn color="primary" @click="currentWeek = new Date()">
                Текущая неделя
              </v-btn>
            </v-card-title>
            <v-card-text>
              <v-calendar
                v-model="selectedDate"
                :events="events"
                event-color="primary"
                type="week"
                :weekdays="[1, 2, 3, 4, 5, 6, 0]"
                :first-day-of-week="1"
              ></v-calendar>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      
      <v-row class="mt-6">
        <v-col cols="12">
          <v-card>
            <v-card-title>Ближайшие занятия</v-card-title>
            <v-card-text>
              <v-list>
                <v-list-item v-for="lesson in upcomingLessons" :key="lesson.id">
                  <v-list-item-avatar>
                    <v-icon color="primary">mdi-book-open-variant</v-icon>
                  </v-list-item-avatar>
                  <v-list-item-content>
                    <v-list-item-title>{{ lesson.course }}</v-list-item-title>
                    <v-list-item-subtitle>
                      {{ lesson.time }} | {{ lesson.room }} | {{ lesson.teacher }}
                    </v-list-item-subtitle>
                  </v-list-item-content>
                  <v-list-item-action>
                    <v-chip :color="lesson.type === 'Лекция' ? 'primary' : 'secondary'">
                      {{ lesson.type }}
                    </v-chip>
                  </v-list-item-action>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>
  </App>
</template>

<script>
import App from '../App.vue'

export default {
  name: 'ScheduleIndex',
  components: {
    App
  },
  data() {
    return {
      selectedDate: new Date(),
      currentWeek: new Date(),
      events: [
        {
          name: 'Введение в программирование',
          start: '2024-01-15 09:00',
          end: '2024-01-15 10:30',
          color: 'primary'
        },
        {
          name: 'Веб-разработка',
          start: '2024-01-15 11:00',
          end: '2024-01-15 12:30',
          color: 'secondary'
        }
      ],
      upcomingLessons: [
        {
          id: 1,
          course: 'Введение в программирование',
          time: 'Понедельник, 09:00-10:30',
          room: 'Аудитория 101',
          teacher: 'Иванов И.И.',
          type: 'Лекция'
        },
        {
          id: 2,
          course: 'Веб-разработка',
          time: 'Понедельник, 11:00-12:30',
          room: 'Аудитория 102',
          teacher: 'Петров П.П.',
          type: 'Практика'
        }
      ]
    }
  }
}
</script>
