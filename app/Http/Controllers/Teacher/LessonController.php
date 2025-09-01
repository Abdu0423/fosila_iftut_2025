<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Group;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        
        // Получаем расписание уроков для текущего учителя
        $schedules = Schedule::with(['lesson', 'group', 'teacher', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'lesson_id' => $schedule->lesson_id,
                    'title' => $schedule->lesson->title ?? 'Урок без названия',
                    'description' => $schedule->lesson->description ?? '',
                    'subject_id' => $schedule->subject_id,
                    'subject_name' => $schedule->subject->name ?? 'Не указан',
                    'group_id' => $schedule->group_id,
                    'group_name' => $schedule->group->name ?? 'Не указана',
                    'teacher_name' => $schedule->teacher->name ?? 'Не указан',
                    'day_of_week' => $schedule->day_of_week ?? '',
                    'start_time' => $schedule->start_time ?? '',
                    'end_time' => $schedule->end_time ?? '',
                    'room' => $schedule->room ?? '',
                    'semester' => $schedule->semester ?? '',
                    'credits' => $schedule->credits ?? '',
                    'study_year' => $schedule->study_year ?? '',
                    'order' => $schedule->order ?? '',
                    'scheduled_at' => $schedule->scheduled_at ? $schedule->scheduled_at->format('d.m.Y H:i') : '',
                    'is_active' => $schedule->is_active ?? true,
                    'created_at' => $schedule->created_at->format('d.m.Y H:i'),
                    'updated_at' => $schedule->updated_at->format('d.m.Y H:i'),
                ];
            });

        // Получаем предметы для фильтрации
        $subjects = Subject::orderBy('name')->get(['id', 'name']);
        
        // Получаем группы для фильтрации
        $groups = Group::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Teacher/Lessons/Index', [
            'lessons' => $schedules,
            'subjects' => $subjects,
            'groups' => $groups,
            'stats' => [
                'total' => $schedules->count(),
                'active' => $schedules->where('is_active', true)->count(),
                'inactive' => $schedules->where('is_active', false)->count(),
            ]
        ]);
    }



    public function show(Schedule $schedule)
    {
        // Проверяем, что расписание принадлежит текущему учителю
        if ($schedule->teacher_id !== Auth::id()) {
            abort(403, 'У вас нет доступа к этому уроку');
        }

        $schedule->load(['lesson', 'subject', 'group', 'teacher']);

        // Получаем студентов группы через связь many-to-many
        $students = $schedule->group->students()
            ->whereHas('role', function($query) {
                $query->where('name', 'student');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'last_name', 'middle_name', 'email', 'phone']);

        return Inertia::render('Teacher/Lessons/Show', [
            'lesson' => [
                'id' => $schedule->id,
                'lesson_id' => $schedule->lesson_id,
                'title' => $schedule->lesson->title ?? 'Урок без названия',
                'description' => $schedule->lesson->description ?? '',
                'subject_name' => $schedule->subject->name ?? 'Не указан',
                'subject_id' => $schedule->subject_id,
                'group_name' => $schedule->group->name ?? 'Не указана',
                'group_id' => $schedule->group_id,
                'teacher_name' => $schedule->teacher->name ?? 'Не указан',
                'teacher_id' => $schedule->teacher_id,
                'day_of_week' => $schedule->day_of_week ?? '',
                'start_time' => $schedule->start_time ?? '',
                'end_time' => $schedule->end_time ?? '',
                'room' => $schedule->room ?? '',
                'semester' => $schedule->semester ?? '',
                'credits' => $schedule->credits ?? '',
                'study_year' => $schedule->study_year ?? '',
                'order' => $schedule->order ?? '',
                'scheduled_at' => $schedule->scheduled_at ? $schedule->scheduled_at->format('d.m.Y H:i') : '',
                'is_active' => $schedule->is_active ?? true,
                'created_at' => $schedule->created_at->format('d.m.Y H:i'),
                'updated_at' => $schedule->updated_at->format('d.m.Y H:i'),
            ],
            'students' => $students->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'last_name' => $student->last_name,
                    'middle_name' => $student->middle_name,
                    'full_name' => trim($student->last_name . ' ' . $student->name . ' ' . $student->middle_name),
                    'email' => $student->email,
                    'phone' => $student->phone,
                ];
            })
        ]);
    }


}
