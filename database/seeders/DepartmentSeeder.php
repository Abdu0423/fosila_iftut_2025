<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Кафедра информационных технологий',
                'code' => 'IT',
                'description' => 'Кафедра занимается подготовкой специалистов в области информационных технологий',
                'is_active' => true,
            ],
            [
                'name' => 'Кафедра экономики и менеджмента',
                'code' => 'EM',
                'description' => 'Кафедра готовит специалистов в области экономики и управления',
                'is_active' => true,
            ],
            [
                'name' => 'Кафедра иностранных языков',
                'code' => 'FL',
                'description' => 'Кафедра преподавания иностранных языков',
                'is_active' => true,
            ],
            [
                'name' => 'Кафедра математики',
                'code' => 'MATH',
                'description' => 'Кафедра высшей математики и прикладной математики',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
