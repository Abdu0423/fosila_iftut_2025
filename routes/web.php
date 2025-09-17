<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Маршруты аутентификации (доступны для гостей)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Защищенные маршруты (требуют аутентификации)
Route::middleware('auth')->group(function () {
    // Главная страница
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Dashboard (альтернативный маршрут)
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

// Курсы
Route::get('/courses', function () {
    return Inertia::render('Courses/Index');
})->name('courses.index');

Route::get('/courses/{course}', function ($course) {
    return Inertia::render('Courses/Show', ['course' => $course]);
})->name('courses.show');

// Расписание
Route::get('/schedule', function () {
    return Inertia::render('Schedule/Index');
})->name('schedule.index');

// Задания
Route::get('/assignments', function () {
    return Inertia::render('Assignments/Index');
})->name('assignments.index');

Route::get('/assignments/{assignment}', function ($assignment) {
    return Inertia::render('Assignments/Show', ['assignment' => $assignment]);
})->name('assignments.show');

// Чат
Route::get('/chat', function () {
    return Inertia::render('Chat/Index');
})->name('chat.index');

// Библиотека
Route::get('/library', function () {
    return Inertia::render('Library/Index');
})->name('library.index');

// Тестовый маршрут для проверки уведомлений (удалить после тестирования)
Route::get('/test-notification', function () {
    return redirect()->back()->with('success', 'Тестовое уведомление об успехе!');
})->name('test.notification');


// Оценки
Route::get('/grades', function () {
    return Inertia::render('Grades/Index');
})->name('grades.index');

// Профиль
Route::get('/profile', function () {
    return Inertia::render('Profile/Index');
})->name('profile.index');

// Настройки
Route::get('/settings', function () {
    return Inertia::render('Settings/Index');
})->name('settings.index');

}); // Закрытие группы auth

