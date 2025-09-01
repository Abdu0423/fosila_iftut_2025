<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'teacher_id',
        'group_id',
        'semester',
        'credits',
        'study_year',
        'order',
        'scheduled_at',
        'is_active'
    ];

    protected $casts = [
        'semester' => 'integer',
        'credits' => 'integer',
        'study_year' => 'integer',
        'order' => 'integer',
        'scheduled_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function subject()
    {
        return $this->hasOneThrough(Subject::class, Lesson::class, 'id', 'id', 'lesson_id', 'subject_id');
    }
}
