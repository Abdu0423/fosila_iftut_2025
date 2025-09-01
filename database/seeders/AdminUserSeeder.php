<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем роль администратора
        $adminRole = Role::where('name', 'admin')->first();
        
        if (!$adminRole) {
            $this->command->error('Роль admin не найдена!');
            return;
        }

        // Создаем администратора, если его нет
        $admin = User::where('email', 'admin@fosila.com')->first();
        
        if (!$admin) {
            User::create([
                'name' => 'Администратор',
                'email' => 'admin@fosila.com',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]);
            
            $this->command->info('Администратор создан: admin@fosila.com / password');
        } else {
            $this->command->info('Администратор уже существует');
        }
    }
}
