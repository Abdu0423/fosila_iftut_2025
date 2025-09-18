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
            // Удаляем ненужные поля
            $table->dropColumn([
                'department_id',
                'difficulty', 
                'is_active',
                'duration'
            ]);
            
            // Добавляем поле для файла
            $table->string('file_path')->nullable()->after('description');
            $table->string('file_name')->nullable()->after('file_path');
            $table->string('file_type')->nullable()->after('file_name');
            $table->integer('file_size')->nullable()->after('file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Возвращаем удаленные поля
            $table->foreignId('department_id')->nullable();
            $table->string('difficulty')->default('medium');
            $table->boolean('is_active')->default(true);
            $table->integer('duration')->default(90);
            
            // Удаляем добавленные поля для файла
            $table->dropColumn([
                'file_path',
                'file_name', 
                'file_type',
                'file_size'
            ]);
        });
    }
};