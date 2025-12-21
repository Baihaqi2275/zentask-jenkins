<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        $user = Auth::user();

        $count = Task::where('user_id', $user->id)
            ->where('status', '!=', 'selesai')
            ->whereNotNull('due_date')
            ->where('due_date', '<=', Carbon::now()->addDays(3)) // Due within 3 days
            ->count();

        return response()->json([
            'count' => $count,
            'has_notifications' => $count > 0,
        ]);
    }

    /**
     * Get today's tasks and overdue tasks
     */
    public function getTodayTasks()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();

        $baseQuery = Task::where('user_id', $user->id)
            ->where('status', '!=', 'selesai');

        // Get overdue tasks
        $overdueTasks = (clone $baseQuery)
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($task) use ($now) {
                $dueDate = Carbon::parse($task->due_date);
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'category' => $task->category,
                    'deadline' => $task->due_date,
                    'deadline_formatted' => $dueDate->format('d M Y'),
                    'days_overdue' => $now->diffInDays($dueDate),
                    'status_label' => 'Overdue',
                    'status_class' => 'overdue',
                    'priority' => 'high',
                ];
            });

        // Get today's tasks
        $todayTasks = (clone $baseQuery)
            ->whereNotNull('due_date')
            ->whereDate('due_date', $today)
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'category' => $task->category,
                    'deadline' => $task->due_date,
                    'deadline_formatted' => Carbon::parse($task->due_date)->format('d M Y'),
                    'status_label' => 'Due Today',
                    'status_class' => 'today',
                    'priority' => 'medium',
                ];
            });

        // Get upcoming tasks (next 1-3 days)
        $upcomingTasks = (clone $baseQuery)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>', $today)
            ->whereDate('due_date', '<=', $today->copy()->addDays(3))
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($task) use ($now) {
                $deadline = Carbon::parse($task->due_date);
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'category' => $task->category,
                    'deadline' => $task->due_date,
                    'deadline_formatted' => $deadline->format('d M Y'),
                    'days_until' => $now->diffInDays($deadline),
                    'status_label' => 'Due Soon',
                    'status_class' => 'upcoming',
                    'priority' => 'low',
                ];
            });

        // Get pending tasks with later due dates
        $laterTasks = (clone $baseQuery)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>', $today->copy()->addDays(3))
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($task) use ($now) {
                $deadline = Carbon::parse($task->due_date);
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'category' => $task->category,
                    'deadline' => $task->due_date,
                    'deadline_formatted' => $deadline->format('d M Y'),
                    'days_until' => $now->diffInDays($deadline),
                    'status_label' => 'Pending',
                    'status_class' => 'pending',
                    'priority' => 'low',
                ];
            });

        // Get tasks without deadlines
        $noDeadlineTasks = (clone $baseQuery)
            ->whereNull('due_date')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'category' => $task->category,
                    'deadline' => null,
                    'deadline_formatted' => 'No deadline',
                    'status_label' => 'No deadline',
                    'status_class' => 'no_deadline',
                    'priority' => 'low',
                ];
            });

        // Merge all tasks
        $allTasks = $overdueTasks
            ->concat($todayTasks)
            ->concat($upcomingTasks)
            ->concat($laterTasks)
            ->concat($noDeadlineTasks);

        return response()->json([
            'success' => true,
            'total_count' => $allTasks->count(),
            'overdue_count' => $overdueTasks->count(),
            'today_count' => $todayTasks->count(),
            'upcoming_count' => $upcomingTasks->count(),
            'tasks' => $allTasks,
            'has_tasks' => $allTasks->count() > 0,
        ]);
    }

    /**
     * Mark notification as read (for future use)
     */
    public function markAsRead(Request $request)
    {
        // For now, just return success
        // Later you can implement a notifications table
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Get notification summary
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $summary = [
            'overdue' => Task::where('user_id', $user->id)
                ->where('status', '!=', 'selesai')
                ->whereNotNull('due_date')
                ->where('due_date', '<', $today)
                ->count(),
            'today' => Task::where('user_id', $user->id)
                ->where('status', '!=', 'selesai')
                ->whereNotNull('due_date')
                ->whereDate('due_date', $today)
                ->count(),
            'upcoming' => Task::where('user_id', $user->id)
                ->where('status', '!=', 'selesai')
                ->whereNotNull('due_date')
                ->whereBetween('due_date', [$today->copy()->addDay(), $today->copy()->addDays(3)])
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'total' => array_sum($summary),
        ]);
    }
}
