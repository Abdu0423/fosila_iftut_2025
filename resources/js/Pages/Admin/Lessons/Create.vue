<template>
  <AdminLayout>
    <!-- Заголовок страницы -->
    <v-row class="mb-6">
      <v-col cols="12">
        <div class="d-flex justify-space-between align-center">
          <div class="d-flex align-center">
            <v-icon size="48" color="primary" class="mr-4">mdi-plus-circle</v-icon>
            <div>
              <h1 class="text-h3 font-weight-bold text-primary mb-2">Создать новый урок</h1>
              <p class="text-body-1 text-medium-emphasis">Добавьте новый учебный материал в систему</p>
            </div>
          </div>
          <v-btn
            variant="outlined"
            color="primary"
            size="large"
            :href="route('admin.lessons.index')"
            prepend-icon="mdi-arrow-left"
            rounded="lg"
            class="back-btn"
          >
            Назад к списку
          </v-btn>
        </div>
      </v-col>
    </v-row>

    <v-row>
      <!-- Основная форма -->
      <v-col cols="12" lg="8">
        <v-card class="form-card" elevation="4" rounded="xl">
          <v-card-title class="text-h5 font-weight-bold pa-6 pb-4">
            <v-icon color="primary" class="mr-3">mdi-book-open-page-variant</v-icon>
            Информация об уроке
          </v-card-title>
          <v-card-text class="pa-6 pt-0">
            <v-form @submit.prevent="submit" class="lesson-form">
              <v-row>
                <!-- Название урока -->
                <v-col cols="12">
                  <v-text-field
                    v-model="form.title"
                    label="Название урока"
                    prepend-inner-icon="mdi-format-title"
                    variant="outlined"
                    rounded="lg"
                    :error-messages="form.errors.title"
                    :disabled="form.processing"
                    required
                    class="form-field"
                  ></v-text-field>
                </v-col>

                <!-- Курс и Кафедра -->
                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.course"
                    label="Курс"
                    prepend-inner-icon="mdi-school"
                    :items="courseOptions"
                    variant="outlined"
                    rounded="lg"
                    :error-messages="form.errors.course"
                    :disabled="form.processing"
                    required
                    class="form-field"
                  ></v-select>
                </v-col>

                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.department_id"
                    label="Кафедра"
                    prepend-inner-icon="mdi-office-building"
                    :items="departments || []"
                    item-title="name"
                    item-value="id"
                    variant="outlined"
                    rounded="lg"
                    :error-messages="form.errors.department_id"
                    :disabled="form.processing"
                    required
                    class="form-field"
                  ></v-select>
                </v-col>

                <!-- Описание урока -->
                <v-col cols="12">
                  <v-textarea
                    v-model="form.content"
                    label="Описание урока"
                    prepend-inner-icon="mdi-text"
                    variant="outlined"
                    rounded="lg"
                    :error-messages="form.errors.content"
                    :disabled="form.processing"
                    rows="6"
                    auto-grow
                    class="form-field"
                  ></v-textarea>
                </v-col>

                <!-- Дополнительные поля -->
                <v-col cols="12">
                  <v-divider class="my-4"></v-divider>
                  <h3 class="text-h6 font-weight-medium mb-4">Дополнительные настройки</h3>
                </v-col>

                <!-- Статус урока -->
                <v-col cols="12" md="6">
                  <v-card variant="outlined" rounded="lg" class="pa-4">
                    <div class="d-flex align-center">
                      <v-switch
                        v-model="form.is_active"
                        color="success"
                        :disabled="form.processing"
                        class="mr-3"
                      ></v-switch>
                      <div>
                        <div class="text-body-1 font-weight-medium">Активный урок</div>
                        <div class="text-body-2 text-medium-emphasis">
                          {{ form.is_active ? 'Урок будет доступен' : 'Урок будет скрыт' }}
                        </div>
                      </div>
                    </div>
                  </v-card>
                </v-col>

                <!-- Сложность урока -->
                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.difficulty"
                    label="Сложность"
                    prepend-inner-icon="mdi-trending-up"
                    :items="difficultyOptions"
                    variant="outlined"
                    rounded="lg"
                    :disabled="form.processing"
                    class="form-field"
                  ></v-select>
                </v-col>

                <!-- Длительность урока -->
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.duration"
                    label="Длительность (часы)"
                    prepend-inner-icon="mdi-clock-outline"
                    type="number"
                    min="1"
                    max="10"
                    variant="outlined"
                    rounded="lg"
                    :disabled="form.processing"
                    class="form-field"
                  ></v-text-field>
                </v-col>

                <!-- Ключевые слова -->
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.keywords"
                    label="Ключевые слова"
                    prepend-inner-icon="mdi-tag-multiple"
                    variant="outlined"
                    rounded="lg"
                    :disabled="form.processing"
                    placeholder="Разделяйте запятыми"
                    class="form-field"
                  ></v-text-field>
                </v-col>
              </v-row>

              <!-- Кнопки действий -->
              <v-row class="mt-8">
                <v-col cols="12">
                  <div class="d-flex gap-4 flex-wrap">
                    <v-btn
                      type="submit"
                      color="primary"
                      size="large"
                      :loading="form.processing"
                      :disabled="form.processing"
                      prepend-icon="mdi-content-save"
                      rounded="lg"
                      class="submit-btn"
                      elevation="4"
                    >
                      <v-icon v-if="!form.processing" start>mdi-content-save</v-icon>
                      {{ form.processing ? 'Создание...' : 'Создать урок' }}
                    </v-btn>
                    
                    <v-btn
                      variant="outlined"
                      color="secondary"
                      size="large"
                      :href="route('admin.lessons.index')"
                      :disabled="form.processing"
                      prepend-icon="mdi-close"
                      rounded="lg"
                    >
                      Отмена
                    </v-btn>

                    <v-btn
                      variant="text"
                      color="info"
                      size="large"
                      @click="previewLesson"
                      :disabled="form.processing"
                      prepend-icon="mdi-eye"
                      rounded="lg"
                    >
                      Предварительный просмотр
                    </v-btn>
                  </div>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Боковая панель -->
      <v-col cols="12" lg="4">
        <!-- Подсказки -->
        <v-card class="tips-card mb-6" elevation="2" rounded="xl">
          <v-card-title class="text-h6 font-weight-bold pa-6 pb-4">
            <v-icon color="info" class="mr-3">mdi-lightbulb</v-icon>
            Подсказки
          </v-card-title>
          <v-card-text class="pa-6 pt-0">
            <v-list class="pa-0">
              <v-list-item class="mb-3" rounded="lg">
                <template v-slot:prepend>
                  <v-avatar color="primary" size="32">
                    <v-icon color="white" size="16">mdi-format-title</v-icon>
                  </v-avatar>
                </template>
                <v-list-item-title class="font-weight-medium">Название урока</v-list-item-title>
                <v-list-item-subtitle class="text-body-2">
                  Используйте краткое и понятное название
                </v-list-item-subtitle>
              </v-list-item>
              
              <v-list-item class="mb-3" rounded="lg">
                <template v-slot:prepend>
                  <v-avatar color="success" size="32">
                    <v-icon color="white" size="16">mdi-school</v-icon>
                  </v-avatar>
                </template>
                <v-list-item-title class="font-weight-medium">Курс</v-list-item-title>
                <v-list-item-subtitle class="text-body-2">
                  Выберите курс, для которого предназначен урок
                </v-list-item-subtitle>
              </v-list-item>
              
              <v-list-item class="mb-3" rounded="lg">
                <template v-slot:prepend>
                  <v-avatar color="warning" size="32">
                    <v-icon color="white" size="16">mdi-office-building</v-icon>
                  </v-avatar>
                </template>
                <v-list-item-title class="font-weight-medium">Кафедра</v-list-item-title>
                <v-list-item-subtitle class="text-body-2">
                  Укажите кафедру, отвечающую за этот урок
                </v-list-item-subtitle>
              </v-list-item>
              
              <v-list-item class="mb-3" rounded="lg">
                <template v-slot:prepend>
                  <v-avatar color="info" size="32">
                    <v-icon color="white" size="16">mdi-text</v-icon>
                  </v-avatar>
                </template>
                <v-list-item-title class="font-weight-medium">Описание</v-list-item-title>
                <v-list-item-subtitle class="text-body-2">
                  Добавьте краткое описание содержания урока
                </v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>

        <!-- Статистика -->
        <v-card class="stats-card" elevation="2" rounded="xl">
          <v-card-title class="text-h6 font-weight-bold pa-6 pb-4">
            <v-icon color="primary" class="mr-3">mdi-chart-line</v-icon>
            Статистика
          </v-card-title>
          <v-card-text class="pa-6 pt-0">
            <div class="mb-4">
              <div class="d-flex justify-space-between align-center mb-2">
                <span class="text-body-2">Всего уроков</span>
                <span class="text-h6 font-weight-bold text-primary">{{ totalLessons || 0 }}</span>
              </div>
              <v-progress-linear
                :model-value="100"
                color="primary"
                height="8"
                rounded
              ></v-progress-linear>
            </div>
            
            <div class="mb-4">
              <div class="d-flex justify-space-between align-center mb-2">
                <span class="text-body-2">Активных уроков</span>
                <span class="text-h6 font-weight-bold text-success">{{ activeLessons || 0 }}</span>
              </div>
              <v-progress-linear
                :model-value="(activeLessons || 0) / (totalLessons || 1) * 100"
                color="success"
                height="8"
                rounded
              ></v-progress-linear>
            </div>
            
            <div class="mb-4">
              <div class="d-flex justify-space-between align-center mb-2">
                <span class="text-body-2">Кафедр</span>
                <span class="text-h6 font-weight-bold text-info">{{ departments?.length || 0 }}</span>
              </div>
              <v-progress-linear
                :model-value="100"
                color="info"
                height="8"
                rounded
              ></v-progress-linear>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Диалог предварительного просмотра -->
    <v-dialog v-model="previewDialog" max-width="800" persistent>
      <v-card class="preview-dialog" rounded="xl">
        <v-card-title class="text-h5 font-weight-bold pa-6 pb-4">
          <v-icon color="primary" class="mr-3">mdi-eye</v-icon>
          Предварительный просмотр урока
        </v-card-title>
        <v-card-text class="pa-6 pt-0">
          <div class="preview-content">
            <h2 class="text-h4 font-weight-bold mb-4">{{ form.title || 'Название урока' }}</h2>
            
            <div class="d-flex gap-4 mb-6">
              <v-chip color="primary" variant="tonal">
                <v-icon start size="small">mdi-school</v-icon>
                {{ form.course ? `${form.course} курс` : 'Курс не выбран' }}
              </v-chip>
              
              <v-chip color="info" variant="tonal">
                <v-icon start size="small">mdi-office-building</v-icon>
                {{ getDepartmentName(form.department_id) || 'Кафедра не выбрана' }}
              </v-chip>
              
              <v-chip :color="form.is_active ? 'success' : 'error'" variant="tonal">
                <v-icon start size="small">
                  {{ form.is_active ? 'mdi-check-circle' : 'mdi-close-circle' }}
                </v-icon>
                {{ form.is_active ? 'Активен' : 'Неактивен' }}
              </v-chip>
            </div>
            
            <div class="text-body-1">
              {{ form.content || 'Описание урока не добавлено' }}
            </div>
          </div>
        </v-card-text>
        <v-card-actions class="pa-6 pt-0">
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            @click="previewDialog = false"
            rounded="lg"
          >
            Закрыть
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Layout from '../../Layout.vue'

