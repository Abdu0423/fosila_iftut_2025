<template>
  <Layout>
    <v-container fluid>
      <div class="grades-page">
        <v-row>
        <v-col cols="12">
          <h1 class="text-h4 mb-6">Мои оценки</h1>
        </v-col>
      </v-row>
      
      <!-- Общая статистика -->
      <v-row>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <div class="text-h4 font-weight-bold primary--text">{{ averageGrade }}</div>
              <div class="text-subtitle-1">Средний балл</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <div class="text-h4 font-weight-bold success--text">{{ completedAssignments }}</div>
              <div class="text-subtitle-1">Завершенных заданий</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <div class="text-h4 font-weight-bold warning--text">{{ pendingAssignments }}</div>
              <div class="text-subtitle-1">Ожидающих оценку</div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text class="text-center">
              <div class="text-h4 font-weight-bold info--text">{{ totalCredits }}</div>
              <div class="text-subtitle-1">Заработано кредитов</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      
      <!-- Фильтры -->
      <v-row class="mt-6">
        <v-col cols="12" md="6">
          <v-select
            v-model="selectedCourse"
            :items="courses"
            label="Курс"
            outlined
            dense
            clearable
          ></v-select>
        </v-col>
        <v-col cols="12" md="6">
          <v-select
            v-model="selectedSemester"
            :items="semesters"
            label="Семестр"
            outlined
            dense
            clearable
          ></v-select>
        </v-col>
      </v-row>
      
      <!-- Оценки по курсам -->
      <v-row>
        <v-col cols="12">
          <v-card v-for="course in filteredCourses" :key="course.id" class="mb-4">
            <v-card-title class="d-flex align-center">
              <v-icon class="mr-3" color="primary">mdi-book-open-variant</v-icon>
              {{ course.name }}
              <v-spacer></v-spacer>
              <v-chip :color="getGradeColor(course.averageGrade)" class="font-weight-bold">
                {{ course.averageGrade }}
              </v-chip>
            </v-card-title>
            
            <v-card-text>
              <v-data-table
                :headers="gradeHeaders"
                :items="course.assignments"
                :items-per-page="10"
                class="elevation-0"
                hide-default-footer
              >
                <template v-slot:item.grade="{ item }">
                  <v-chip :color="getGradeColor(item.grade)" small>
                    {{ item.grade }}
                  </v-chip>
                </template>
                
                <template v-slot:item.status="{ item }">
                  <v-chip :color="getStatusColor(item.status)" small>
                    {{ item.status }}
                  </v-chip>
                </template>
                
                <template v-slot:item.actions="{ item }">
                  <v-btn
                    icon
                    small
                    @click="viewAssignment(item)"
                  >
                    <v-icon>mdi-eye</v-icon>
                  </v-btn>
                </template>
              </v-data-table>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      
      <!-- График прогресса -->
      <v-row>
        <v-col cols="12">
          <v-card>
            <v-card-title>Прогресс по семестрам</v-card-title>
            <v-card-text>
              <div style="height: 300px;">
                <!-- Здесь будет график -->
                <div class="text-center pa-8">
                  <v-icon size="64" color="grey">mdi-chart-line</v-icon>
                  <h3 class="text-h6 mt-4">График прогресса</h3>
                  <p class="text-grey">Здесь будет отображаться график вашего прогресса</p>
                </div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      </div>
    </v-container>
    
    <!-- Диалог просмотра задания -->
    <v-dialog v-model="assignmentDialog" max-width="600">
      <v-card>
        <v-card-title>
          {{ selectedAssignment?.title }}
          <v-spacer></v-spacer>
          <v-btn icon @click="assignmentDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <div v-if="selectedAssignment">
            <v-list dense>
              <v-list-item>
                <v-list-item-title>Оценка</v-list-item-title>
                <v-list-item-subtitle>
                  <v-chip :color="getGradeColor(selectedAssignment.grade)">
                    {{ selectedAssignment.grade }}
                  </v-chip>
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>Статус</v-list-item-title>
                <v-list-item-subtitle>
                  <v-chip :color="getStatusColor(selectedAssignment.status)">
                    {{ selectedAssignment.status }}
                  </v-chip>
                </v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>Дата сдачи</v-list-item-title>
                <v-list-item-subtitle>{{ selectedAssignment.submittedDate }}</v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>Дата оценки</v-list-item-title>
                <v-list-item-subtitle>{{ selectedAssignment.gradedDate }}</v-list-item-subtitle>
              </v-list-item>
            </v-list>
            
            <v-divider class="my-4"></v-divider>
            
            <h3 class="text-h6 mb-3">Комментарий преподавателя</h3>
            <p>{{ selectedAssignment.comment || 'Комментарий отсутствует' }}</p>
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>
  </Layout>
