<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Syllabus extends Model
{
    use HasFactory;

    /**
     * Таблица, связанная с моделью.
     */
    protected $table = 'syllabuses';

    protected $fillable = [
        'name',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'subject_id',
        'creation_year',
        'created_by'
    ];

    protected $casts = [
        'creation_year' => 'integer',
        'file_size' => 'integer',
    ];

    /**
     * Отношение к предмету
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Отношение к создателю
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Отношение к расписаниям (many-to-many)
     */
    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'schedule_syllabus')
                    ->withTimestamps();
    }

    /**
     * Получить размер файла в читаемом формате
     */
    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) {
            return 'Неизвестно';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * Получить URL для скачивания файла
     */
    public function getDownloadUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }

        // Определяем контекст по текущему маршруту
        $currentRoute = request()->route();
        if ($currentRoute && str_contains($currentRoute->getName(), 'teacher')) {
            return route('teacher.syllabuses.download', $this->id);
        }

        return route('admin.syllabuses.download', $this->id);
    }

    /**
     * Получить URL для предварительного просмотра
     */
    public function getPreviewUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }

        // Для PDF файлов можно показать предварительный просмотр
        if ($this->file_type === 'application/pdf') {
            // Определяем контекст по текущему маршруту
            $currentRoute = request()->route();
            if ($currentRoute && str_contains($currentRoute->getName(), 'teacher')) {
                return route('teacher.syllabuses.preview', $this->id);
            }
            
            return route('admin.syllabuses.preview', $this->id);
        }

        return null;
    }
}