// Админ панель (требует аутентификации и прав администратора)
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Главная страница админа
    Route::get('/', function () {
        \Log::info('Admin Dashboard: Запрос к /admin получен', [
            'user_id' => auth()->id(),
            'role_id' => auth()->user()->role_id,
            'url' => request()->url()
        ]);
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');
    
    // Управление предметами
    Route::resource('subjects', App\Http\Controllers\Admin\SubjectController::class)->names('admin.subjects');
    Route::post('/subjects/{subject}/toggle-status', [App\Http\Controllers\Admin\SubjectController::class, 'toggleStatus'])->name('admin.subjects.toggle-status');
    Route::post('/subjects/bulk-action', [App\Http\Controllers\Admin\SubjectController::class, 'bulkAction'])->name('admin.subjects.bulk-action');
    Route::post('/subjects/{subject}/duplicate', [App\Http\Controllers\Admin\SubjectController::class, 'duplicate'])->name('admin.subjects.duplicate');
    Route::get('/subjects-export', [App\Http\Controllers\Admin\SubjectController::class, 'export'])->name('admin.subjects.export');
    
    // Управление пользователями
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('admin.users.show');
    Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/users/{user}/delete', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy.post');
    
    // Управление курсами
    Route::get('/courses', function () {
        return Inertia::render('Admin/Courses/Index');
    })->name('admin.courses.index');
    
    Route::get('/courses/create', function () {
        return Inertia::render('Admin/Courses/Create');
    })->name('admin.courses.create');
    
    Route::get('/courses/{course}/edit', function ($course) {
        return Inertia::render('Admin/Courses/Edit', ['course' => $course]);
    })->name('admin.courses.edit');
    
    // Управление заданиями
    Route::get('/assignments', function () {
        return Inertia::render('Admin/Assignments/Index');
    })->name('admin.assignments.index');
    
    Route::get('/assignments/create', function () {
        return Inertia::render('Admin/Assignments/Create');
    })->name('admin.assignments.create');
    
    Route::get('/assignments/{assignment}/edit', function ($assignment) {
        return Inertia::render('Admin/Assignments/Edit', ['assignment' => $assignment]);
    })->name('admin.assignments.edit');
    
    // Управление библиотекой
    Route::get('/library', function () {
        return Inertia::render('Admin/Library/Index');
    })->name('admin.library.index');
    
    Route::get('/library/create', function () {
        return Inertia::render('Admin/Library/Create');
    })->name('admin.library.create');
    
    Route::get('/library/{resource}/edit', function ($resource) {
        return Inertia::render('Admin/Library/Edit', ['resource' => $resource]);
    })->name('admin.library.edit');
    
    // Статистика и отчеты
    Route::get('/reports', function () {
        return Inertia::render('Admin/Reports/Index');
    })->name('admin.reports.index');
    
    // Настройки системы
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings/general', [App\Http\Controllers\Admin\SettingsController::class, 'updateGeneral'])->name('admin.settings.general');
    Route::post('/settings/files', [App\Http\Controllers\Admin\SettingsController::class, 'updateFileSettings'])->name('admin.settings.files');
    Route::post('/settings/system', [App\Http\Controllers\Admin\SettingsController::class, 'updateSystemSettings'])->name('admin.settings.system');
    Route::post('/settings/email', [App\Http\Controllers\Admin\SettingsController::class, 'updateEmailSettings'])->name('admin.settings.email');
    Route::post('/settings/profile', [App\Http\Controllers\Admin\SettingsController::class, 'updateProfile'])->name('admin.settings.profile');
    Route::post('/settings/test-email', [App\Http\Controllers\Admin\SettingsController::class, 'testEmail'])->name('admin.settings.test-email');
    Route::post('/settings/clear-cache', [App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])->name('admin.settings.clear-cache');
    Route::post('/settings/backup-database', [App\Http\Controllers\Admin\SettingsController::class, 'backupDatabase'])->name('admin.settings.backup-database');
    
    // Управление силлабусами
    Route::get('/syllabuses', [App\Http\Controllers\Admin\SyllabusController::class, 'index'])->name('admin.syllabuses.index');
    Route::get('/syllabuses/create', [App\Http\Controllers\Admin\SyllabusController::class, 'create'])->name('admin.syllabuses.create');
    Route::post('/syllabuses', [App\Http\Controllers\Admin\SyllabusController::class, 'store'])->name('admin.syllabuses.store');
    Route::get('/syllabuses/{syllabus}', [App\Http\Controllers\Admin\SyllabusController::class, 'show'])->name('admin.syllabuses.show');
    Route::get('/syllabuses/{syllabus}/edit', [App\Http\Controllers\Admin\SyllabusController::class, 'edit'])->name('admin.syllabuses.edit');
    Route::put('/syllabuses/{syllabus}', [App\Http\Controllers\Admin\SyllabusController::class, 'update'])->name('admin.syllabuses.update');
    Route::delete('/syllabuses/{syllabus}', [App\Http\Controllers\Admin\SyllabusController::class, 'destroy'])->name('admin.syllabuses.destroy');
    Route::get('/syllabuses/{syllabus}/download', [App\Http\Controllers\Admin\SyllabusController::class, 'download'])->name('admin.syllabuses.download');
    Route::get('/syllabuses/{syllabus}/preview', [App\Http\Controllers\Admin\SyllabusController::class, 'preview'])->name('admin.syllabuses.preview');
    Route::get('/syllabuses/{syllabus}/content', [App\Http\Controllers\Admin\SyllabusController::class, 'getFileContent'])->name('admin.syllabuses.content');
    Route::get('/syllabuses/{syllabus}/pdf-viewer', [App\Http\Controllers\Admin\SyllabusController::class, 'getPdfViewerUrl'])->name('admin.syllabuses.pdf-viewer');
    Route::get('/syllabuses/{syllabus}/word-viewer', [App\Http\Controllers\Admin\SyllabusController::class, 'getWordViewerUrl'])->name('admin.syllabuses.word-viewer');
    Route::get('/syllabuses/{syllabus}/word-html', [App\Http\Controllers\Admin\SyllabusController::class, 'convertWordToHtml'])->name('admin.syllabuses.word-html');
    
    // Управление уроками
    Route::get('/lessons', function () {
        return Inertia::render('Admin/Lessons/Index');
    })->name('admin.lessons.index');
    
    Route::get('/lessons/create', function () {
        return Inertia::render('Admin/Lessons/Create');
    })->name('admin.lessons.create');
    
    Route::get('/lessons/{lesson}/edit', function ($lesson) {
        return Inertia::render('Admin/Lessons/Edit', ['lesson' => $lesson]);
    })->name('admin.lessons.edit');
    
    Route::get('/lessons/{lesson}/materials', function ($lesson) {
        return Inertia::render('Admin/Lessons/Materials', ['lesson' => $lesson]);
    })->name('admin.lessons.materials');
    
    // Управление тестами
    Route::get('/tests', function () {
        return Inertia::render('Admin/Tests/Index');
    })->name('admin.tests.index');
    
    Route::get('/tests/create', function () {
        return Inertia::render('Admin/Tests/Create');
    })->name('admin.tests.create');
    
    Route::get('/tests/{test}/edit', function ($test) {
        return Inertia::render('Admin/Tests/Edit', ['test' => $test]);
    })->name('admin.tests.edit');
    
    Route::get('/tests/{test}/questions', function ($test) {
        return Inertia::render('Admin/Tests/Questions', ['test' => $test]);
    })->name('admin.tests.questions');
    
    // Управление оценками
    Route::get('/grades', function () {
        return Inertia::render('Admin/Grades/Index');
    })->name('admin.grades.index');
    
    Route::get('/grades/students', function () {
        return Inertia::render('Admin/Grades/Students');
    })->name('admin.grades.students');
    
    Route::get('/grades/courses', function () {
        return Inertia::render('Admin/Grades/Courses');
    })->name('admin.grades.courses');
    
    // Управление расписанием (специфичные роуты ПЕРЕД resource)
    Route::get('/schedules/analytics', [App\Http\Controllers\Admin\ScheduleController::class, 'analytics'])->name('admin.schedules.analytics');
    Route::get('/schedules/bulk-create', [App\Http\Controllers\Admin\ScheduleController::class, 'bulkCreate'])->name('admin.schedules.bulk-create');
    Route::post('/schedules/bulk-store', [App\Http\Controllers\Admin\ScheduleController::class, 'bulkStore'])->name('admin.schedules.bulk-store');
    Route::get('/schedules/export', [App\Http\Controllers\Admin\ScheduleController::class, 'export'])->name('admin.schedules.export');
    Route::post('/schedules/export', [App\Http\Controllers\Admin\ScheduleController::class, 'export'])->name('admin.schedules.export.post');
    Route::post('/schedules/import', [App\Http\Controllers\Admin\ScheduleController::class, 'import'])->name('admin.schedules.import');
    Route::post('/schedules/bulk-action', [App\Http\Controllers\Admin\ScheduleController::class, 'bulkAction'])->name('admin.schedules.bulk-action');
    Route::post('/schedules/{schedule}/toggle-status', [App\Http\Controllers\Admin\ScheduleController::class, 'toggleStatus'])->name('admin.schedules.toggle-status');
    Route::post('/schedules/{schedule}/duplicate', [App\Http\Controllers\Admin\ScheduleController::class, 'duplicate'])->name('admin.schedules.duplicate');
    
    // Управление уроками в расписании
    Route::get('/schedules/{schedule}/lessons', [App\Http\Controllers\Admin\ScheduleController::class, 'lessons'])->name('admin.schedules.lessons');
    Route::post('/schedules/{schedule}/lessons', [App\Http\Controllers\Admin\ScheduleController::class, 'addLesson'])->name('admin.schedules.lessons.add');
    Route::delete('/schedules/{schedule}/lessons/{lesson}', [App\Http\Controllers\Admin\ScheduleController::class, 'removeLesson'])->name('admin.schedules.lessons.remove');
    Route::patch('/schedules/{schedule}/lessons/reorder', [App\Http\Controllers\Admin\ScheduleController::class, 'reorderLessons'])->name('admin.schedules.lessons.reorder');
    
    // Resource роут должен быть ПОСЛЕДНИМ
    Route::resource('schedules', App\Http\Controllers\Admin\ScheduleController::class)->names('admin.schedules');
    
    Route::resource('tests', App\Http\Controllers\Admin\TestController::class)->names('admin.tests');
Route::get('/tests/{test}/questions', [App\Http\Controllers\Admin\TestController::class, 'questions'])->name('admin.tests.questions');
Route::post('/tests/{test}/questions', [App\Http\Controllers\Admin\TestController::class, 'storeQuestion'])->name('admin.tests.questions.store');
Route::put('/tests/{test}/questions/{question}', [App\Http\Controllers\Admin\TestController::class, 'updateQuestion'])->name('admin.tests.questions.update');
Route::delete('/tests/{test}/questions/{question}', [App\Http\Controllers\Admin\TestController::class, 'destroyQuestion'])->name('admin.tests.questions.destroy');
Route::get('/tests/{test}/preview', [App\Http\Controllers\Admin\TestController::class, 'preview'])->name('admin.tests.preview');
    Route::get('/test-export', function() {
        return response('test')
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="test.txt"');
    })->name('admin.test-export');
});

