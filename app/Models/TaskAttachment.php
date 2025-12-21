<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_file_id',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function userFile()
    {
        return $this->belongsTo(UserFile::class);
    }
}
