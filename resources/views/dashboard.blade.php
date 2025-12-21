@extends('layouts.dashboard-layout')

@section('content')
<div class="space-y-6">

    {{-- Hero Section --}}
    <div class="relative overflow-hidden">
        <div class="relative z-10">
            {{-- Welcome Header --}}
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center animate-pulse">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-4xl lg:text-5xl font-bold text-white glow-text-blue">
                        ZenTask
                    </h1>
                </div>
            </div>

            {{-- Greeting & Date --}}
            <div class="mt-6 space-y-2">
                <h2 class="text-2xl lg:text-3xl font-bold text-white">
                    Halo, {{ Auth::user()->name }}
                </h2>
                <p class="text-gray-400 text-lg">
                    {{ now()->format('l, d F Y') }}
                </p>
            </div>
        </div>

        {{-- Decorative Background --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl -z-10"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl -z-10"></div>
    </div>

    {{-- Statistics Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">

        {{-- Total Tasks Card --}}
        <a href="{{ route('dashboard') }}"
           class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:scale-105 transition-transform duration-300">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-blue-500/20 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1 bg-blue-500/20 text-blue-400 text-xs font-semibold rounded-full">
                        +12 MINGGU INI
                    </span>
                </div>

                <p class="text-gray-400 text-sm font-medium mb-2">TOTAL KEGIATAN</p>
                <div class="flex items-end gap-2">
                    <h3 class="text-5xl font-bold text-white">{{ Auth::user()->tasks()->count() }}</h3>
                </div>

                {{-- Progress Bar --}}
                <div class="mt-4 h-2 bg-slate-800/50 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full"
                         style="width: 100%"></div>
                </div>
            </div>
            <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl"></div>
        </a>

        {{-- In Progress Card --}}
        <a href="{{ route('dashboard', ['status' => 'berjalan']) }}"
           class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:scale-105 transition-transform duration-300">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-yellow-500/20 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 text-xs font-semibold rounded-full">
                        BERJALAN
                    </span>
                </div>

                <p class="text-gray-400 text-sm font-medium mb-2">IN PROGRESS</p>
                <div class="flex items-end gap-2">
                    <h3 class="text-5xl font-bold text-white">{{ Auth::user()->tasks()->where('status', 'berjalan')->count() }}</h3>
                </div>

                {{-- Progress Bar --}}
                <div class="mt-4 h-2 bg-slate-800/50 rounded-full overflow-hidden">
                    @php
                        $totalTasks = Auth::user()->tasks()->count();
                        $inProgressTasks = Auth::user()->tasks()->where('status', 'berjalan')->count();
                        $progressPercentage = $totalTasks > 0 ? ($inProgressTasks / $totalTasks) * 100 : 0;
                    @endphp
                    <div class="h-full bg-gradient-to-r from-yellow-500 to-orange-500 rounded-full"
                         style="width: {{ $progressPercentage }}%"></div>
                </div>
            </div>
            <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-yellow-500/10 rounded-full blur-2xl"></div>
        </a>

        {{-- Completed Card --}}
        <a href="{{ route('dashboard', ['status' => 'selesai']) }}"
           class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:scale-105 transition-transform duration-300">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-green-500/20 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1 bg-green-500/20 text-green-400 text-xs font-semibold rounded-full">
                        SELESAI
                    </span>
                </div>

                <p class="text-gray-400 text-sm font-medium mb-2">TASK COMPLETED</p>
                <div class="flex items-end gap-2">
                    <h3 class="text-5xl font-bold text-white">{{ Auth::user()->tasks()->where('status', 'selesai')->count() }}</h3>
                </div>

                {{-- Progress Bar --}}
                <div class="mt-4 h-2 bg-slate-800/50 rounded-full overflow-hidden">
                    @php
                        $completedTasks = Auth::user()->tasks()->where('status', 'selesai')->count();
                        $completedPercentage = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
                    @endphp
                    <div class="h-full bg-gradient-to-r from-green-500 to-emerald-500 rounded-full"
                         style="width: {{ $completedPercentage }}%"></div>
                </div>
            </div>
            <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-green-500/10 rounded-full blur-2xl"></div>
        </a>

        {{-- Nearest Deadline Card --}}
        <a href="{{ route('dashboard', ['view' => 'deadlines']) }}"
           class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:scale-105 transition-transform duration-300">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-red-500/20 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1 bg-red-500/20 text-red-400 text-xs font-semibold rounded-full">
                        URGENT
                    </span>
                </div>

                <p class="text-gray-400 text-sm font-medium mb-2">DEADLINE TERDEKAT</p>
                @php
                    $nearestTask = Auth::user()->tasks()
                        ->where('status', 'berjalan')
                        ->whereNotNull('due_date')
                        ->orderBy('due_date', 'asc')
                        ->first();
                @endphp

                @if($nearestTask)
                    <div class="space-y-2">
                        <h3 class="text-2xl font-bold text-white">
                            {{ \Carbon\Carbon::parse($nearestTask->due_date)->format('d M Y') }}
                        </h3>
                        <p class="text-red-400 text-sm font-medium">{{ $nearestTask->title }}</p>
                    </div>
                @else
                    <div class="space-y-2">
                        <h3 class="text-2xl font-bold text-gray-500">No Deadline</h3>
                        <p class="text-gray-600 text-sm">All tasks completed!</p>
                    </div>
                @endif
            </div>
            <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-red-500/10 rounded-full blur-2xl"></div>
        </a>
    </div>

    {{-- Upcoming Tasks Section --}}
    <div class="glass-card rounded-2xl p-6 lg:p-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-pink-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Upcoming Tasks</h2>
            </div>

            <div class="flex items-center gap-3">
                {{-- Filter Buttons --}}
                <div class="hidden md:flex items-center gap-2">
                    <a href="{{ route('dashboard', ['filter' => 'todo']) }}"
                       class="px-4 py-2 bg-green-500/20 text-green-400 text-sm font-semibold rounded-xl hover:bg-green-500/30 transition-all">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                            To do
                        </span>
                    </a>
                    <a href="{{ route('dashboard', ['filter' => 'work']) }}"
                       class="px-4 py-2 bg-slate-800/50 text-gray-400 text-sm font-semibold rounded-xl hover:bg-slate-700/50 transition-all">
                        Work
                    </a>
                    <a href="{{ route('dashboard', ['filter' => 'high']) }}"
                       class="px-4 py-2 bg-slate-800/50 text-gray-400 text-sm font-semibold rounded-xl hover:bg-slate-700/50 transition-all">
                        High priority
                    </a>
                    <button class="p-2 bg-slate-800/50 text-gray-400 rounded-xl hover:bg-slate-700/50 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </button>
                </div>

                {{-- Show All Button --}}
                @php
                    $hasFilter = request('filter') || request('status');
                @endphp
                @if(request('view') === 'deadlines')
                    <a href="{{ route('dashboard') }}"
                       class="px-4 py-2 bg-slate-800/50 hover:bg-slate-700/50 text-white text-sm font-semibold rounded-xl transition-all">
                        Show Recent
                    </a>
                @else
                    @if($hasFilter)
                        <a href="{{ route('dashboard') }}"
                           class="px-4 py-2 bg-slate-800/50 hover:bg-slate-700/50 text-white text-sm font-semibold rounded-xl transition-all">
                            Show Recent
                        </a>
                    @endif
                    <a href="{{ route('dashboard', ['view' => 'deadlines']) }}"
                       class="px-4 py-2 bg-slate-800/50 hover:bg-slate-700/50 text-white text-sm font-semibold rounded-xl transition-all">
                        Show All ({{ Auth::user()->tasks()->count() }})
                    </a>
                @endif
            </div>
        </div>

        {{-- Task Cards Grid --}}
        @php
            $filter = request('filter');
            $statusFilter = request('status');
            $taskQuery = Auth::user()->tasks();
            if (in_array($statusFilter, ['berjalan', 'selesai'])) {
                $taskQuery->where('status', $statusFilter);
            }
            if ($filter === 'todo') {
                $taskQuery->where('status', 'berjalan');
            } elseif ($filter === 'work') {
                $taskQuery->where('category', 'Work');
            } elseif ($filter === 'high') {
                $taskQuery->where('priority', 'high');
            }

            $upcomingTasks = (clone $taskQuery)
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();
            $deadlineTasks = (clone $taskQuery)
                ->whereNotNull('due_date')
                ->orderBy('due_date', 'asc')
                ->get();
            $deadlineGroups = $deadlineTasks->groupBy(function ($task) {
                return \Carbon\Carbon::parse($task->due_date)->format('Y-m-d');
            });
        @endphp

        @if(request('view') === 'deadlines')
            @if($deadlineGroups->count() > 0)
                <div class="space-y-5">
                    @foreach($deadlineGroups as $deadlineKey => $groupTasks)
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-blue-100">
                                    {{ \Carbon\Carbon::parse($deadlineKey)->format('d M Y') }}
                                </h4>
                                <span class="text-xs text-blue-100/60">{{ $groupTasks->count() }} task</span>
                            </div>
                            <div class="flex gap-4 overflow-x-auto pb-2 custom-scrollbar">
                                @foreach($groupTasks as $task)
                                    <div class="min-w-[260px] max-w-[260px] glass-card rounded-2xl p-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs px-2 py-1 rounded-full bg-blue-500/15 border border-blue-400/20 text-blue-100">
                                                {{ $task->category ?? 'Tanpa kategori' }}
                                            </span>
                                            @if(($task->status ?? '') === 'selesai')
                                                <span class="text-xs px-2 py-1 rounded-full bg-green-500/15 border border-green-400/20 text-green-100">
                                                    SELESAI
                                                </span>
                                            @else
                                                <span class="text-xs px-2 py-1 rounded-full bg-purple-500/15 border border-purple-400/20 text-purple-100">
                                                    IN PROGRESS
                                                </span>
                                            @endif
                                        </div>
                                        <h5 class="mt-3 text-white font-semibold text-sm line-clamp-2">
                                            {{ $task->title ?? 'Untitled' }}
                                        </h5>
                                        <p class="mt-2 text-xs text-blue-100/70 line-clamp-2">
                                            {{ $task->description ?? '-' }}
                                        </p>
                                        <div class="mt-4 flex items-center justify-between">
                                            <span class="text-xs text-blue-100/60">
                                                {{ $task->created_at ? $task->created_at->format('d M Y') : '-' }}
                                            </span>
                                            <a href="{{ route('tasks.edit', $task) }}"
                                                class="text-xs font-semibold text-blue-300 hover:text-blue-200 transition">
                                                Edit
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-24 h-24 mb-6 bg-slate-800/50 rounded-3xl flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">No Tasks Yet!</h3>
                </div>
            @endif
        @else
            @if($upcomingTasks->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6">
                    @foreach($upcomingTasks as $task)
                        @include('components.task-card', ['task' => $task])
                    @endforeach
                </div>
            @else
                {{-- Empty State --}}
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-24 h-24 mb-6 bg-slate-800/50 rounded-3xl flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">No Tasks Yet!</h3>
                </div>
            @endif
        @endif
    </div>

    {{-- Productivity Stats --}}
    <div class="glass-card rounded-2xl p-6 lg:p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Productivity Overview
            </h3>
            <span class="px-4 py-2 bg-gradient-to-r from-green-500/20 to-emerald-500/20 text-green-400 rounded-xl font-bold text-lg">
                {{ $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0 }}% Complete
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-slate-800/30 rounded-xl">
                <p class="text-gray-400 text-sm mb-1">This Week</p>
                <p class="text-2xl font-bold text-white">
                    {{ Auth::user()->tasks()->where('created_at', '>=', now()->startOfWeek())->count() }}
                </p>
            </div>
            <div class="text-center p-4 bg-slate-800/30 rounded-xl">
                <p class="text-gray-400 text-sm mb-1">This Month</p>
                <p class="text-2xl font-bold text-white">
                    {{ Auth::user()->tasks()->whereMonth('created_at', now()->month)->count() }}
                </p>
            </div>
            <div class="text-center p-4 bg-slate-800/30 rounded-xl">
                <p class="text-gray-400 text-sm mb-1">Overdue</p>
                <p class="text-2xl font-bold text-red-400">
                    {{ Auth::user()->tasks()->where('status', 'berjalan')->where('due_date', '<', now())->count() }}
                </p>
            </div>
            <div class="text-center p-4 bg-slate-800/30 rounded-xl">
                <p class="text-gray-400 text-sm mb-1">Avg. Daily</p>
                <p class="text-2xl font-bold text-white">
                    {{ Auth::user()->tasks()->count() > 0 ? round(Auth::user()->tasks()->count() / 30) : 0 }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
