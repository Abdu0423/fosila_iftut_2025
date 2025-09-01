<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            SubjectSeeder::class,
            DepartmentSeeder::class,
            SpecialtySeeder::class,
            GroupSeeder::class,
            LessonSeeder::class,
            ScheduleSeeder::class,
        ]);
    }
}
