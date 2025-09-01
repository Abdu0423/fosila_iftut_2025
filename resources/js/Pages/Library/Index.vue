<template>
  <App>
    <div class="library-page">
      <v-row>
        <v-col cols="12">
          <h1 class="text-h4 mb-6">Библиотека</h1>
        </v-col>
      </v-row>
      
      <!-- Поиск и фильтры -->
      <v-row>
        <v-col cols="12" md="8">
          <v-text-field
            v-model="searchQuery"
            label="Поиск по библиотеке..."
            prepend-inner-icon="mdi-magnify"
            outlined
            dense
            clearable
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-select
            v-model="selectedCategory"
            :items="categories"
            label="Категория"
            outlined
            dense
            clearable
          ></v-select>
        </v-col>
      </v-row>
      
      <!-- Результаты поиска -->
      <v-row>
        <v-col cols="12">
          <v-card v-for="resource in filteredResources" :key="resource.id" class="mb-4">
            <v-card-title class="d-flex align-center">
              <v-icon class="mr-3" :color="getResourceColor(resource.type)">
                {{ getResourceIcon(resource.type) }}
              </v-icon>
              {{ resource.title }}
              <v-spacer></v-spacer>
              <v-chip :color="getResourceColor(resource.type)" small>
                {{ resource.type }}
              </v-chip>
            </v-card-title>
            
            <v-card-text>
              <p>{{ resource.description }}</p>
              
              <v-list dense>
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-book</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title>{{ resource.course }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
                
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-account</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title>{{ resource.author }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
                
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-calendar</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title>{{ resource.date }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-card-text>
            
            <v-card-actions>
              <v-btn color="primary" @click="downloadResource(resource)">
                <v-icon left>mdi-download</v-icon>
                Скачать
              </v-btn>
              <v-btn color="secondary" outlined @click="previewResource(resource)">
                <v-icon left>mdi-eye</v-icon>
                Предварительный просмотр
              </v-btn>
              <v-spacer></v-spacer>
              <v-btn icon @click="toggleFavorite(resource)">
                <v-icon :color="resource.isFavorite ? 'red' : 'grey'">
                  {{ resource.isFavorite ? 'mdi-heart' : 'mdi-heart-outline' }}
                </v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
      
      <!-- Пагинация -->
      <v-row>
        <v-col cols="12" class="text-center">
          <v-pagination
            v-model="currentPage"
            :length="totalPages"
            :total-visible="7"
          ></v-pagination>
        </v-col>
      </v-row>
    </v-container>
    
    <!-- Диалог предварительного просмотра -->
    <v-dialog v-model="previewDialog" max-width="800">
      <v-card>
        <v-card-title>
          {{ selectedResource?.title }}
          <v-spacer></v-spacer>
          <v-btn icon @click="previewDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <div v-if="selectedResource">
            <p>{{ selectedResource.description }}</p>
            <v-divider class="my-4"></v-divider>
            <h3 class="text-h6 mb-3">Детали ресурса</h3>
            <v-list dense>
              <v-list-item>
                <v-list-item-title>Тип: {{ selectedResource.type }}</v-list-item-title>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>Размер: {{ selectedResource.size }}</v-list-item-title>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>Формат: {{ selectedResource.format }}</v-list-item-title>
              </v-list-item>
            </v-list>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="downloadResource(selectedResource)">
            Скачать
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    </div>
  </App>
</template>

<script>
import App from '../App.vue'

export default {
  name: 'LibraryIndex',
  components: {
    App
  },
  data() {
    return {
      searchQuery: '',
      selectedCategory: null,
      currentPage: 1,
      previewDialog: false,
      selectedResource: null,
      categories: [
        'Все',
        'Учебники',
        'Лекции',
        'Практические работы',
        'Видео',
        'Презентации'
      ],
      resources: [
        {
          id: 1,
          title: 'Учебник по Python для начинающих',
          description: 'Подробное руководство по изучению языка программирования Python',
          type: 'Учебник',
          course: 'Введение в программирование',
          author: 'Иванов И.И.',
          date: '2024-01-15',
          size: '2.5 MB',
          format: 'PDF',
          isFavorite: false
        },
        {
          id: 2,
          title: 'Лекция 1: Основы веб-разработки',
          description: 'Вводная лекция по основам создания веб-сайтов',
          type: 'Лекция',
          course: 'Веб-разработка',
          author: 'Петров П.П.',
          date: '2024-01-14',
          size: '15.2 MB',
          format: 'MP4',
          isFavorite: true
        },
        {
          id: 3,
          title: 'Практическая работа №1',
          description: 'Создание простого HTML-сайта',
          type: 'Практические работы',
          course: 'Веб-разработка',
          author: 'Петров П.П.',
          date: '2024-01-13',
          size: '1.8 MB',
          format: 'ZIP',
          isFavorite: false
        }
      ]
    }
  },
  computed: {
    filteredResources() {
      let filtered = this.resources
      
      if (this.searchQuery) {
        filtered = filtered.filter(resource =>
          resource.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          resource.description.toLowerCase().includes(this.searchQuery.toLowerCase())
        )
      }
      
      if (this.selectedCategory && this.selectedCategory !== 'Все') {
        filtered = filtered.filter(resource => resource.type === this.selectedCategory)
      }
      
      return filtered
    },
    
    totalPages() {
      return Math.ceil(this.filteredResources.length / 10)
    }
  },
  methods: {
    getResourceIcon(type) {
      const icons = {
        'Учебник': 'mdi-book-open-variant',
        'Лекция': 'mdi-video',
        'Практические работы': 'mdi-file-document',
        'Видео': 'mdi-video',
        'Презентации': 'mdi-presentation'
      }
      return icons[type] || 'mdi-file'
    },
    
    getResourceColor(type) {
      const colors = {
        'Учебник': 'primary',
        'Лекция': 'secondary',
        'Практические работы': 'success',
        'Видео': 'info',
        'Презентации': 'warning'
      }
      return colors[type] || 'grey'
    },
    
    downloadResource(resource) {
      // Здесь будет логика скачивания
      console.log('Скачивание ресурса:', resource.title)
    },
    
    previewResource(resource) {
      this.selectedResource = resource
      this.previewDialog = true
    },
    
    toggleFavorite(resource) {
      resource.isFavorite = !resource.isFavorite
    }
  }
}
</script>
