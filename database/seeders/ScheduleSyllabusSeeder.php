<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\Syllabus;

class ScheduleSyllabusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем все расписания и силлабусы
        $schedules = Schedule::all();
        $syllabuses = Syllabus::all();

        if ($schedules->isEmpty() || $syllabuses->isEmpty()) {
            $this->command->info('Нет данных для создания связей между расписанием и силлабусами');
            return;
        }

        foreach ($schedules as $schedule) {
            // Случайным образом выбираем 1-3 силлабуса для каждого расписания
            $syllabusCount = rand(1, min(3, $syllabuses->count()));
            $randomSyllabuses = $syllabuses->random($syllabusCount);

            // Привязываем силлабусы к расписанию
            $schedule->syllabuses()->attach($randomSyllabuses->pluck('id')->toArray());
        }

        $this->command->info('Связи между расписанием и силлабусами созданы');
    }
}