// Тестовый маршрут без middleware для проверки
Route::get('/test-export-public', function() {
    return response('test public')
        ->header('Content-Type', 'text/plain')
        ->header('Content-Disposition', 'attachment; filename="test-public.txt"');
})->name('admin.test-export-public');

// Тестовый маршрут для проверки экспорта расписания
Route::get('/test-schedule-export', function() {
    try {
        $schedules = App\Models\Schedule::with(['lesson', 'teacher', 'group'])->get();
        return response()->json([
            'success' => true,
            'count' => $schedules->count(),
            'sample' => $schedules->first()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->name('admin.test-schedule-export');

// Тестовый маршрут для экспорта без middleware
Route::get('/test-export-schedule', function() {
    try {
        $schedules = App\Models\Schedule::with(['lesson', 'teacher', 'group'])->get();
        
        $filename = 'test_schedules_' . date('Y-m-d_H-i-s') . '.csv';
        $csvContent = '';
        $csvContent .= "\xEF\xBB\xBF"; // BOM для кириллицы
        
        $headers = ['ID', 'Урок', 'Преподаватель', 'Группа', 'Семестр', 'Кредиты', 'Год обучения', 'Порядок', 'Запланированная дата', 'Статус', 'Дата создания'];
        $csvContent .= implode(';', $headers) . "\n";
        
        foreach ($schedules as $schedule) {
            $row = [
                $schedule->id,
                $schedule->lesson->name ?? 'Не указано',
                $schedule->teacher->name ?? 'Не указано',
                $schedule->group->name ?? 'Не указано',
                $schedule->semester,
                $schedule->credits,
                $schedule->study_year,
                $schedule->order,
                $schedule->scheduled_at ? $schedule->scheduled_at->format('Y-m-d H:i:s') : 'Не указано',
                $schedule->is_active ? 'Активно' : 'Неактивно',
                $schedule->created_at->format('Y-m-d H:i:s')
            ];
            $csvContent .= implode(';', $row) . "\n";
        }
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->name('admin.test-export-schedule');

// Маршруты для учителей
Route::prefix('teacher')->middleware(['auth', 'teacher'])->group(function () {
    // Главная страница учителя
    Route::get('/', function () {
        return Inertia::render('Teacher/Dashboard');
    })->name('teacher.dashboard');
    
    // Мои курсы
    Route::get('/courses', function () {
        return Inertia::render('Teacher/Courses/Index');
    })->name('teacher.courses.index');
    
    Route::get('/courses/{course}', function ($course) {
        return Inertia::render('Teacher/Courses/Show', ['course' => $course]);
    })->name('teacher.courses.show');
    
    // Мои уроки (только просмотр)
    Route::get('/lessons', [App\Http\Controllers\Teacher\LessonController::class, 'index'])->name('teacher.lessons.index');
    Route::get('/lessons/{schedule}', [App\Http\Controllers\Teacher\LessonController::class, 'show'])->name('teacher.lessons.show');
    
    // Мои тесты
    Route::get('/tests', [App\Http\Controllers\Teacher\TestController::class, 'index'])->name('teacher.tests.index');
    Route::get('/tests/create', [App\Http\Controllers\Teacher\TestController::class, 'create'])->name('teacher.tests.create');
    Route::post('/tests', [App\Http\Controllers\Teacher\TestController::class, 'store'])->name('teacher.tests.store');
    Route::get('/tests/{test}', [App\Http\Controllers\Teacher\TestController::class, 'show'])->name('teacher.tests.show');
    Route::get('/tests/{test}/edit', [App\Http\Controllers\Teacher\TestController::class, 'edit'])->name('teacher.tests.edit');
    Route::put('/tests/{test}', [App\Http\Controllers\Teacher\TestController::class, 'update'])->name('teacher.tests.update');
    Route::delete('/tests/{test}', [App\Http\Controllers\Teacher\TestController::class, 'destroy'])->name('teacher.tests.destroy');
    Route::post('/tests/{test}/toggle-status', [App\Http\Controllers\Teacher\TestController::class, 'toggleStatus'])->name('teacher.tests.toggle-status');
    Route::post('/tests/{test}/copy', [App\Http\Controllers\Teacher\TestController::class, 'copyTest'])->name('teacher.tests.copy');
    
    // Оценки студентов
    Route::get('/grades', function () {
        return Inertia::render('Teacher/Grades/Index');
    })->name('teacher.grades.index');
    
    Route::get('/grades/students', function () {
        return Inertia::render('Teacher/Grades/Students');
    })->name('teacher.grades.students');
    
    // Мои студенты
    Route::get('/students', [App\Http\Controllers\Teacher\StudentController::class, 'index'])->name('teacher.students.index');
    Route::get('/students/group/{group}', [App\Http\Controllers\Teacher\StudentController::class, 'show'])->name('teacher.students.group');
    Route::get('/students/student/{student}', [App\Http\Controllers\Teacher\StudentController::class, 'showStudent'])->name('teacher.students.student');
    
    // Расписание
    Route::get('/schedule', function () {
        return Inertia::render('Teacher/Schedule/Index');
    })->name('teacher.schedule.index');
    
    // Материалы
    Route::get('/materials', function () {
        return Inertia::render('Teacher/Materials/Index');
    })->name('teacher.materials.index');
    
    Route::get('/materials/create', function () {
        return Inertia::render('Teacher/Materials/Create');
    })->name('teacher.materials.create');
    
    // Отчеты
    Route::get('/reports', function () {
        return Inertia::render('Teacher/Reports/Index');
    })->name('teacher.reports.index');
    
    // Профиль
    Route::get('/profile', function () {
        return Inertia::render('Teacher/Profile/Index');
    })->name('teacher.profile.index');
    
    // Настройки
    Route::get('/settings', function () {
        return Inertia::render('Teacher/Settings/Index');
    })->name('teacher.settings.index');
});
