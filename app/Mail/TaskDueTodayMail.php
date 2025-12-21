<?php
// app/Mail/TaskDueTodayMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskDueTodayMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $tasks;
    public $taskCount;

    public function __construct($user, $tasks)
    {
        $this->user = $user;
        $this->tasks = $tasks;
        $this->taskCount = $tasks->count();
    }

    public function build()
    {
        return $this->subject('⏰ Task Deadline Hari Ini - Zentask')
                    ->view('emails.task-due-today');
    }
}
