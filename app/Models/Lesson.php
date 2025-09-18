<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'subject_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size'
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }


    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'lesson_schedule')
                    ->withPivot(['order', 'duration', 'start_time', 'end_time', 'room', 'notes'])
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

        return route('admin.lessons.download', $this->id);
    }
}
