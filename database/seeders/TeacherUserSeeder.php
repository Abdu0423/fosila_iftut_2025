<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class TeacherUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем роль учителя
        $teacherRole = Role::where('name', 'teacher')->first();

        if (!$teacherRole) {
            $this->command->error('Роль "teacher" не найдена. Сначала запустите RoleSeeder.');
            return;
        }

        // Проверяем, существует ли уже пользователь с email teacher@fosila.com
        $existingTeacher = User::where('email', 'teacher@fosila.com')->first();

        if ($existingTeacher) {
            $this->command->info('Пользователь teacher@fosila.com уже существует.');
            return;
        }

        // Создаем пользователя-учителя
        $teacher = User::create([
            'name' => 'Учитель',
            'last_name' => 'Тестовый',
            'email' => 'teacher@fosila.com',
            'password' => Hash::make('password'),
        ]);

        // Привязываем роль учителя
        $teacher->role()->associate($teacherRole);
        $teacher->save();

        $this->command->info('Пользователь-учитель создан: teacher@fosila.com / password');
    }
}
