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
        Schema::table('syllabuses', function (Blueprint $table) {
            // Удаляем старый внешний ключ
            $table->dropForeign(['lesson_id']);
            
            // Удаляем старое поле
            $table->dropColumn('lesson_id');
            
            // Добавляем новое поле
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('syllabuses', function (Blueprint $table) {
            // Удаляем новый внешний ключ
            $table->dropForeign(['subject_id']);
            
            // Удаляем новое поле
            $table->dropColumn('subject_id');
            
            // Возвращаем старое поле
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
        });
    }
};