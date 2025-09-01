<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\User;
use App\Models\Schedule;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'lessons' => Lesson::count(),
            'teachers' => User::whereHas('role', function($q) {
                $q->where('role_id', '2');
            })->count(),
            'students' => User::whereHas('role', function($q) {
                $q->where('role_id', '3');
            })->count(),
            'schedules' => Schedule::count(),
            'activeLessons' => Lesson::count(),
            'busyTeachers' => User::whereHas('role', function($q) {
                $q->where('role_id', '2');
            })->whereHas('schedules')->count(),
            'enrolledStudents' => User::whereHas('role', function($q) {
                $q->where('role_id', '3');
            })->count(),
        ];

        $recentLessons = Lesson::with('department')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentLessons' => $recentLessons,
        ]);
    }
}