const props = defineProps({
  departments: Array,
  totalLessons: Number,
  activeLessons: Number,
})

const previewDialog = ref(false)

const form = useForm({
  title: '',
  content: '',
  course: null,
  department_id: null,
  is_active: true,
  difficulty: 'medium',
  duration: 2,
  keywords: '',
})

const courseOptions = [
  { title: '1 курс', value: 1 },
  { title: '2 курс', value: 2 },
  { title: '3 курс', value: 3 },
  { title: '4 курс', value: 4 },
  { title: '5 курс', value: 5 },
  { title: '6 курс', value: 6 },
]

const difficultyOptions = [
  { title: 'Легкий', value: 'easy' },
  { title: 'Средний', value: 'medium' },
  { title: 'Сложный', value: 'hard' },
]

const getDepartmentName = (departmentId) => {
  const department = props.departments?.find(d => d.id === departmentId)
  return department?.name
}

const submit = () => {
  form.post(route('admin.lessons.store'))
}

const previewLesson = () => {
  previewDialog.value = true
}
</script>

<style scoped>
.form-card, .tips-card, .stats-card {
  transition: all 0.3s ease;
}

.form-card:hover, .tips-card:hover, .stats-card:hover {
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.form-field {
  transition: all 0.3s ease;
}

.form-field .v-field:focus-within {
  transform: scale(1.02);
}

.submit-btn {
  transition: all 0.3s ease;
}

.submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.back-btn {
  transition: all 0.3s ease;
}

.back-btn:hover {
  transform: translateX(-4px);
}

.preview-dialog {
  border: 2px solid rgba(var(--v-theme-primary), 0.1);
}

.preview-content {
  background: #f8f9fa;
  padding: 24px;
  border-radius: 12px;
  border: 1px solid rgba(0, 0, 0, 0.1);
}

/* Анимации появления */
.form-card {
  animation: slideInUp 0.6s ease-out;
}

.tips-card {
  animation: slideInRight 0.6s ease-out 0.2s both;
}

.stats-card {
  animation: slideInRight 0.6s ease-out 0.4s both;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Адаптивность */
@media (max-width: 768px) {
  .d-flex.justify-space-between {
    flex-direction: column;
    gap: 16px;
  }
  
  .back-btn {
    width: 100%;
  }
  
  .d-flex.gap-4 {
    flex-direction: column;
  }
  
  .submit-btn, .v-btn {
    width: 100%;
  }
}
</style>
