<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Lesson;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    /**
     * Отобразить список расписания
     */
    public function index()
    {
        $schedules = Schedule::with(['lesson', 'teacher', 'group'])
            ->orderBy('scheduled_at', 'desc')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'lesson_name' => $schedule->lesson->name ?? 'Не указан',
                    'teacher_name' => $schedule->teacher->name ?? 'Не указан',
                    'group_name' => $schedule->group->name ?? 'Не указана',
                    'semester' => $schedule->semester,
                    'credits' => $schedule->credits,
                    'study_year' => $schedule->study_year,
                    'order' => $schedule->order,
                    'scheduled_at' => $schedule->scheduled_at ? $schedule->scheduled_at->format('d.m.Y H:i') : 'Не указано',
                    'is_active' => $schedule->is_active,
                    'created_at' => $schedule->created_at->format('d.m.Y H:i'),
                    'updated_at' => $schedule->updated_at->format('d.m.Y H:i'),
                ];
            });

        $lessons = Lesson::all()->map(function ($lesson) {
            return [
                'id' => $lesson->id,
                'name' => $lesson->name,
            ];
        });

        $groups = Group::all()->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
            ];
        });

        $teachers = User::whereHas('role', function($q) {
            $q->where('name', 'teacher');
        })->get()->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
            ];
        });

        return Inertia::render('Admin/Schedules/Index', [
            'schedules' => $schedules,
            'lessons' => $lessons,
            'groups' => $groups,
            'teachers' => $teachers,
        ]);
    }

    /**
     * Показать форму создания расписания
     */
    public function create()
    {
        $lessons = Lesson::all()->map(function ($lesson) {
            return [
                'id' => $lesson->id,
                'name' => $lesson->name,
            ];
        });

        $groups = Group::all()->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
            ];
        });

        $teachers = User::whereHas('role', function($q) {
            $q->where('name', 'teacher');
        })->get()->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
            ];
        });

        return Inertia::render('Admin/Schedules/Create', [
            'lessons' => $lessons,
            'groups' => $groups,
            'teachers' => $teachers,
        ]);
    }

    /**
     * Сохранить новое расписание
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'teacher_id' => 'required|exists:users,id',
            'group_id' => 'required|exists:groups,id',
            'semester' => 'required|integer|in:1,2',
            'credits' => 'required|integer|min:1|max:10',
            'study_year' => 'required|integer|min:2020|max:2030',
            'order' => 'required|integer|min:1',
            'scheduled_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Расписание успешно создано');
    }

    /**
     * Показать детали расписания
     */
    public function show(Schedule $schedule)
    {
        $schedule->load(['lesson', 'teacher', 'group']);

        return Inertia::render('Admin/Schedules/Show', [
            'schedule' => [
                'id' => $schedule->id,
                'lesson_name' => $schedule->lesson->name ?? 'Не указан',
                'teacher_name' => $schedule->teacher->name ?? 'Не указан',
                'group_name' => $schedule->group->name ?? 'Не указана',
                'semester' => $schedule->semester,
                'credits' => $schedule->credits,
                'study_year' => $schedule->study_year,
                'order' => $schedule->order,
                'scheduled_at' => $schedule->scheduled_at ? $schedule->scheduled_at->format('d.m.Y H:i') : 'Не указано',
                'is_active' => $schedule->is_active,
                'created_at' => $schedule->created_at->format('d.m.Y H:i'),
                'updated_at' => $schedule->updated_at->format('d.m.Y H:i'),
            ],
        ]);
    }

    /**
     * Показать форму редактирования расписания
     */
    public function edit(Schedule $schedule)
    {
        $schedule->load(['lesson', 'teacher', 'group']);

        $lessons = Lesson::all()->map(function ($lesson) {
            return [
                'id' => $lesson->id,
                'name' => $lesson->name,
            ];
        });

        $groups = Group::all()->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
            ];
        });

        $teachers = User::whereHas('role', function($q) {
            $q->where('name', 'teacher');
        })->get()->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
            ];
        });

        return Inertia::render('Admin/Schedules/Edit', [
            'schedule' => [
                'id' => $schedule->id,
                'lesson_id' => $schedule->lesson_id,
                'teacher_id' => $schedule->teacher_id,
                'group_id' => $schedule->group_id,
                'semester' => $schedule->semester,
                'credits' => $schedule->credits,
                'study_year' => $schedule->study_year,
                'order' => $schedule->order,
                'scheduled_at' => $schedule->scheduled_at ? $schedule->scheduled_at->format('Y-m-d\TH:i') : null,
                'is_active' => $schedule->is_active,
            ],
            'lessons' => $lessons,
            'groups' => $groups,
            'teachers' => $teachers,
        ]);
    }

    /**
     * Обновить расписание
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'teacher_id' => 'required|exists:users,id',
            'group_id' => 'required|exists:groups,id',
            'semester' => 'required|integer|in:1,2',
            'credits' => 'required|integer|min:1|max:10',
            'study_year' => 'required|integer|min:2020|max:2030',
            'order' => 'required|integer|min:1',
            'scheduled_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Расписание успешно обновлено');
    }

    /**
     * Удалить расписание
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Расписание успешно удалено');
    }

    /**
     * Импорт расписания из файла
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            
            // Здесь будет логика импорта
            // Пока просто возвращаем успешный ответ
            
            return response()->json([
                'success' => true,
                'message' => 'Файл успешно загружен: ' . $filename,
                'imported_count' => 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при импорте: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Экспорт расписания в файл
     */
    public function export(Request $request)
    {
        try {
            \Log::info('Начинаем экспорт расписания');
            
            // Получаем данные
            $schedules = Schedule::with(['lesson', 'teacher', 'group'])
                ->orderBy('scheduled_at', 'desc')
                ->get();
                
            \Log::info('Получено записей расписания: ' . $schedules->count());
            
            // Проверяем первую запись для диагностики
            if ($schedules->count() > 0) {
                $first = $schedules->first();
                \Log::info('Первая запись - ID: ' . $first->id . ', Lesson: ' . ($first->lesson ? $first->lesson->name : 'null') . ', Teacher: ' . ($first->teacher ? $first->teacher->name : 'null') . ', Group: ' . ($first->group ? $first->group->name : 'null'));
            }

            $filename = 'schedules_' . date('Y-m-d_H-i-s') . '.csv';

            // Создаем CSV содержимое
            $csvContent = '';
            $csvContent .= "\xEF\xBB\xBF"; // Добавляем BOM для корректного отображения кириллицы в Excel
            
            // Заголовки
            $headers = [
                'ID',
                'Урок',
                'Преподаватель', 
                'Группа',
                'Семестр',
                'Кредиты',
                'Год обучения',
                'Порядок',
                'Запланированная дата',
                'Статус',
                'Дата создания'
            ];
            
            $csvContent .= implode(';', $headers) . "\n";

            // Данные
            foreach ($schedules as $schedule) {
                try {
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
                } catch (\Exception $e) {
                    \Log::error('Ошибка при обработке записи ID ' . $schedule->id . ': ' . $e->getMessage());
                    // Пропускаем проблемную запись и продолжаем
                    continue;
                }
            }

            \Log::info('CSV содержимое создано, размер: ' . strlen($csvContent) . ' байт');
            
            // Возвращаем файл для скачивания
            return response($csvContent)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            \Log::error('Ошибка при экспорте: ' . $e->getMessage());
            \Log::error('Стек ошибки: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Ошибка при экспорте: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Массовое создание расписания
     */
    public function bulkCreate()
    {
        try {
            $lessons = Lesson::all()->map(function ($lesson) {
                return [
                    'id' => $lesson->id,
                    'name' => $lesson->name,
                ];
            });

            $groups = Group::all()->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                ];
            });

            // Получаем всех пользователей, если нет учителей
            $teachers = User::all()->map(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                ];
            });

            return Inertia::render('Admin/Schedules/BulkCreate', [
                'lessons' => $lessons,
                'groups' => $groups,
                'teachers' => $teachers,
            ]);
        } catch (\Exception $e) {
            \Log::error('Ошибка в bulkCreate: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ошибка при загрузке данных: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Сохранение массового создания
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'schedules' => 'required|array|min:1',
            'schedules.*.lesson_id' => 'required|exists:lessons,id',
            'schedules.*.teacher_id' => 'required|exists:users,id',
            'schedules.*.group_id' => 'required|exists:groups,id',
            'schedules.*.semester' => 'required|integer|in:1,2',
            'schedules.*.credits' => 'required|integer|min:1|max:10',
            'schedules.*.study_year' => 'required|integer|min:2020|max:2030',
            'schedules.*.order' => 'required|integer|min:1',
            'schedules.*.scheduled_at' => 'nullable|date',
            'schedules.*.is_active' => 'boolean',
        ]);

        $createdCount = 0;
        
        foreach ($validated['schedules'] as $scheduleData) {
            Schedule::create($scheduleData);
            $createdCount++;
        }

        return redirect()->route('admin.schedules.index')
            ->with('success', "Успешно создано {$createdCount} записей расписания");
    }

    /**
     * Аналитика расписания
     */
    public function analytics()
    {
        $totalSchedules = Schedule::count();
        $activeSchedules = Schedule::where('is_active', true)->count();
        $inactiveSchedules = Schedule::where('is_active', false)->count();

        // Статистика по семестрам
        $semesterStats = Schedule::selectRaw('semester, COUNT(*) as count')
            ->groupBy('semester')
            ->get();

        // Статистика по преподавателям
        $teacherStats = Schedule::with('teacher')
            ->selectRaw('teacher_id, COUNT(*) as count')
            ->groupBy('teacher_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Статистика по группам
        $groupStats = Schedule::with('group')
            ->selectRaw('group_id, COUNT(*) as count')
            ->groupBy('group_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Статистика по месяцам
        $monthlyStats = Schedule::selectRaw('MONTH(scheduled_at) as month, COUNT(*) as count')
            ->whereNotNull('scheduled_at')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return Inertia::render('Admin/Schedules/Analytics', [
            'totalSchedules' => $totalSchedules,
            'activeSchedules' => $activeSchedules,
            'inactiveSchedules' => $inactiveSchedules,
            'semesterStats' => $semesterStats,
            'teacherStats' => $teacherStats,
            'groupStats' => $groupStats,
            'monthlyStats' => $monthlyStats,
        ]);
    }
}
