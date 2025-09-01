<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Устанавливаем локаль для приложения
        app()->setLocale('en');
        
        // Устанавливаем локаль для Carbon
        Carbon::setLocale('en');
        
        // Альтернативно, для русского языка:
        // app()->setLocale('ru');
        // Carbon::setLocale('ru');
    }
}
