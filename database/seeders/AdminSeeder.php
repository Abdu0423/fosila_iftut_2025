<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        // Проверяем, существует ли уже админ
        $adminExists = User::where('email', 'admin@fosila.com')->exists();
        
        if (!$adminExists) {
            User::create([
                'name' => 'Администратор',
                'last_name' => 'Системы',
                'email' => 'admin@fosila.com',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]);
            
            $this->command->info('Администратор создан успешно!');
        } else {
            $this->command->info('Администратор уже существует.');
        }
    }
}
