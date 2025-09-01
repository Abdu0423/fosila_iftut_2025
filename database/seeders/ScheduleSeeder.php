<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\Lesson;
use App\Models\Group;
use App\Models\User;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем уроки
        $lessons = Lesson::all();
        
        // Получаем группы
        $groups = Group::all();
        
        // Получаем учителей (админ как учитель)
        $teachers = User::whereHas('role', function($q) {
            $q->where('name', 'teacher');
        })->get();
        
        if ($teachers->isEmpty()) {
            // Если нет учителей, используем админа
            $teachers = User::whereHas('role', function($q) {
                $q->where('name', 'admin');
            })->get();
        }

        $currentYear = date('Y');
        
        foreach ($lessons as $lesson) {
            // Для каждого урока создаем несколько записей в расписании
            foreach ($groups as $group) {
                // Проверяем, что группа и урок принадлежат одной кафедре
                if ($group->department_id === $lesson->department_id) {
                    Schedule::create([
                        'lesson_id' => $lesson->id,
                        'teacher_id' => $teachers->first()->id,
                        'group_id' => $group->id,
                        'semester' => rand(1, 2),
                        'credits' => rand(2, 6),
                        'study_year' => $currentYear,
                        'order' => rand(1, 10),
                        'scheduled_at' => now()->addDays(rand(1, 30)),
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
