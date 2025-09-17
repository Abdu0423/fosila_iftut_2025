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
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('code')->nullable()->after('name'); // Код предмета (например, MATH001)
            $table->text('description')->nullable()->after('content'); // Более подробное описание
            $table->integer('credits')->default(3); // Количество кредитов
            $table->boolean('is_active')->default(true); // Статус активности
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['code', 'description', 'credits', 'is_active']);
        });
    }
};
