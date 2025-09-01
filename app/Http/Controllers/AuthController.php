<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        Log::info('Показана страница входа');
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        Log::info('Получен запрос на вход', [
            'email' => $request->email,
            'has_password' => !empty($request->password)
        ]);

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Попытка входа', ['email' => $credentials['email']]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            Log::info('Пользователь успешно авторизован', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role ? $user->role->name : 'no role',
                'is_admin' => $user->isAdmin()
            ]);

            if ($user->isAdmin()) {
                Log::info('Перенаправление администратора на /admin');
                return redirect('/admin');
            }

            if ($user->isTeacher()) {
                Log::info('Перенаправление учителя на /teacher');
                return redirect('/teacher');
            }

            Log::info('Перенаправление студента на /dashboard');
            return redirect('/dashboard');
        }

        Log::warning('Неудачная попытка входа', ['email' => $credentials['email']]);

        return back()->withErrors([
            'email' => 'Предоставленные учетные данные не соответствуют нашим записям.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
