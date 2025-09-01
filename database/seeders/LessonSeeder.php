<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Subject;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем предметы
        $mathSubject = Subject::where('name', 'Математика')->first();
        $physicsSubject = Subject::where('name', 'Физика')->first();
        $chemistrySubject = Subject::where('name', 'Химия')->first();
        $biologySubject = Subject::where('name', 'Биология')->first();
        $historySubject = Subject::where('name', 'История')->first();
        $literatureSubject = Subject::where('name', 'Литература')->first();
        $russianSubject = Subject::where('name', 'Русский язык')->first();
        $englishSubject = Subject::where('name', 'Английский язык')->first();
        $informaticsSubject = Subject::where('name', 'Информатика')->first();
        $geographySubject = Subject::where('name', 'География')->first();
        $socialSubject = Subject::where('name', 'Обществознание')->first();
        $philosophySubject = Subject::where('name', 'Философия')->first();
        $economicsSubject = Subject::where('name', 'Экономика')->first();
        $lawSubject = Subject::where('name', 'Право')->first();
        $psychologySubject = Subject::where('name', 'Психология')->first();
        $peSubject = Subject::where('name', 'Физическая культура')->first();
        $safetySubject = Subject::where('name', 'ОБЖ')->first();
        $technologySubject = Subject::where('name', 'Технология')->first();
        $artSubject = Subject::where('name', 'Искусство')->first();
        $astronomySubject = Subject::where('name', 'Астрономия')->first();

        $lessons = [
            // Уроки по математике
            [
                'title' => 'Алгебра и начала анализа',
                'description' => 'Изучение алгебраических выражений, функций и их свойств',
                'subject_id' => $mathSubject->id,
                'duration' => 90,
                'difficulty' => 'medium',
                'is_active' => true,
            ],
            [
                'title' => 'Геометрия',
                'description' => 'Изучение геометрических фигур и их свойств',
                'subject_id' => $mathSubject->id,
                'duration' => 90,
                'difficulty' => 'medium',
                'is_active' => true,
            ],
            
            // Уроки по физике
            [
                'title' => 'Механика',
                'description' => 'Изучение движения тел и сил',
                'subject_id' => $physicsSubject->id,
                'duration' => 90,
                'difficulty' => 'hard',
                'is_active' => true,
            ],
            [
                'title' => 'Электричество и магнетизм',
                'description' => 'Изучение электрических и магнитных явлений',
                'subject_id' => $physicsSubject->id,
                'duration' => 90,
                'difficulty' => 'hard',
                'is_active' => true,
            ],
            
            // Уроки по информатике
            [
                'title' => 'Основы программирования',
                'description' => 'Изучение основ программирования на Python',
                'subject_id' => $informaticsSubject->id,
                'duration' => 90,
                'difficulty' => 'medium',
                'is_active' => true,
            ],
            [
                'title' => 'Веб-разработка',
                'description' => 'Создание веб-сайтов с использованием HTML, CSS и JavaScript',
                'subject_id' => $informaticsSubject->id,
                'duration' => 90,
                'difficulty' => 'medium',
                'is_active' => true,
            ],
            
            // Уроки по английскому языку
            [
                'title' => 'Грамматика английского языка',
                'description' => 'Изучение грамматических правил английского языка',
                'subject_id' => $englishSubject->id,
                'duration' => 90,
                'difficulty' => 'medium',
                'is_active' => true,
            ],
            [
                'title' => 'Разговорный английский',
                'description' => 'Развитие навыков устной речи на английском языке',
                'subject_id' => $englishSubject->id,
                'duration' => 90,
                'difficulty' => 'easy',
                'is_active' => true,
            ],
            
            // Уроки по русскому языку
            [
                'title' => 'Грамматика русского языка',
                'description' => 'Изучение грамматических правил русского языка',
                'subject_id' => $russianSubject->id,
                'duration' => 90,
                'difficulty' => 'medium',
                'is_active' => true,
            ],
            [
                'title' => 'Культура речи',
                'description' => 'Развитие навыков правильной речи и письма',
                'subject_id' => $russianSubject->id,
                'duration' => 90,
                'difficulty' => 'easy',
                'is_active' => true,
            ],
            
            // Уроки по истории
            [
                'title' => 'История России',
                'description' => 'Изучение истории России с древнейших времен',
                'subject_id' => $historySubject->id,
                'duration' => 90,
                'difficulty' => 'medium',
                'is_active' => true,
            ],
            [
                'title' => 'Всеобщая история',
                'description' => 'Изучение истории зарубежных стран',
                'subject_id' => $historySubject->id,
                'duration' => 90,
                'difficulty' => 'medium',
                'is_active' => true,
            ],
        ];

        foreach ($lessons as $lesson) {
            Lesson::create($lesson);
        }

        $this->command->info('Уроки успешно созданы!');
    }
}

