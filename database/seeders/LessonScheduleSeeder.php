<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\Lesson;

class LessonScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем все расписания и уроки
        $schedules = Schedule::all();
        $lessons = Lesson::where('is_active', true)->get();

        if ($schedules->isEmpty() || $lessons->isEmpty()) {
            $this->command->info('Нет данных для создания связей между расписанием и уроками');
            return;
        }

        foreach ($schedules as $schedule) {
            // Случайным образом выбираем 2-5 уроков для каждого расписания
            $lessonCount = rand(2, min(5, $lessons->count()));
            $randomLessons = $lessons->random($lessonCount);

            $lessonData = [];
            foreach ($randomLessons as $index => $lesson) {
                $lessonData[$lesson->id] = [
                    'order' => $index + 1,
                    'duration' => rand(45, 120), // от 45 до 120 минут
                    'start_time' => sprintf('%02d:%02d', rand(8, 16), rand(0, 1) * 30), // с 8:00 до 16:30
                    'room' => 'Ауд. ' . rand(101, 510),
                    'notes' => 'Автоматически сгенерированные тестовые данные'
                ];
            }

            // Привязываем уроки к расписанию
            $schedule->lessons()->attach($lessonData);
        }

        $this->command->info('Связи между расписанием и уроками созданы');
    }
}