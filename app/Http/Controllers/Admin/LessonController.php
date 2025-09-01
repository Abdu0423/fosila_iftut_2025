<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Department;
use Inertia\Inertia;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Lesson::with(['department']);

            // Получаем фильтры из сессии или запроса
            $search = $request->get('search', session('lessons_search'));
            $departmentId = $request->get('department_id', session('lessons_department_id'));
            $page = $request->get('page', 1);
            $sortBy = $request->get('sortBy', session('lessons_sort_by', 'title'));
            $sortDesc = $request->boolean('sortDesc', session('lessons_sort_desc', false));

            // Сохраняем фильтры в сессии
            session(['lessons_search' => $search]);
            session(['lessons_department_id' => $departmentId]);
            session(['lessons_sort_by' => $sortBy]);
            session(['lessons_sort_desc' => $sortDesc]);

            // Применяем фильтры
            if ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
            }

            if ($departmentId) {
                $query->where('department_id', $departmentId);
            }

            // Сортировка
            $query->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');

            $lessons = $query->paginate(15);
            $departments = Department::all();

            \Log::info('Lessons index called', [
                'lessons_count' => $lessons->count(),
                'departments_count' => $departments->count(),
                'filters' => [
                    'search' => $search,
                    'department_id' => $departmentId,
                    'sortBy' => $sortBy,
                    'sortDesc' => $sortDesc
                ]
            ]);

            return Inertia::render('Admin/Lessons/Index', [
                'lessons' => $lessons,
                'departments' => $departments,
                'filters' => [
                    'search' => $search,
                    'department_id' => $departmentId,
                    'sortBy' => $sortBy,
                    'sortDesc' => $sortDesc
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in lessons index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Inertia::render('Admin/Lessons/Index', [
                'lessons' => collect([])->paginate(15),
                'departments' => collect([]),
                'filters' => [],
                'error' => 'Произошла ошибка при загрузке данных'
            ]);
        }
    }

    /**
     * Обработка фильтров через POST запрос
     */
    public function filter(Request $request)
    {
        try {
            $query = Lesson::with(['department']);

            // Поиск по названию
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
            }

            // Фильтр по кафедре
            if ($request->filled('department_id')) {
                $query->where('department_id', $request->get('department_id'));
            }

            $lessons = $query->orderBy('title')->paginate(15);
            $departments = Department::all();

            \Log::info('Lessons filter called', [
                'lessons_count' => $lessons->count(),
                'departments_count' => $departments->count(),
                'filters' => $request->only(['search', 'department_id'])
            ]);

            return Inertia::render('Admin/Lessons/Index', [
                'lessons' => $lessons,
                'departments' => $departments,
                'filters' => $request->only(['search', 'department_id']),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in lessons filter', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Inertia::render('Admin/Lessons/Index', [
                'lessons' => collect([])->paginate(15),
                'departments' => collect([]),
                'filters' => $request->only(['search', 'department_id']),
                'error' => 'Произошла ошибка при фильтрации данных'
            ]);
        }
    }

    /**
     * Очистка фильтров
     */
    public function clearFilters()
    {
        session()->forget(['lessons_search', 'lessons_department_id', 'lessons_sort_by', 'lessons_sort_desc']);
        return redirect()->route('admin.lessons.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
        $totalLessons = Lesson::count();
        $activeLessons = Lesson::count();

        return Inertia::render('Admin/Lessons/Create', [
            'departments' => $departments,
            'totalLessons' => $totalLessons,
            'activeLessons' => $activeLessons,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
        ]);

        Lesson::create($request->all());

        return redirect()->route('admin.lessons.index')
            ->with('success', 'Урок создан успешно!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lesson $lesson)
    {
        $lesson->load(['department', 'schedules.teacher', 'schedules.group']);
        return Inertia::render('Admin/Lessons/Show', [
            'lesson' => $lesson,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson)
    {
        $departments = Department::all();

        return Inertia::render('Admin/Lessons/Edit', [
            'lesson' => $lesson,
            'departments' => $departments,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'course' => 'required|integer|min:1|max:6',
            'department_id' => 'required|exists:departments,id',
            'is_active' => 'boolean',
        ]);

        $lesson->update($request->all());

        return redirect()->route('admin.lessons.index')
            ->with('success', 'Урок обновлен успешно!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson)
    {
        $lesson->delete();

        return redirect()->route('admin.lessons.index')
            ->with('success', 'Урок удален успешно!');
    }
}
