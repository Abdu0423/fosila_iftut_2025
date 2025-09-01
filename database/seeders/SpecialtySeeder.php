<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specialty;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            [
                'name' => 'Информационные системы и технологии',
                'code' => '09.03.02',
                'description' => 'Бакалавриат по направлению информационные системы и технологии',
                'duration_years' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Прикладная информатика',
                'code' => '09.03.03',
                'description' => 'Бакалавриат по направлению прикладная информатика',
                'duration_years' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Менеджмент',
                'code' => '38.03.02',
                'description' => 'Бакалавриат по направлению менеджмент',
                'duration_years' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Экономика',
                'code' => '38.03.01',
                'description' => 'Бакалавриат по направлению экономика',
                'duration_years' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Лингвистика',
                'code' => '45.03.02',
                'description' => 'Бакалавриат по направлению лингвистика',
                'duration_years' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($specialties as $specialty) {
            Specialty::create($specialty);
        }
    }
}
