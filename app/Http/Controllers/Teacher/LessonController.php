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
        
        // Получаем расписания с уроками для текущего учителя
        $schedules = Schedule::with(['lessons.subject', 'group', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Преобразуем данные для отображения
        $lessons = collect();
        foreach ($schedules as $schedule) {
            foreach ($schedule->lessons as $lesson) {
                $lessons->push([
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'description' => $lesson->description ?? '',
                    'subject_id' => $lesson->subject_id,
                    'subject_name' => $lesson->subject->name ?? 'Не указан',
                    'group_id' => $schedule->group_id,
                    'group_name' => $schedule->group->name ?? 'Не указана',
                    'teacher_name' => $teacher->name,
                    'schedule_id' => $schedule->id,
                    'schedule_name' => $schedule->subject->name ?? 'Расписание',
                    'file_name' => $lesson->file_name,
                    'file_size' => $lesson->file_size,
                    'is_active' => $schedule->is_active ?? true,
                    'created_at' => $lesson->created_at->format('d.m.Y H:i'),
                    'updated_at' => $lesson->updated_at->format('d.m.Y H:i'),
                ]);
            }
        }

        // Получаем предметы для фильтрации
        $subjects = Subject::orderBy('name')->get(['id', 'name']);
        
        // Получаем группы для фильтрации
        $groups = Group::orderBy('name')->get(['id', 'name']);

        // Получаем расписания для создания уроков
        $schedules = Schedule::where('teacher_id', $teacher->id)
            ->with(['subject', 'group'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Teacher/Lessons/Index', [
            'lessons' => $lessons,
            'subjects' => $subjects,
            'groups' => $groups,
            'schedules' => $schedules,
            'stats' => [
                'total' => $lessons->count(),
                'active' => $lessons->where('is_active', true)->count(),
                'inactive' => $lessons->where('is_active', false)->count(),
            ]
        ]);
    }



    /**
     * Показать форму создания урока
     */
    public function create()
    {
        $teacher = Auth::user();
        
        // Получаем расписания учителя для выбора
        $schedules = Schedule::where('teacher_id', $teacher->id)
            ->with(['subject', 'group'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Teacher/Lessons/Create', [
            'schedules' => $schedules,
        ]);
    }

    /**
     * Создать новый урок
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Начало создания урока', $request->all());
            
            // Логируем информацию о файле
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                \Log::info('Информация о файле', [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'extension' => $file->getClientOriginalExtension(),
                    'path' => $file->getPathname()
                ]);
            }
            
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'schedule_id' => 'required|exists:schedules,id',
                'file' => 'nullable|file|max:10240', // 10MB max - временно убираем ограничения по типу
            ]);

            $teacher = Auth::user();
            \Log::info('Учитель найден', ['teacher_id' => $teacher->id]);
            
            // Проверяем, что расписание принадлежит учителю
            $schedule = Schedule::where('id', $request->schedule_id)
                ->where('teacher_id', $teacher->id)
                ->firstOrFail();
            
            \Log::info('Расписание найдено', ['schedule_id' => $schedule->id, 'subject_id' => $schedule->subject_id]);

            $lessonData = [
                'title' => $request->title,
                'description' => $request->description,
                'subject_id' => $schedule->subject_id, // Берем предмет из расписания
            ];

            // Обработка загрузки файла
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('lessons', $fileName, 'public');
                
                $lessonData['file_name'] = $file->getClientOriginalName();
                $lessonData['file_size'] = $file->getSize();
                $lessonData['file_path'] = $filePath;
            }

            \Log::info('Данные урока для создания', $lessonData);
            $lesson = Lesson::create($lessonData);
            \Log::info('Урок создан', ['lesson_id' => $lesson->id]);

            // Добавляем урок к расписанию
            $schedule->lessons()->attach($lesson->id);
            \Log::info('Урок привязан к расписанию');

            if ($request->wantsJson() || $request->header('X-Inertia')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Урок успешно создан!',
                    'lesson' => $lesson
                ]);
            }
            
            return redirect()->route('teacher.lessons.index')
                ->with('success', 'Урок успешно создан!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Ошибка валидации', $e->errors());
            if ($request->wantsJson() || $request->header('X-Inertia')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Ошибка при создании урока', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            if ($request->wantsJson() || $request->header('X-Inertia')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при создании урока: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Ошибка при создании урока: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Показать урок
     */
    public function show(Lesson $lesson)
    {
        $teacher = Auth::user();
        
        // Проверяем, что урок принадлежит расписанию учителя
        $schedule = $lesson->schedules()
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$schedule) {
            abort(403, 'У вас нет доступа к этому уроку');
        }

        $lesson->load(['subject', 'schedules.group', 'schedules.subject']);

        return Inertia::render('Teacher/Lessons/Show', [
            'lesson' => $lesson,
            'schedule' => $schedule,
        ]);
    }

    /**
     * Показать форму редактирования урока
     */
    public function edit(Lesson $lesson)
    {
        $teacher = Auth::user();
        
        // Проверяем, что урок принадлежит расписанию учителя
        $schedule = $lesson->schedules()
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$schedule) {
            abort(403, 'У вас нет доступа к этому уроку');
        }

        // Получаем расписания учителя для выбора
        $schedules = Schedule::where('teacher_id', $teacher->id)
            ->with(['subject', 'group'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Teacher/Lessons/Edit', [
            'lesson' => $lesson,
            'schedules' => $schedules,
        ]);
    }

    /**
     * Обновить урок
     */
    public function update(Request $request, Lesson $lesson)
    {
        $teacher = Auth::user();
        
        // Проверяем, что урок принадлежит расписанию учителя
        $schedule = $lesson->schedules()
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$schedule) {
            abort(403, 'У вас нет доступа к этому уроку');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'schedule_id' => 'required|exists:schedules,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt|max:10240', // 10MB max
        ]);

        // Получаем новое расписание для обновления subject_id
        $newSchedule = Schedule::where('id', $request->schedule_id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'subject_id' => $newSchedule->subject_id, // Берем предмет из нового расписания
        ];

        // Обработка загрузки файла
        if ($request->hasFile('file')) {
            // Удаляем старый файл
            if ($lesson->file_path && \Storage::disk('public')->exists($lesson->file_path)) {
                \Storage::disk('public')->delete($lesson->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('lessons', $fileName, 'public');
            
            $updateData['file_name'] = $file->getClientOriginalName();
            $updateData['file_size'] = $file->getSize();
            $updateData['file_path'] = $filePath;
        }

        $lesson->update($updateData);

        // Обновляем связь с расписанием если изменилось
        if ($request->schedule_id != $schedule->id) {
            $lesson->schedules()->detach($schedule->id);
            $lesson->schedules()->attach($newSchedule->id);
        }

        return redirect()->route('teacher.lessons.index')
            ->with('success', 'Урок успешно обновлен!');
    }

    /**
     * Удалить урок
     */
    public function destroy(Lesson $lesson)
    {
        $teacher = Auth::user();
        
        // Проверяем, что урок принадлежит расписанию учителя
        $schedule = $lesson->schedules()
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$schedule) {
            abort(403, 'У вас нет доступа к этому уроку');
        }

        // Удаляем файл если он существует
        if ($lesson->file_path && \Storage::disk('public')->exists($lesson->file_path)) {
            \Storage::disk('public')->delete($lesson->file_path);
        }

        $lesson->delete();

        return redirect()->route('teacher.lessons.index')
            ->with('success', 'Урок успешно удален!');
    }


}
