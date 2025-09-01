<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'lesson_id',
        'created_by',
        'type',
        'time_limit',
        'passing_score',
        'max_attempts',
        'is_active',
        'is_public',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(TestQuestion::class)->orderBy('order');
    }

    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'Неактивен';
        }

        if ($this->start_date && now()->lt($this->start_date)) {
            return 'Ожидает';
        }

        if ($this->end_date && now()->gt($this->end_date)) {
            return 'Завершен';
        }

        return 'Активен';
    }
}
