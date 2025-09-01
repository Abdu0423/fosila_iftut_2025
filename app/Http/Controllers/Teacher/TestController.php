<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\TestAnswer;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        
        // Получаем тесты, созданные учителем
        $tests = Test::where('created_by', $teacher->id)
            ->with(['lesson', 'creator', 'questions'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($test) {
                return [
                    'id' => $test->id,
                    'title' => $test->title,
                    'description' => $test->description,
                    'lesson_title' => $test->lesson->title ?? 'Не указан урок',
                    'lesson_id' => $test->lesson_id,
                    'type' => $test->type,
                    'time_limit' => $test->time_limit,
                    'passing_score' => $test->passing_score,
                    'max_attempts' => $test->max_attempts,
                    'is_active' => $test->is_active,
                    'is_public' => $test->is_public,
                    'start_date' => $test->start_date ? $test->start_date->format('d.m.Y H:i') : null,
                    'end_date' => $test->end_date ? $test->end_date->format('d.m.Y H:i') : null,
                    'status' => $test->status,
                    'questions_count' => $test->questions->count(),
                    'created_at' => $test->created_at->format('d.m.Y H:i'),
                    'updated_at' => $test->updated_at->format('d.m.Y H:i'),
                ];
            });

        // Получаем уроки учителя для создания тестов
        $lessons = Schedule::where('teacher_id', $teacher->id)
            ->with(['lesson.subject'])
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->lesson_id,
                    'title' => $schedule->lesson->title ?? 'Урок без названия',
                    'subject_name' => $schedule->lesson->subject->name ?? 'Не указан',
                    'schedule_id' => $schedule->id,
                ];
            });

        return Inertia::render('Teacher/Tests/Index', [
            'tests' => $tests,
            'lessons' => $lessons,
            'stats' => [
                'total' => $tests->count(),
                'active' => $tests->where('is_active', true)->count(),
                'inactive' => $tests->where('is_active', false)->count(),
            ]
        ]);
    }

    public function create()
    {
        $teacher = Auth::user();
        
        // Получаем уроки учителя
        $lessons = Schedule::where('teacher_id', $teacher->id)
            ->with(['lesson.subject'])
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->lesson_id,
                    'title' => $schedule->lesson->title ?? 'Урок без названия',
                    'subject_name' => $schedule->lesson->subject->name ?? 'Не указан',
                    'schedule_id' => $schedule->id,
                ];
            });

        // Получаем доступные тесты из других уроков того же предмета
        $availableTests = $this->getAvailableTests($teacher);

        return Inertia::render('Teacher/Tests/Create', [
            'lessons' => $lessons,
            'availableTests' => $availableTests,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lesson_id' => 'required|exists:lessons,id',
            'type' => 'required|in:quiz,exam,homework',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'max_attempts' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        // Проверяем, что урок принадлежит учителю
        $hasAccess = Schedule::where('teacher_id', Auth::id())
            ->where('lesson_id', $request->lesson_id)
            ->exists();
            
        if (!$hasAccess) {
            abort(403, 'У вас нет доступа к этому уроку');
        }

        $test = Test::create([
            'title' => $request->title,
            'description' => $request->description,
            'lesson_id' => $request->lesson_id,
            'created_by' => Auth::id(),
            'type' => $request->type,
            'time_limit' => $request->time_limit,
            'passing_score' => $request->passing_score,
            'max_attempts' => $request->max_attempts,
            'is_active' => $request->boolean('is_active', true),
            'is_public' => $request->boolean('is_public', false),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('teacher.tests.index')
            ->with('success', 'Тест успешно создан!');
    }

    public function show(Test $test)
    {
        // Проверяем, что тест принадлежит учителю
        if ($test->created_by !== Auth::id()) {
            abort(403, 'У вас нет доступа к этому тесту');
        }

        $test->load(['lesson', 'creator', 'questions.answers']);

        return Inertia::render('Teacher/Tests/Show', [
            'test' => [
                'id' => $test->id,
                'title' => $test->title,
                'description' => $test->description,
                'lesson_title' => $test->lesson->title ?? 'Не указан урок',
                'lesson_id' => $test->lesson_id,
                'type' => $test->type,
                'time_limit' => $test->time_limit,
                'passing_score' => $test->passing_score,
                'max_attempts' => $test->max_attempts,
                'is_active' => $test->is_active,
                'is_public' => $test->is_public,
                'start_date' => $test->start_date ? $test->start_date->format('Y-m-d\TH:i') : null,
                'end_date' => $test->end_date ? $test->end_date->format('Y-m-d\TH:i') : null,
                'status' => $test->status,
                'created_at' => $test->created_at->format('d.m.Y H:i'),
                'updated_at' => $test->updated_at->format('d.m.Y H:i'),
                'questions' => $test->questions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'question' => $question->question,
                        'type' => $question->type,
                        'order' => $question->order,
                        'explanation' => $question->explanation,
                        'answers' => $question->answers->map(function ($answer) {
                            return [
                                'id' => $answer->id,
                                'answer' => $answer->answer,
                                'is_correct' => $answer->is_correct,
                                'order' => $answer->order,
                            ];
                        })
                    ];
                })
            ]
        ]);
    }

    public function edit(Test $test)
    {
        // Проверяем, что тест принадлежит учителю
        if ($test->created_by !== Auth::id()) {
            abort(403, 'У вас нет доступа к этому тесту');
        }

        $teacher = Auth::user();
        
        // Получаем уроки учителя
        $lessons = Schedule::where('teacher_id', $teacher->id)
            ->with(['lesson.subject'])
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->lesson_id,
                    'title' => $schedule->lesson->title ?? 'Урок без названия',
                    'subject_name' => $schedule->lesson->subject->name ?? 'Не указан',
                    'schedule_id' => $schedule->id,
                ];
            });

        return Inertia::render('Teacher/Tests/Edit', [
            'test' => [
                'id' => $test->id,
                'title' => $test->title,
                'description' => $test->description,
                'lesson_id' => $test->lesson_id,
                'type' => $test->type,
                'time_limit' => $test->time_limit,
                'passing_score' => $test->passing_score,
                'max_attempts' => $test->max_attempts,
                'is_active' => $test->is_active,
                'is_public' => $test->is_public,
                'start_date' => $test->start_date ? $test->start_date->format('Y-m-d\TH:i') : null,
                'end_date' => $test->end_date ? $test->end_date->format('Y-m-d\TH:i') : null,
            ],
            'lessons' => $lessons,
        ]);
    }

    public function update(Request $request, Test $test)
    {
        // Проверяем, что тест принадлежит учителю
        if ($test->created_by !== Auth::id()) {
            abort(403, 'У вас нет доступа к этому тесту');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lesson_id' => 'required|exists:lessons,id',
            'type' => 'required|in:quiz,exam,homework',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'max_attempts' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        // Проверяем, что урок принадлежит учителю
        $hasAccess = Schedule::where('teacher_id', Auth::id())
            ->where('lesson_id', $request->lesson_id)
            ->exists();
            
        if (!$hasAccess) {
            abort(403, 'У вас нет доступа к этому уроку');
        }

        $test->update([
            'title' => $request->title,
            'description' => $request->description,
            'lesson_id' => $request->lesson_id,
            'type' => $request->type,
            'time_limit' => $request->time_limit,
            'passing_score' => $request->passing_score,
            'max_attempts' => $request->max_attempts,
            'is_active' => $request->boolean('is_active', true),
            'is_public' => $request->boolean('is_public', false),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('teacher.tests.index')
            ->with('success', 'Тест успешно обновлен!');
    }

    public function destroy(Test $test)
    {
        // Проверяем, что тест принадлежит учителю
        if ($test->created_by !== Auth::id()) {
            abort(403, 'У вас нет доступа к этому тесту');
        }

        $test->delete();

        return redirect()->route('teacher.tests.index')
            ->with('success', 'Тест успешно удален!');
    }

    public function toggleStatus(Test $test)
    {
        // Проверяем, что тест принадлежит учителю
        if ($test->created_by !== Auth::id()) {
            abort(403, 'У вас нет доступа к этому тесту');
        }

        $test->update([
            'is_active' => !$test->is_active
        ]);

        return redirect()->back()
            ->with('success', 'Статус теста изменен!');
    }

    public function copyTest(Request $request, Test $sourceTest)
    {
        // Проверяем, что исходный тест доступен учителю
        $teacher = Auth::user();
        $hasAccess = $this->checkTestAccess($sourceTest, $teacher);
        
        if (!$hasAccess) {
            abort(403, 'У вас нет доступа к этому тесту');
        }

        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
        ]);

        // Проверяем, что целевой урок принадлежит учителю
        $hasAccess = Schedule::where('teacher_id', $teacher->id)
            ->where('lesson_id', $request->lesson_id)
            ->exists();
            
        if (!$hasAccess) {
            abort(403, 'У вас нет доступа к этому уроку');
        }

        DB::transaction(function () use ($sourceTest, $request, $teacher) {
            // Создаем новый тест
            $newTest = Test::create([
                'title' => $request->title,
                'description' => $sourceTest->description,
                'lesson_id' => $request->lesson_id,
                'created_by' => $teacher->id,
                'type' => $sourceTest->type,
                'time_limit' => $sourceTest->time_limit,
                'passing_score' => $sourceTest->passing_score,
                'max_attempts' => $sourceTest->max_attempts,
                'is_active' => false, // Новый тест неактивен по умолчанию
                'is_public' => false,
            ]);

            // Копируем вопросы и ответы
            foreach ($sourceTest->questions as $question) {
                $newQuestion = TestQuestion::create([
                    'test_id' => $newTest->id,
                    'question' => $question->question,
                    'type' => $question->type,
                    'order' => $question->order,
                    'explanation' => $question->explanation,
                ]);

                foreach ($question->answers as $answer) {
                    TestAnswer::create([
                        'question_id' => $newQuestion->id,
                        'answer' => $answer->answer,
                        'is_correct' => $answer->is_correct,
                        'order' => $answer->order,
                        'matching_key' => $answer->matching_key,
                        'matching_value' => $answer->matching_value,
                    ]);
                }
            }
        });

        return redirect()->route('teacher.tests.index')
            ->with('success', 'Тест успешно скопирован!');
    }

    private function getAvailableTests($teacher)
    {
        // Получаем предметы, которые преподает учитель
        $teacherSubjects = Schedule::where('teacher_id', $teacher->id)
            ->with('subject')
            ->get()
            ->pluck('subject_id')
            ->unique();

        // Получаем тесты из уроков того же предмета
        return Test::whereHas('lesson.schedules', function ($query) use ($teacherSubjects) {
            $query->whereIn('subject_id', $teacherSubjects);
        })
        ->with(['lesson', 'creator'])
        ->where('is_public', true) // Только публичные тесты
        ->where('created_by', '!=', $teacher->id) // Не собственные тесты
        ->get()
        ->map(function ($test) {
            return [
                'id' => $test->id,
                'title' => $test->title,
                'description' => $test->description,
                'lesson_title' => $test->lesson->title ?? 'Не указан урок',
                'creator_name' => $test->creator->name ?? 'Не указан',
                'type' => $test->type,
                'questions_count' => $test->questions->count(),
            ];
        });
    }

    private function checkTestAccess($test, $teacher)
    {
        // Проверяем, что тест публичный или принадлежит учителю
        if ($test->created_by === $teacher->id) {
            return true;
        }

        if (!$test->is_public) {
            return false;
        }

        // Проверяем, что тест из урока того же предмета
        $teacherSubjects = Schedule::where('teacher_id', $teacher->id)
            ->pluck('subject_id')
            ->unique();

        return $test->lesson->schedules()
            ->whereIn('subject_id', $teacherSubjects)
            ->exists();
    }
}
