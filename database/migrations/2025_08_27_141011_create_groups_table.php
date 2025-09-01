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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Имя группы
            $table->integer('enrollment_year'); // Год поступления
            $table->integer('graduation_year'); // Год выпуска
            $table->enum('status', ['active', 'graduated', 'suspended'])->default('active'); // Статус группы
            $table->foreignId('department_id')->constrained()->onDelete('cascade'); // ID кафедры
            $table->integer('course'); // Курс (1, 2, 3, 4, 5, 6)
            $table->foreignId('specialty_id')->constrained()->onDelete('cascade'); // ID специальности
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
