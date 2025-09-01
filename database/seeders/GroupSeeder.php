<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Department;
use App\Models\Specialty;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = date('Y');
        
        // Получаем кафедры и специальности
        $itDepartment = Department::where('code', 'IT')->first();
        $emDepartment = Department::where('code', 'EM')->first();
        $flDepartment = Department::where('code', 'FL')->first();
        
        $itSpecialty = Specialty::where('code', '09.03.02')->first();
        $piSpecialty = Specialty::where('code', '09.03.03')->first();
        $managementSpecialty = Specialty::where('code', '38.03.02')->first();
        $linguisticsSpecialty = Specialty::where('code', '45.03.02')->first();

        $groups = [
            // Группы ИТ кафедры
            [
                'name' => 'ИС-21-1',
                'enrollment_year' => 2021,
                'graduation_year' => 2025,
                'status' => 'active',
                'department_id' => $itDepartment->id,
                'course' => 3,
                'specialty_id' => $itSpecialty->id,
            ],
            [
                'name' => 'ИС-22-1',
                'enrollment_year' => 2022,
                'graduation_year' => 2026,
                'status' => 'active',
                'department_id' => $itDepartment->id,
                'course' => 2,
                'specialty_id' => $itSpecialty->id,
            ],
            [
                'name' => 'ПИ-21-1',
                'enrollment_year' => 2021,
                'graduation_year' => 2025,
                'status' => 'active',
                'department_id' => $itDepartment->id,
                'course' => 3,
                'specialty_id' => $piSpecialty->id,
            ],
            
            // Группы экономической кафедры
            [
                'name' => 'МН-21-1',
                'enrollment_year' => 2021,
                'graduation_year' => 2025,
                'status' => 'active',
                'department_id' => $emDepartment->id,
                'course' => 3,
                'specialty_id' => $managementSpecialty->id,
            ],
            [
                'name' => 'МН-22-1',
                'enrollment_year' => 2022,
                'graduation_year' => 2026,
                'status' => 'active',
                'department_id' => $emDepartment->id,
                'course' => 2,
                'specialty_id' => $managementSpecialty->id,
            ],
            
            // Группы кафедры иностранных языков
            [
                'name' => 'ЛН-21-1',
                'enrollment_year' => 2021,
                'graduation_year' => 2025,
                'status' => 'active',
                'department_id' => $flDepartment->id,
                'course' => 3,
                'specialty_id' => $linguisticsSpecialty->id,
            ],
        ];

        foreach ($groups as $group) {
            Group::create($group);
        }
    }
}
