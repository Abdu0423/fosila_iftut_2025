<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Group;
use App\Models\User;

class TeacherScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем тестовые предметы
        $subjects = [
            ['name' => 'Математика', 'code' => 'MATH101'],
            ['name' => 'Физика', 'code' => 'PHYS101'],
            ['name' => 'Химия', 'code' => 'CHEM101'],
            ['name' => 'Информатика', 'code' => 'INFO101'],
        ];

        foreach ($subjects as $subjectData) {
            Subject::updateOrCreate(
                ['code' => $subjectData['code']],
                $subjectData
            );
        }

        // Создаем тестовые группы
        $groups = [
            ['name' => 'ИС-1-21', 'specialty_id' => 1],
            ['name' => 'ИС-2-21', 'specialty_id' => 1],
            ['name' => 'ПО-1-21', 'specialty_id' => 1],
        ];

        foreach ($groups as $groupData) {
            Group::updateOrCreate(
                ['name' => $groupData['name']],
                $groupData
            );
        }

        // Создаем тестовые уроки
        $lessons = [
            ['title' => 'Введение в математический анализ', 'description' => 'Основы дифференциального исчисления'],
            ['title' => 'Механика', 'description' => 'Основы классической механики'],
            ['title' => 'Органическая химия', 'description' => 'Основы органической химии'],
            ['title' => 'Программирование на Python', 'description' => 'Основы программирования'],
        ];

        foreach ($lessons as $lessonData) {
            Lesson::updateOrCreate(
                ['title' => $lessonData['title']],
                $lessonData
            );
        }

        // Получаем учителя (предполагаем, что есть пользователь с role = 'teacher')
        $teacher = User::where('role', 'teacher')->first();
        
        if (!$teacher) {
            // Создаем тестового учителя
            $teacher = User::create([
                'name' => 'Иванов Иван Иванович',
                'email' => 'teacher@example.com',
                'password' => bcrypt('password'),
                'role' => 'teacher',
            ]);
        }

        // Создаем расписание для учителя
        $schedules = [
            [
                'lesson_id' => 1,
                'subject_id' => 1,
                'group_id' => 1,
                'teacher_id' => $teacher->id,
                'day_of_week' => 'monday',
                'start_time' => '09:00',
                'end_time' => '10:30',
                'room' => 'А-101',
                'semester' => '1',
                'is_active' => true,
            ],
            [
                'lesson_id' => 2,
                'subject_id' => 2,
                'group_id' => 1,
                'teacher_id' => $teacher->id,
                'day_of_week' => 'tuesday',
                'start_time' => '10:40',
                'end_time' => '12:10',
                'room' => 'Б-202',
                'semester' => '1',
                'is_active' => true,
            ],
            [
                'lesson_id' => 3,
                'subject_id' => 3,
                'group_id' => 2,
                'teacher_id' => $teacher->id,
                'day_of_week' => 'wednesday',
                'start_time' => '13:00',
                'end_time' => '14:30',
                'room' => 'В-303',
                'semester' => '1',
                'is_active' => true,
            ],
            [
                'lesson_id' => 4,
                'subject_id' => 4,
                'group_id' => 3,
                'teacher_id' => $teacher->id,
                'day_of_week' => 'thursday',
                'start_time' => '14:40',
                'end_time' => '16:10',
                'room' => 'Г-404',
                'semester' => '1',
                'is_active' => true,
            ],
        ];

        foreach ($schedules as $scheduleData) {
            Schedule::updateOrCreate(
                [
                    'lesson_id' => $scheduleData['lesson_id'],
                    'group_id' => $scheduleData['group_id'],
                    'day_of_week' => $scheduleData['day_of_week'],
                    'start_time' => $scheduleData['start_time'],
                ],
                $scheduleData
            );
        }

        // Создаем тестовых студентов в группах
        $students = [
            ['name' => 'Студент 1', 'email' => 'student1@example.com', 'group_id' => 1],
            ['name' => 'Студент 2', 'email' => 'student2@example.com', 'group_id' => 1],
            ['name' => 'Студент 3', 'email' => 'student3@example.com', 'group_id' => 2],
            ['name' => 'Студент 4', 'email' => 'student4@example.com', 'group_id' => 2],
            ['name' => 'Студент 5', 'email' => 'student5@example.com', 'group_id' => 3],
        ];

        foreach ($students as $studentData) {
            User::updateOrCreate(
                ['email' => $studentData['email']],
                array_merge($studentData, [
                    'password' => bcrypt('password'),
                    'role' => 'student',
                ])
            );
        }
    }
}
