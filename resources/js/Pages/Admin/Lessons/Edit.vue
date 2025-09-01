<template>
  <AdminLayout>
    <v-row>
      <v-col cols="12">
        <div class="d-flex justify-space-between align-center mb-4">
          <h1 class="text-h4">Редактировать урок</h1>
          <v-btn
            variant="outlined"
            :href="route('admin.lessons.index')"
            prepend-icon="mdi-arrow-left"
          >
            Назад к списку
          </v-btn>
        </div>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12" md="8">
        <v-card>
          <v-card-title>Информация об уроке</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="submit">
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    v-model="form.title"
                    label="Название урока"
                    :error-messages="form.errors.title"
                    required
                  ></v-text-field>
                </v-col>

                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.course"
                    label="Курс"
                    :items="[1, 2, 3, 4, 5, 6]"
                    :error-messages="form.errors.course"
                    required
                  ></v-select>
                </v-col>

                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.department_id"
                    label="Кафедра"
                    :items="departments"
                    item-title="name"
                    item-value="id"
                    :error-messages="form.errors.department_id"
                    required
                  ></v-select>
                </v-col>

                <v-col cols="12">
                  <v-textarea
                    v-model="form.content"
                    label="Описание урока"
                    :error-messages="form.errors.content"
                    rows="4"
                  ></v-textarea>
                </v-col>

                <v-col cols="12">
                  <v-switch
                    v-model="form.is_active"
                    label="Активный урок"
                    color="primary"
                  ></v-switch>
                </v-col>
              </v-row>

              <v-row>
                <v-col cols="12">
                  <div class="d-flex gap-2">
                    <v-btn
                      type="submit"
                      color="primary"
                      :loading="form.processing"
                      prepend-icon="mdi-content-save"
                    >
                      Сохранить изменения
                    </v-btn>
                    
                    <v-btn
                      variant="outlined"
                      :href="route('admin.lessons.show', lesson.id)"
                      :disabled="form.processing"
                    >
                      Отмена
                    </v-btn>
                  </div>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card>
          <v-card-title>Информация об уроке</v-card-title>
          <v-card-text>
            <v-list>
              <v-list-item>
                <v-list-item-title>ID урока</v-list-item-title>
                <v-list-item-subtitle>{{ lesson.id }}</v-list-item-subtitle>
              </v-list-item>
              
              <v-list-item>
                <v-list-item-title>Дата создания</v-list-item-title>
                <v-list-item-subtitle>{{ formatDate(lesson.created_at) }}</v-list-item-subtitle>
              </v-list-item>
              
              <v-list-item>
                <v-list-item-title>Последнее обновление</v-list-item-title>
                <v-list-item-subtitle>{{ formatDate(lesson.updated_at) }}</v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>

        <v-card class="mt-4">
          <v-card-title>Действия</v-card-title>
          <v-card-text>
            <v-btn
              block
              color="info"
              :href="route('admin.lessons.show', lesson.id)"
              prepend-icon="mdi-eye"
            >
              Просмотреть урок
            </v-btn>
            
            <v-btn
              block
              color="error"
              variant="outlined"
              class="mt-2"
              prepend-icon="mdi-delete"
              @click="deleteLesson"
            >
              Удалить урок
            </v-btn>
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
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import Layout from '../../Layout.vue'

const props = defineProps({
  lesson: Object,
  departments: Array,
})

const deleteDialog = ref(false)

const form = useForm({
  title: props.lesson.title,
  content: props.lesson.content,
  course: props.lesson.course,
  department_id: props.lesson.department_id,
  is_active: props.lesson.is_active,
})

const submit = () => {
  form.put(route('admin.lessons.update', props.lesson.id))
}

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
</script>
