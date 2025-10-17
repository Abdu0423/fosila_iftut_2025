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
        'schedule_id',
        'created_by',
        'time_limit',
        'passing_score',
        'max_attempts',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
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
        return $this->is_active ? 'Активен' : 'Неактивен';
    }
}