</template>

<script>
import Layout from '../Layout.vue'

export default {
  name: 'GradesIndex',
  components: {
    Layout
  },
  data() {
    return {
      selectedCourse: null,
      selectedSemester: null,
      assignmentDialog: false,
      selectedAssignment: null,
      courses: [
        'Все курсы',
        'Введение в программирование',
        'Веб-разработка'
      ],
      semesters: [
        'Все семестры',
        '1 семестр',
        '2 семестр'
      ],
      gradeHeaders: [
        { text: 'Задание', value: 'title' },
        { text: 'Тип', value: 'type' },
        { text: 'Оценка', value: 'grade' },
        { text: 'Статус', value: 'status' },
        { text: 'Дата', value: 'date' },
        { text: 'Действия', value: 'actions', sortable: false }
      ],
      courseGrades: [
        {
          id: 1,
          name: 'Введение в программирование',
          averageGrade: 4.2,
          assignments: [
            {
              id: 1,
              title: 'Лабораторная работа №1',
              type: 'Лабораторная',
              grade: 5,
              status: 'Оценено',
              date: '2024-01-15'
            },
            {
              id: 2,
              title: 'Домашнее задание №1',
              type: 'Домашнее',
              grade: 4,
              status: 'Оценено',
              date: '2024-01-10'
            },
            {
              id: 3,
              title: 'Контрольная работа',
              type: 'Контрольная',
              grade: 4,
              status: 'Ожидает оценки',
              date: '2024-01-20'
            }
          ]
        },
        {
          id: 2,
          name: 'Веб-разработка',
          averageGrade: 4.5,
          assignments: [
            {
              id: 4,
              title: 'Проект веб-сайта',
              type: 'Проект',
              grade: 5,
              status: 'Оценено',
              date: '2024-01-18'
            }
          ]
        }
      ]
    }
  },
  computed: {
    filteredCourses() {
      let filtered = this.courseGrades
      
      if (this.selectedCourse && this.selectedCourse !== 'Все курсы') {
        filtered = filtered.filter(course => course.name === this.selectedCourse)
      }
      
      return filtered
    },
    
    averageGrade() {
      const allGrades = this.courseGrades.flatMap(course => 
        course.assignments.filter(assignment => assignment.grade)
      )
      if (allGrades.length === 0) return '0.0'
      
      const sum = allGrades.reduce((acc, assignment) => acc + assignment.grade, 0)
      return (sum / allGrades.length).toFixed(1)
    },
    
    completedAssignments() {
      return this.courseGrades.flatMap(course => 
        course.assignments.filter(assignment => assignment.status === 'Оценено')
      ).length
    },
    
    pendingAssignments() {
      return this.courseGrades.flatMap(course => 
        course.assignments.filter(assignment => assignment.status === 'Ожидает оценки')
      ).length
    },
    
    totalCredits() {
      // Простой расчет кредитов на основе оценок
      return this.courseGrades.length * 3
    }
  },
  methods: {
    getGradeColor(grade) {
      if (grade >= 4.5) return 'success'
      if (grade >= 3.5) return 'warning'
      return 'error'
    },
    
    getStatusColor(status) {
      switch (status) {
        case 'Оценено':
          return 'success'
        case 'Ожидает оценки':
          return 'warning'
        default:
          return 'grey'
      }
    },
    
    viewAssignment(assignment) {
      this.selectedAssignment = {
        ...assignment,
        submittedDate: '2024-01-15',
        gradedDate: '2024-01-16',
        comment: 'Отличная работа! Код написан качественно и соответствует всем требованиям.'
      }
      this.assignmentDialog = true
    }
  }
}
</script>
