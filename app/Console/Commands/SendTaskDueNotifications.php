<?php
// app/Console/Commands/SendTaskDueNotifications.php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use App\Mail\TaskDueTodayMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendTaskDueNotifications extends Command
{
    protected $signature = 'notifications:send-task-due';
    protected $description = 'Send notifications for tasks due today';

    public function handle()
    {
        $today = Carbon::today()->toDateString();

        // Get all users with tasks due today
        $users = User::whereHas('tasks', function ($query) use ($today) {
            $query->where('due_date', $today)
                  ->where('status', '!=', 'completed');
        })->with(['tasks' => function ($query) use ($today) {
            $query->where('due_date', $today)
                  ->where('status', '!=', 'completed');
        }])->get();

        foreach ($users as $user) {
            $tasks = $user->tasks;

            if ($tasks->isEmpty()) {
                continue;
            }

            // Send Email
            try {
                Mail::to($user->email)->send(new TaskDueTodayMail($user, $tasks));
            } catch (\Exception $e) {
                $this->error("Failed to send email to {$user->email}: {$e->getMessage()}");
            }

            // Store notification in database
            DB::table('notifications')->insert([
                'user_id' => $user->id,
                'type' => 'task_due_today',
                'message' => "Kamu punya " . $tasks->count() . " task yang deadline-nya hari ini",
                'data' => json_encode([
                    'task_count' => $tasks->count(),
                    'date' => $today
                ]),
                'read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info("Notification sent to {$user->name} ({$user->email})");
        }

        $this->info('Task due notifications sent successfully!');
        return 0;
    }
}
