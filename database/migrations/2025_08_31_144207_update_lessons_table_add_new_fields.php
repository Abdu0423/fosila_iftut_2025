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
        Schema::table('lessons', function (Blueprint $table) {
            // Удаляем старые поля
            $table->dropForeign(['department_id']);
            $table->dropColumn(['content', 'department_id']);
            
            // Добавляем новые поля
            $table->text('description')->nullable()->after('title');
            $table->foreignId('subject_id')->nullable()->after('description')->constrained()->onDelete('set null');
            $table->foreignId('group_id')->nullable()->after('subject_id')->constrained()->onDelete('set null');
            $table->foreignId('teacher_id')->nullable()->after('group_id')->constrained('users')->onDelete('set null');
            $table->integer('duration')->nullable()->after('teacher_id');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium')->after('duration');
            $table->boolean('is_active')->default(true)->after('difficulty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Удаляем новые поля
            $table->dropForeign(['subject_id', 'group_id', 'teacher_id']);
            $table->dropColumn(['description', 'subject_id', 'group_id', 'teacher_id', 'duration', 'difficulty', 'is_active']);
            
            // Восстанавливаем старые поля
            $table->text('content')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
        });
    }
};
