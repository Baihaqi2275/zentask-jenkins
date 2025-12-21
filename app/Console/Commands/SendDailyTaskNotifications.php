<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Notification;
use App\Mail\TaskDueTodayMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendDailyTaskNotifications extends Command
{
    protected $signature = 'notifications:send-daily';
    protected $description = 'Send daily task notifications to users with tasks due today';

    public function handle()
    {
        $this->info('🔔 Starting daily notification process...');

        $today = Carbon::today()->toDateString();
        $sentCount = 0;
        $failedCount = 0;

        // Get users with tasks due today (Eager Loading)
        $users = User::whereHas('tasks', function ($query) use ($today) {
            $query->where('due_date', $today)
                  ->where('status', '!=', 'completed');
        })->with(['tasks' => function ($query) use ($today) {
            $query->where('due_date', $today)
                  ->where('status', '!=', 'completed');
        }])->get();

        if ($users->isEmpty()) {
            $this->info('ℹ️  No users with tasks due today.');
            Log::info('Daily notifications: No tasks due today');
            return 0;
        }

        $this->info("📊 Found {$users->count()} users with tasks due today");

        foreach ($users as $user) {
            try {
                $tasks = $user->tasks;

                // Send email
                Mail::to($user->email)->send(new TaskDueTodayMail($user, $tasks));

                // Store notification in database
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'task_due_today',
                    'message' => "Kamu punya {$tasks->count()} task yang deadline-nya hari ini",
                    'read' => false
                ]);

                $this->info("✅ Sent to {$user->name} ({$user->email}) - {$tasks->count()} tasks");
                $sentCount++;

                // Log success
                Log::info("Notification sent", [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'task_count' => $tasks->count()
                ]);

            } catch (\Exception $e) {
                $this->error("❌ Failed for {$user->email}: {$e->getMessage()}");
                $failedCount++;

                // Log error
                Log::error("Notification failed", [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("✨ Done! Sent: {$sentCount}, Failed: {$failedCount}");

        return 0;
    }
}
