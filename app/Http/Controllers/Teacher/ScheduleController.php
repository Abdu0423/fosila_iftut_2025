<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Syllabus;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    /**
     * Показать расписание текущего учителя
     */
    public function index()
    {
        $teacher = Auth::user();
        
        // Получаем расписания текущего учителя
        $schedules = Schedule::where('teacher_id', $teacher->id)
            ->with(['subject', 'group', 'syllabuses', 'lessons.subject'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Получаем доступные предметы для создания новых расписаний
        $subjects = Subject::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Получаем доступные группы
        $groups = Group::where('status', 'active')
            ->orderBy('name')
            ->get();

        // Получаем доступные силлабусы для добавления
        $syllabuses = Syllabus::with(['subject', 'creator'])
            ->orderBy('name')
            ->get();

        // Получаем доступные уроки для добавления
        $lessons = Lesson::with(['subject'])
            ->orderBy('title')
            ->get();

        return Inertia::render('Teacher/Schedule/Index', [
            'schedules' => $schedules,
            'subjects' => $subjects,
            'groups' => $groups,
            'syllabuses' => $syllabuses,
            'lessons' => $lessons,
        ]);
    }

    /**
     * Создать новое расписание
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'group_id' => 'required|exists:groups,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string|max:1000',
        ]);

        $teacher = Auth::user();

        $schedule = Schedule::create([
            'subject_id' => $request->subject_id,
            'teacher_id' => $teacher->id,
            'group_id' => $request->group_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('teacher.schedule.index')
            ->with('success', 'Расписание создано успешно!');
    }

    /**
     * Показать конкретное расписание
     */
    public function show(Schedule $schedule)
    {
        $teacher = Auth::user();
        
        // Проверяем, что расписание принадлежит текущему учителю
        if ($schedule->teacher_id !== $teacher->id) {
            abort(403, 'У вас нет доступа к этому расписанию');
        }

        $schedule->load(['subject', 'group', 'syllabuses.creator', 'lessons.subject']);

        // Получаем доступные предметы для редактирования
        $subjects = Subject::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Получаем доступные группы
        $groups = Group::where('status', 'active')
            ->orderBy('name')
            ->get();

        // Получаем доступные силлабусы для добавления
        $availableSyllabuses = Syllabus::with(['subject', 'creator'])
            ->whereNotIn('id', $schedule->syllabuses->pluck('id'))
            ->orderBy('name')
            ->get();

        // Получаем доступные уроки для добавления
        $availableLessons = Lesson::with(['subject'])
            ->whereNotIn('id', $schedule->lessons->pluck('id'))
            ->orderBy('title')
            ->get();

        return Inertia::render('Teacher/Schedule/Show', [
            'schedule' => $schedule,
            'subjects' => $subjects,
            'groups' => $groups,
            'availableSyllabuses' => $availableSyllabuses,
            'availableLessons' => $availableLessons,
        ]);
    }

    /**
     * Обновить расписание
     */
    public function update(Request $request, Schedule $schedule)
    {
        $teacher = Auth::user();
        
        // Проверяем, что расписание принадлежит текущему учителю
        if ($schedule->teacher_id !== $teacher->id) {
            abort(403, 'У вас нет доступа к этому расписанию');
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'group_id' => 'required|exists:groups,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $schedule->update($request->all());

        return redirect()->route('teacher.schedule.index')
            ->with('success', 'Расписание обновлено успешно!');
    }

    /**
     * Удалить расписание
     */
    public function destroy(Schedule $schedule)
    {
        $teacher = Auth::user();
        
        // Проверяем, что расписание принадлежит текущему учителю
        if ($schedule->teacher_id !== $teacher->id) {
            abort(403, 'У вас нет доступа к этому расписанию');
        }

        $schedule->delete();

        return redirect()->route('teacher.schedule.index')
            ->with('success', 'Расписание удалено успешно!');
    }

    /**
     * Добавить силлабус к расписанию
     */
    public function addSyllabus(Request $request, Schedule $schedule)
    {
        $teacher = Auth::user();
        
        // Проверяем, что расписание принадлежит текущему учителю
        if ($schedule->teacher_id !== $teacher->id) {
            abort(403, 'У вас нет доступа к этому расписанию');
        }

        $request->validate([
            'syllabus_ids' => 'required|array',
            'syllabus_ids.*' => 'exists:syllabuses,id',
        ]);

        // Добавляем силлабусы к расписанию
        $schedule->syllabuses()->sync($request->syllabus_ids);

        return response()->json([
            'success' => true,
            'message' => 'Силлабусы добавлены к расписанию'
        ]);
    }

    /**
     * Добавить урок к расписанию
     */
    public function addLesson(Request $request, Schedule $schedule)
    {
        $teacher = Auth::user();
        
        // Проверяем, что расписание принадлежит текущему учителю
        if ($schedule->teacher_id !== $teacher->id) {
            abort(403, 'У вас нет доступа к этому расписанию');
        }

        $request->validate([
            'lesson_ids' => 'required|array',
            'lesson_ids.*' => 'exists:lessons,id',
        ]);

        // Получаем текущий максимальный порядок
        $maxOrder = $schedule->lessons()->max('lesson_schedule.order') ?? 0;

        // Добавляем уроки к расписанию с порядком
        $lessonData = [];
        foreach ($request->lesson_ids as $index => $lessonId) {
            $lessonData[$lessonId] = [
                'order' => $maxOrder + $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $schedule->lessons()->sync($lessonData);

        return response()->json([
            'success' => true,
            'message' => 'Уроки добавлены к расписанию'
        ]);
    }

    /**
     * Удалить силлабус из расписания
     */
    public function removeSyllabus(Schedule $schedule, Syllabus $syllabus)
    {
        $teacher = Auth::user();
        
        // Проверяем, что расписание принадлежит текущему учителю
        if ($schedule->teacher_id !== $teacher->id) {
            abort(403, 'У вас нет доступа к этому расписанию');
        }

        $schedule->syllabuses()->detach($syllabus->id);

        return response()->json([
            'success' => true,
            'message' => 'Силлабус удален из расписания'
        ]);
    }

    /**
     * Удалить урок из расписания
     */
    public function removeLesson(Schedule $schedule, Lesson $lesson)
    {
        $teacher = Auth::user();
        
        // Проверяем, что расписание принадлежит текущему учителю
        if ($schedule->teacher_id !== $teacher->id) {
            abort(403, 'У вас нет доступа к этому расписанию');
        }

        $schedule->lessons()->detach($lesson->id);

        return response()->json([
            'success' => true,
            'message' => 'Урок удален из расписания'
        ]);
    }

    /**
     * Изменить порядок уроков в расписании
     */
    public function reorderLessons(Request $request, Schedule $schedule)
    {
        $teacher = Auth::user();
        
        // Проверяем, что расписание принадлежит текущему учителю
        if ($schedule->teacher_id !== $teacher->id) {
            abort(403, 'У вас нет доступа к этому расписанию');
        }

        $request->validate([
            'lesson_orders' => 'required|array',
            'lesson_orders.*.lesson_id' => 'required|exists:lessons,id',
            'lesson_orders.*.order' => 'required|integer|min:1',
        ]);

        foreach ($request->lesson_orders as $item) {
            $schedule->lessons()->updateExistingPivot($item['lesson_id'], [
                'order' => $item['order']
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Порядок уроков обновлен'
        ]);
    }
}