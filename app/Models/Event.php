<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_at',
        'end_at',
        'all_day',
        'color',
        'user_id',
        'category_id',
        'rrule',
        'is_recurring',
        'audio_path',
        'transcript',
    ];

    protected $casts = [
        'all_day' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_recurring' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getDisplayColorAttribute()
    {
        return $this->color ?? ($this->category?->color ?? null);
    }

    public function scopeBetween($query, $start, $end)
    {
        return $query->where('start_at', '>=', $start)->where('start_at', '<=', $end);
    }
}
