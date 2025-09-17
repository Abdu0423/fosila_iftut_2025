<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'Математика',
                'code' => 'MATH001',
                'content' => 'Изучение чисел, величин, пространственных отношений и логических структур',
                'description' => 'Фундаментальная наука, изучающая количественные отношения и пространственные формы',
                'credits' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Физика',
                'code' => 'PHYS001',
                'content' => 'Изучение материи, энергии и их взаимодействий',
                'description' => 'Наука о природе, изучающая общие закономерности явлений',
                'credits' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Химия',
                'code' => 'CHEM001',
                'content' => 'Изучение веществ, их свойств и превращений',
                'description' => 'Наука о веществах, их составе, строении, свойствах и превращениях',
                'credits' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Биология',
                'code' => 'BIOL001',
                'content' => 'Изучение живых организмов и их взаимодействий',
                'description' => 'Наука о жизни и живых организмах',
                'credits' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'История',
                'code' => 'HIST001',
                'content' => 'Изучение прошлого человечества',
                'description' => 'Наука, изучающая прошлое человеческого общества',
                'credits' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Литература',
                'code' => 'LIT001',
                'content' => 'Изучение художественных произведений',
                'description' => 'Изучение художественной словесности и литературного творчества',
                'credits' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Русский язык',
                'code' => 'RUS001',
                'content' => 'Изучение русского языка и культуры речи',
                'description' => 'Изучение грамматики, орфографии и стилистики русского языка',
                'credits' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Английский язык',
                'code' => 'ENG001',
                'content' => 'Изучение английского языка',
                'description' => 'Изучение английского языка как иностранного',
                'credits' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Информатика',
                'code' => 'CS001',
                'content' => 'Изучение информационных технологий и программирования',
                'description' => 'Наука об автоматической обработке информации',
                'credits' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'География',
                'code' => 'GEO001',
                'content' => 'Изучение Земли и её природных явлений',
                'description' => 'Наука о земной поверхности, населении и хозяйстве',
                'credits' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Обществознание',
                'code' => 'SOC001',
                'content' => 'Изучение общества и социальных отношений',
                'description' => 'Комплекс дисциплин об обществе и человеке',
                'credits' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Философия',
                'code' => 'PHIL001',
                'content' => 'Изучение фундаментальных вопросов бытия',
                'description' => 'Наука о всеобщих законах развития природы, общества и мышления',
                'credits' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Экономика',
                'code' => 'ECON001',
                'content' => 'Изучение экономических процессов и отношений',
                'description' => 'Наука о хозяйственной деятельности и экономических отношениях',
                'credits' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Право',
                'code' => 'LAW001',
                'content' => 'Изучение правовых норм и отношений',
                'description' => 'Изучение системы общеобязательных правил поведения',
                'credits' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Психология',
                'code' => 'PSY001',
                'content' => 'Изучение психики и поведения человека',
                'description' => 'Наука о закономерностях психических процессов',
                'credits' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Физическая культура',
                'code' => 'PE001',
                'content' => 'Физическое воспитание и спорт',
                'description' => 'Область деятельности по укреплению здоровья',
                'credits' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'ОБЖ',
                'code' => 'SAFE001',
                'content' => 'Основы безопасности жизнедеятельности',
                'description' => 'Изучение основ защиты человека от опасностей',
                'credits' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Технология',
                'code' => 'TECH001',
                'content' => 'Изучение технологических процессов',
                'description' => 'Изучение способов и приемов изготовления продукции',
                'credits' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Искусство',
                'code' => 'ART001',
                'content' => 'Изучение изобразительного искусства и музыки',
                'description' => 'Изучение художественного творчества и эстетики',
                'credits' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Астрономия',
                'code' => 'ASTR001',
                'content' => 'Изучение небесных объектов и космоса',
                'description' => 'Наука о Вселенной, изучающая небесные тела',
                'credits' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        $this->command->info('Предметы успешно созданы!');
    }
}
