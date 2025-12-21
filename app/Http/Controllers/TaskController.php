<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\UserFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display dashboard with tasks
     */
    public function index()
    {
        try {
            $user = Auth::user();

            // Safety check: pastikan relationship tasks() ada
            if (!method_exists($user, 'tasks')) {
                Log::error('User model missing tasks() relationship');
                abort(500, 'System configuration error');
            }

            // Get all tasks for the user (FIXED: pakai due_date)
            $tasks = $user->tasks()
                ->orderBy('due_date', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate statistics (dengan fallback 0)
            $totalTasks = $tasks->count();
            $inProgressTasks = $tasks->where('status', 'berjalan')->count();
            $completedTasks = $tasks->where('status', 'selesai')->count();

            // Get nearest deadline (FIXED: pakai due_date)
            $nearestDeadline = $user->tasks()
                ->where('status', 'berjalan')
                ->whereNotNull('due_date')
                ->orderBy('due_date', 'asc')
                ->first();

            return view('dashboard', compact(
                'tasks',
                'totalTasks',
                'inProgressTasks',
                'completedTasks',
                'nearestDeadline'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard loading failed: ' . $e->getMessage());

            // Return view dengan data kosong sebagai fallback
            return view('dashboard', [
                'tasks' => collect([]),
                'totalTasks' => 0,
                'inProgressTasks' => 0,
                'completedTasks' => 0,
                'nearestDeadline' => null,
            ])->with('error', 'Failed to load dashboard. Please refresh the page.');
        }
    }

    /**
     * Store a new task (FIXED: pakai due_date dan voice_text)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:5000',
            'due_date' => 'required|date|after_or_equal:today', // FIXED
            'status' => 'nullable|in:berjalan,selesai',
            'voice_text' => 'nullable|string|max:10000', // FIXED
            'attachments' => 'nullable|array',
            'attachments.*' => 'integer',
        ], [
            'title.required' => 'Task title is required.',
            'title.max' => 'Task title must not exceed 255 characters.',
            'category.required' => 'Category is required.',
            'category.max' => 'Category must not exceed 100 characters.',
            'due_date.required' => 'Deadline is required.',
            'due_date.date' => 'Deadline must be a valid date.',
            'due_date.after_or_equal' => 'Deadline cannot be in the past.',
            'status.in' => 'Invalid status value.',
        ]);

        try {
            $task = Task::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'category' => $validated['category'],
                'description' => $validated['description'] ?? null,
                'due_date' => $validated['due_date'], // FIXED
                'voice_text' => $validated['voice_text'] ?? null, // FIXED
                'status' => $validated['status'] ?? 'berjalan',
            ]);

            $attachmentIds = collect($request->input('attachments', []))
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            if ($attachmentIds->isNotEmpty()) {
                $validFileIds = UserFile::where('user_id', Auth::id())
                    ->whereIn('id', $attachmentIds)
                    ->pluck('id');

                foreach ($validFileIds as $fileId) {
                    TaskAttachment::create([
                        'task_id' => $task->id,
                        'user_file_id' => $fileId,
                    ]);
                }
            }

            return redirect()->route('tasks.create')
                ->with('success', 'Task created successfully!');

        } catch (\Exception $e) {
            Log::error('Task creation failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'data' => $validated,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to create task. Please try again.');
        }
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('tasks.create');
    }

    public function edit(Task $task)
    {
        // Make sure user owns this task
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $task->load('attachments.userFile');

        return view('tasks.edit', compact('task'));
    }

    /**
     * Update task (FIXED: pakai due_date dan voice_text)
     */
    public function update(Request $request, Task $task)
    {
        // Make sure user owns this task
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:5000',
            'due_date' => 'required|date', // FIXED
            'status' => 'required|in:berjalan,selesai',
            'voice_text' => 'nullable|string|max:10000', // FIXED
            'attachments' => 'nullable|array',
            'attachments.*' => 'integer',
        ], [
            'title.required' => 'Task title is required.',
            'category.required' => 'Category is required.',
            'due_date.required' => 'Deadline is required.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status value.',
        ]);

        try {
            $task->update($validated);

            $attachmentIds = collect($request->input('attachments', []))
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            $validFileIds = UserFile::where('user_id', Auth::id())
                ->whereIn('id', $attachmentIds)
                ->pluck('id')
                ->toArray();

            $existing = $task->attachments()->pluck('user_file_id')->toArray();
            $toDelete = array_diff($existing, $validFileIds);
            $toAdd = array_diff($validFileIds, $existing);

            if (!empty($toDelete)) {
                $task->attachments()->whereIn('user_file_id', $toDelete)->delete();
            }

            foreach ($toAdd as $fileId) {
                TaskAttachment::create([
                    'task_id' => $task->id,
                    'user_file_id' => $fileId,
                ]);
            }

            return redirect()->route('dashboard')
                ->with('success', 'Task updated successfully!');

        } catch (\Exception $e) {
            Log::error('Task update failed: ' . $e->getMessage(), [
                'task_id' => $task->id,
                'user_id' => Auth::id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to update task. Please try again.');
        }
    }

    /**
     * Delete task
     */
    public function destroy(Task $task)
    {
        // Make sure user owns this task
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $taskTitle = $task->title; // Store for logging
            $task->delete();

            Log::info('Task deleted', [
                'task_id' => $task->id,
                'title' => $taskTitle,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Task deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Task deletion failed: ' . $e->getMessage(), [
                'task_id' => $task->id,
                'user_id' => Auth::id(),
            ]);

            return back()
                ->with('error', 'Failed to delete task. Please try again.');
        }
    }

    /**
     * Get dashboard statistics (API endpoint) (FIXED: pakai due_date)
     */
    public function getDashboardStats()
    {
        try {
            $user = Auth::user();

            $stats = [
                'total_tasks' => $user->tasks()->count(),
                'in_progress' => $user->tasks()->where('status', 'berjalan')->count(),
                'completed' => $user->tasks()->where('status', 'selesai')->count(),
                'overdue' => $user->tasks()
                    ->where('status', 'berjalan')
                    ->whereNotNull('due_date') // FIXED
                    ->where('due_date', '<', Carbon::now()) // FIXED
                    ->count(),
                'today' => $user->tasks()
                    ->where('status', 'berjalan')
                    ->whereNotNull('due_date') // FIXED
                    ->whereDate('due_date', Carbon::today()) // FIXED
                    ->count(),
                'this_week' => $user->tasks()
                    ->where('created_at', '>=', Carbon::now()->startOfWeek())
                    ->count(),
                'this_month' => $user->tasks()
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
            ];

            // Calculate productivity rate
            if ($stats['total_tasks'] > 0) {
                $stats['productivity_rate'] = round(($stats['completed'] / $stats['total_tasks']) * 100);
            } else {
                $stats['productivity_rate'] = 0;
            }

            return response()->json([
                'success' => true,
                'stats' => $stats,
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard stats failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to load statistics',
                'stats' => [
                    'total_tasks' => 0,
                    'in_progress' => 0,
                    'completed' => 0,
                    'overdue' => 0,
                    'today' => 0,
                    'this_week' => 0,
                    'this_month' => 0,
                    'productivity_rate' => 0,
                ],
            ], 500);
        }
    }

    /**
     * Search tasks (API endpoint) (FIXED: pakai due_date)
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'results' => [],
                'count' => 0,
                'message' => 'Query too short',
            ]);
        }

        try {
            $user = Auth::user();

            $tasks = $user->tasks()
                ->where(function($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('category', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->orderBy('due_date', 'asc') // FIXED
                ->limit(10)
                ->get()
                ->map(function($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'category' => $task->category,
                        'description' => $task->description,
                        'status' => $task->status,
                        'deadline' => $task->due_date, // Keep as 'deadline' for frontend compatibility
                        'deadline_formatted' => $task->due_date
                            ? Carbon::parse($task->due_date)->format('d M Y')
                            : null,
                        'is_overdue' => $task->due_date
                            ? Carbon::parse($task->due_date)->isPast() && $task->status === 'berjalan'
                            : false,
                    ];
                });

            return response()->json([
                'success' => true,
                'results' => $tasks,
                'count' => $tasks->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Task search failed: ' . $e->getMessage(), [
                'query' => $query,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Search failed',
                'results' => [],
                'count' => 0,
            ], 500);
        }
    }

    /**
     * Filter tasks (FIXED: pakai due_date)
     */
    public function filter(Request $request)
    {
        try {
            $user = Auth::user();
            $query = $user->tasks();

            // Filter by status
            if ($request->has('status') && in_array($request->status, ['berjalan', 'selesai'])) {
                $query->where('status', $request->status);
            }

            // Filter by category
            if ($request->has('category') && !empty($request->category)) {
                $query->where('category', $request->category);
            }

            // Filter by date range (FIXED: pakai due_date)
            if ($request->has('date_from') && !empty($request->date_from)) {
                $query->where('due_date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && !empty($request->date_to)) {
                $query->where('due_date', '<=', $request->date_to);
            }

            // Sort (FIXED: validate column name)
            $allowedSortColumns = ['due_date', 'created_at', 'title', 'status'];
            $sortBy = $request->input('sort_by', 'due_date');
            $sortBy = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'due_date';

            $sortOrder = $request->input('sort_order', 'asc');
            $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'asc';

            $query->orderBy($sortBy, $sortOrder);

            $tasks = $query->get();

            return response()->json([
                'success' => true,
                'tasks' => $tasks,
                'count' => $tasks->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Task filter failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Filter failed',
                'tasks' => [],
                'count' => 0,
            ], 500);
        }
    }

    /**
     * Get recent tasks
     */
    public function getRecentTasks()
    {
        try {
            $user = Auth::user();

            $tasks = $user->tasks()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'tasks' => $tasks,
                'count' => $tasks->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Get recent tasks failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to load recent tasks',
                'tasks' => [],
                'count' => 0,
            ], 500);
        }
    }

    /**
     * Quick update task status (AJAX)
     */
    public function quickUpdate(Request $request, Task $task)
    {
        // Make sure user owns this task
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:berjalan,selesai',
            'title' => 'sometimes|string|max:255',
        ]);

        try {
            $task->update($validated);

            return response()->json([
                'success' => true,
                'task' => $task->fresh(),
                'message' => 'Task updated successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Quick update failed: ' . $e->getMessage(), [
                'task_id' => $task->id,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Update failed',
            ], 500);
        }
    }

    /**
     * Toggle task status (complete/incomplete)
     */
    public function toggleStatus(Task $task)
    {
        // Make sure user owns this task
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $newStatus = $task->status === 'selesai' ? 'berjalan' : 'selesai';
            $task->update(['status' => $newStatus]);

            $statusText = $newStatus === 'selesai' ? 'completed' : 'in progress';

            return back()->with('success', "Task marked as {$statusText}!");

        } catch (\Exception $e) {
            Log::error('Toggle status failed: ' . $e->getMessage(), [
                'task_id' => $task->id,
            ]);

            return back()->with('error', 'Failed to update task status.');
        }
    }

    /**
     * Duplicate task
     */
    public function duplicate(Task $task)
    {
        // Make sure user owns this task
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $newTask = $task->replicate();
            $newTask->title = $task->title . ' (Copy)';
            $newTask->status = 'berjalan';
            $newTask->created_at = now();
            $newTask->updated_at = now();
            $newTask->save();

            Log::info('Task duplicated', [
                'original_id' => $task->id,
                'new_id' => $newTask->id,
                'user_id' => Auth::id(),
            ]);

            return back()->with('success', 'Task duplicated successfully!');

        } catch (\Exception $e) {
            Log::error('Task duplication failed: ' . $e->getMessage(), [
                'task_id' => $task->id,
            ]);

            return back()->with('error', 'Failed to duplicate task.');
        }
    }

    /**
     * Export tasks to CSV (FIXED: pakai due_date)
     */
    public function export()
    {
        try {
            $user = Auth::user();
            $tasks = $user->tasks()->orderBy('due_date', 'asc')->get();

            $filename = 'zentask_export_' . $user->id . '_' . date('Y-m-d_His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function() use ($tasks) {
                $file = fopen('php://output', 'w');

                // Add BOM for proper UTF-8 encoding
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // CSV Header
                fputcsv($file, ['ID', 'Title', 'Category', 'Description', 'Status', 'Due Date', 'Created At', 'Updated At']);

                // CSV Rows
                foreach ($tasks as $task) {
                    fputcsv($file, [
                        $task->id,
                        $task->title,
                        $task->category,
                        $task->description ?? '',
                        $task->status === 'berjalan' ? 'In Progress' : 'Completed',
                        $task->due_date ? Carbon::parse($task->due_date)->format('Y-m-d H:i:s') : '',
                        $task->created_at->format('Y-m-d H:i:s'),
                        $task->updated_at->format('Y-m-d H:i:s'),
                    ]);
                }

                fclose($file);
            };

            Log::info('Tasks exported', [
                'user_id' => $user->id,
                'task_count' => $tasks->count(),
            ]);

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Task export failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to export tasks. Please try again.');
        }
    }

    /**
     * Get productivity data for charts (FIXED: pakai due_date)
     */
    public function getProductivityData()
    {
        try {
            $user = Auth::user();

            // Last 6 months data
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthName = $date->format('M');

                $completed = $user->tasks()
                    ->where('status', 'selesai')
                    ->whereYear('updated_at', $date->year)
                    ->whereMonth('updated_at', $date->month)
                    ->count();

                $total = $user->tasks()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $data[] = [
                    'month' => $monthName,
                    'year' => $date->year,
                    'completed' => $completed,
                    'total' => $total,
                    'rate' => $total > 0 ? round(($completed / $total) * 100) : 0,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            Log::error('Productivity data failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to load productivity data',
                'data' => [],
            ], 500);
        }
    }
}
