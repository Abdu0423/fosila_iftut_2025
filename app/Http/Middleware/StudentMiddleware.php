<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Загружаем роль пользователя
        $user->load('role');
        
        // Проверяем, что пользователь является студентом
        if (!$user->isStudent()) {
            // Если не студент, перенаправляем в зависимости от роли
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isTeacher()) {
                return redirect()->route('teacher.dashboard');
            } else {
                abort(403, 'Доступ запрещен. Роль: ' . ($user->role ? $user->role->name : 'не определена'));
            }
        }

        return $next($request);
    }
}
