<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('group_student', function (Blueprint $table) {
            // Проверяем, существуют ли уже колонки
            if (!Schema::hasColumn('group_student', 'user_id')) {
                $table->unsignedBigInteger('user_id');
            }
            if (!Schema::hasColumn('group_student', 'group_id')) {
                $table->unsignedBigInteger('group_id');
            }
            
            // Добавляем индексы
            $table->index(['user_id', 'group_id']);
            $table->index('user_id');
            $table->index('group_id');
            
            // Добавляем внешние ключи
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_student', function (Blueprint $table) {
            // Удаляем внешние ключи
            $table->dropForeign(['user_id']);
            $table->dropForeign(['group_id']);
            
            // Удаляем колонки
            $table->dropColumn(['user_id', 'group_id']);
        });
    }
};