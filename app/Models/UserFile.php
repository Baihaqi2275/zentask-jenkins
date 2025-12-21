<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_name',
        'file_name',
        'path',
        'mime_type',
        'extension',
        'size',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }
}
