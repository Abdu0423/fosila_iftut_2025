# IFTUT - Система дистанционного обучения

Современная платформа для дистанционного обучения в университете, построенная на Laravel, Vue 3, Inertia.js и Vuetify.

## 🚀 Технологии

- **Backend**: Laravel 10
- **Frontend**: Vue 3 + Composition API
- **UI Framework**: Vuetify 3
- **SPA**: Inertia.js
- **Build Tool**: Vite
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Sanctum

## 📋 Функциональность

### Для студентов:
- 📚 Просмотр курсов и материалов
- 📝 Выполнение заданий
- 📅 Расписание занятий
- 💬 Чат с преподавателями
- 📊 Отслеживание прогресса
- 📖 Библиотека материалов
- ⭐ Просмотр оценок

### Для преподавателей:
- 👨‍🏫 Управление курсами
- 📋 Создание заданий
- 📊 Оценка работ
- 💬 Общение со студентами
- 📈 Аналитика успеваемости

### Для администраторов:
- 👥 Управление пользователями
- 🏫 Управление факультетами и кафедрами
- 📚 Управление курсами
- ⚙️ Системные настройки

## 🛠️ Установка

### Требования
- PHP 8.1+
- Node.js 18+
- Composer
- MySQL/PostgreSQL

### Шаги установки

1. **Клонирование репозитория**
```bash
git clone <repository-url>
cd iftut
```

2. **Установка PHP зависимостей**
```bash
composer install
```

3. **Установка Node.js зависимостей**
```bash
npm install
```

4. **Настройка окружения**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Настройка базы данных**
Отредактируйте файл `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iftut
DB_USERNAME=root
DB_PASSWORD=
```

6. **Запуск миграций**
```bash
php artisan migrate
```

7. **Заполнение базы тестовыми данными**
```bash
php artisan db:seed
```

8. **Сборка фронтенда**
```bash
npm run build
```

9. **Запуск сервера разработки**
```bash
# Терминал 1: Laravel сервер
php artisan serve

# Терминал 2: Vite dev server
npm run dev
```

## 📁 Структура проекта

```
iftut/
├── app/
│   ├── Http/Controllers/     # Контроллеры
│   ├── Models/              # Модели Eloquent
│   └── Providers/           # Сервис-провайдеры
├── database/
│   ├── migrations/          # Миграции БД
│   └── seeders/            # Сидеры данных
├── resources/
│   ├── js/
│   │   ├── Pages/          # Vue компоненты страниц
│   │   ├── Components/     # Переиспользуемые компоненты
│   │   └── app.js         # Точка входа приложения
│   ├── css/
│   │   └── app.css        # Стили
│   └── views/
│       └── app.blade.php  # Основной шаблон
├── routes/
│   └── web.php            # Веб маршруты
└── vite.config.js         # Конфигурация Vite
```

## 🎨 UI/UX Особенности

- **Адаптивный дизайн** - работает на всех устройствах
- **Material Design** - современный и интуитивный интерфейс
- **Темная/светлая тема** - поддержка переключения тем
- **Анимации** - плавные переходы и эффекты
- **Доступность** - соответствует стандартам WCAG

## 🔧 Разработка

### Команды для разработки

```bash
# Запуск в режиме разработки
npm run dev

# Сборка для продакшена
npm run build

# Проверка кода
npm run lint

# Тестирование
php artisan test
```

### Создание новых компонентов

```bash
# Создание новой страницы
touch resources/js/Pages/NewPage.vue

# Создание нового компонента
touch resources/js/Components/NewComponent.vue
```

## 📊 База данных

Основные таблицы:
- `users` - пользователи системы
- `courses` - курсы
- `lessons` - уроки
- `assignments` - задания
- `submissions` - сдачи заданий
- `grades` - оценки
- `departments` - кафедры
- `faculties` - факультеты

## 🔐 Безопасность

- CSRF защита
- XSS защита
- SQL инъекции защита
- Аутентификация через Sanctum
- Авторизация на основе ролей

## 📱 Мобильная поддержка

- Адаптивный дизайн
- Touch-friendly интерфейс
- PWA готовность
- Оффлайн функциональность

## 🚀 Развертывание

### Продакшен

1. **Настройка сервера**
```bash
composer install --optimize-autoloader --no-dev
npm run build
```

2. **Настройка веб-сервера**
```bash
# Nginx конфигурация
sudo nano /etc/nginx/sites-available/iftut
```

3. **Настройка очередей**
```bash
php artisan queue:work
```

## 🤝 Вклад в проект

1. Форкните репозиторий
2. Создайте ветку для новой функции
3. Внесите изменения
4. Создайте Pull Request

## 📄 Лицензия

MIT License

## 📞 Поддержка

- Email: support@iftut.edu
- Документация: [docs.iftut.edu](https://docs.iftut.edu)
- Issues: [GitHub Issues](https://github.com/iftut/issues)

---

**IFTUT** - Инновационная платформа для дистанционного обучения в университете 🎓
