<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\TestAnswer;
use App\Models\Lesson;
use App\Models\User;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем учителя
        $teacher = User::whereHas('role', function($query) {
            $query->where('name', 'teacher');
        })->first();

        if (!$teacher) {
            $this->command->error('Учитель не найден! Сначала создайте учителя.');
            return;
        }

        // Получаем уроки
        $mathLesson = Lesson::where('title', 'Алгебра и начала анализа')->first();
        $physicsLesson = Lesson::where('title', 'Механика')->first();
        $informaticsLesson = Lesson::where('title', 'Основы программирования')->first();

        if (!$mathLesson || !$physicsLesson || !$informaticsLesson) {
            $this->command->error('Уроки не найдены! Сначала создайте уроки.');
            return;
        }

        // Создаем тест по математике
        $mathTest = Test::create([
            'title' => 'Контрольная работа по алгебре',
            'description' => 'Тест по теме "Квадратные уравнения"',
            'lesson_id' => $mathLesson->id,
            'created_by' => $teacher->id,
            'type' => 'quiz',
            'time_limit' => 45,
            'passing_score' => 70,
            'max_attempts' => 2,
            'is_active' => true,
            'is_public' => true,
        ]);

        // Вопросы для теста по математике
        $mathQuestions = [
            [
                'question' => 'Решите уравнение: x² - 5x + 6 = 0',
                'type' => 'single_choice',
                'order' => 1,
                'explanation' => 'Используйте формулу дискриминанта: D = b² - 4ac',
                'answers' => [
                    ['answer' => 'x = 2, x = 3', 'is_correct' => true, 'order' => 1],
                    ['answer' => 'x = -2, x = -3', 'is_correct' => false, 'order' => 2],
                    ['answer' => 'x = 1, x = 6', 'is_correct' => false, 'order' => 3],
                    ['answer' => 'x = -1, x = -6', 'is_correct' => false, 'order' => 4],
                ]
            ],
            [
                'question' => 'Какая формула используется для решения квадратного уравнения?',
                'type' => 'single_choice',
                'order' => 2,
                'explanation' => 'Формула корней квадратного уравнения: x = (-b ± √D) / 2a',
                'answers' => [
                    ['answer' => 'x = (-b ± √(b² - 4ac)) / 2a', 'is_correct' => true, 'order' => 1],
                    ['answer' => 'x = b ± √(b² - 4ac)', 'is_correct' => false, 'order' => 2],
                    ['answer' => 'x = (-b ± √(b² + 4ac)) / 2a', 'is_correct' => false, 'order' => 3],
                    ['answer' => 'x = b ± √(b² + 4ac)', 'is_correct' => false, 'order' => 4],
                ]
            ],
            [
                'question' => 'Выберите все правильные утверждения о квадратных уравнениях:',
                'type' => 'multiple_choice',
                'order' => 3,
                'explanation' => 'Квадратное уравнение может иметь 0, 1 или 2 корня в зависимости от дискриминанта',
                'answers' => [
                    ['answer' => 'Квадратное уравнение всегда имеет два корня', 'is_correct' => false, 'order' => 1],
                    ['answer' => 'Если дискриминант равен нулю, то уравнение имеет один корень', 'is_correct' => true, 'order' => 2],
                    ['answer' => 'Если дискриминант отрицательный, то уравнение не имеет корней', 'is_correct' => true, 'order' => 3],
                    ['answer' => 'Коэффициент a не может быть равен нулю', 'is_correct' => true, 'order' => 4],
                ]
            ],
        ];

        foreach ($mathQuestions as $questionData) {
            $question = TestQuestion::create([
                'test_id' => $mathTest->id,
                'question' => $questionData['question'],
                'type' => $questionData['type'],
                'order' => $questionData['order'],
                'explanation' => $questionData['explanation'],
            ]);

            foreach ($questionData['answers'] as $answerData) {
                TestAnswer::create([
                    'question_id' => $question->id,
                    'answer' => $answerData['answer'],
                    'is_correct' => $answerData['is_correct'],
                    'order' => $answerData['order'],
                ]);
            }
        }

        // Создаем тест по физике
        $physicsTest = Test::create([
            'title' => 'Тест по механике',
            'description' => 'Проверка знаний по основам механики',
            'lesson_id' => $physicsLesson->id,
            'created_by' => $teacher->id,
            'type' => 'exam',
            'time_limit' => 60,
            'passing_score' => 80,
            'max_attempts' => 1,
            'is_active' => true,
            'is_public' => false,
        ]);

        // Вопросы для теста по физике
        $physicsQuestions = [
            [
                'question' => 'Какая формула описывает второй закон Ньютона?',
                'type' => 'single_choice',
                'order' => 1,
                'explanation' => 'F = ma - сила равна произведению массы на ускорение',
                'answers' => [
                    ['answer' => 'F = ma', 'is_correct' => true, 'order' => 1],
                    ['answer' => 'F = mv', 'is_correct' => false, 'order' => 2],
                    ['answer' => 'F = mgh', 'is_correct' => false, 'order' => 3],
                    ['answer' => 'F = kx', 'is_correct' => false, 'order' => 4],
                ]
            ],
            [
                'question' => 'Верно ли утверждение: "Сила тяжести всегда направлена вниз"?',
                'type' => 'single_choice',
                'order' => 2,
                'explanation' => 'Сила тяжести направлена к центру Земли',
                'answers' => [
                    ['answer' => 'Верно', 'is_correct' => true, 'order' => 1],
                    ['answer' => 'Неверно', 'is_correct' => false, 'order' => 2],
                ]
            ],
        ];

        foreach ($physicsQuestions as $questionData) {
            $question = TestQuestion::create([
                'test_id' => $physicsTest->id,
                'question' => $questionData['question'],
                'type' => $questionData['type'],
                'order' => $questionData['order'],
                'explanation' => $questionData['explanation'],
            ]);

            foreach ($questionData['answers'] as $answerData) {
                TestAnswer::create([
                    'question_id' => $question->id,
                    'answer' => $answerData['answer'],
                    'is_correct' => $answerData['is_correct'],
                    'order' => $answerData['order'],
                ]);
            }
        }

        // Создаем тест по информатике
        $informaticsTest = Test::create([
            'title' => 'Домашнее задание по программированию',
            'description' => 'Практическое задание по основам Python',
            'lesson_id' => $informaticsLesson->id,
            'created_by' => $teacher->id,
            'type' => 'homework',
            'time_limit' => 120,
            'passing_score' => 60,
            'max_attempts' => 3,
            'is_active' => true,
            'is_public' => true,
        ]);

        // Вопросы для теста по информатике
        $informaticsQuestions = [
            [
                'question' => 'Что выведет код: print("Hello" + " " + "World")?',
                'type' => 'single_choice',
                'order' => 1,
                'explanation' => 'Оператор + для строк выполняет конкатенацию',
                'answers' => [
                    ['answer' => 'Hello World', 'is_correct' => true, 'order' => 1],
                    ['answer' => 'HelloWorld', 'is_correct' => false, 'order' => 2],
                    ['answer' => 'Hello + World', 'is_correct' => false, 'order' => 3],
                    ['answer' => 'Ошибка', 'is_correct' => false, 'order' => 4],
                ]
            ],
            [
                'question' => 'Выберите правильные способы создания списка в Python:',
                'type' => 'multiple_choice',
                'order' => 2,
                'explanation' => 'Список можно создать квадратными скобками или функцией list()',
                'answers' => [
                    ['answer' => 'my_list = [1, 2, 3]', 'is_correct' => true, 'order' => 1],
                    ['answer' => 'my_list = list()', 'is_correct' => true, 'order' => 2],
                    ['answer' => 'my_list = (1, 2, 3)', 'is_correct' => false, 'order' => 3],
                    ['answer' => 'my_list = {1, 2, 3}', 'is_correct' => false, 'order' => 4],
                ]
            ],
        ];

        foreach ($informaticsQuestions as $questionData) {
            $question = TestQuestion::create([
                'test_id' => $informaticsTest->id,
                'question' => $questionData['question'],
                'type' => $questionData['type'],
                'order' => $questionData['order'],
                'explanation' => $questionData['explanation'],
            ]);

            foreach ($questionData['answers'] as $answerData) {
                TestAnswer::create([
                    'question_id' => $question->id,
                    'answer' => $answerData['answer'],
                    'is_correct' => $answerData['is_correct'],
                    'order' => $answerData['order'],
                ]);
            }
        }

        $this->command->info('Тесты успешно созданы!');
    }
}
