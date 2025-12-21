{{-- Single Task Card Component --}}
<div class="glass-card rounded-2xl p-5 hover:scale-105 transition-all duration-300 group">
    {{-- Header --}}
    <div class="flex items-start justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full animate-pulse
                {{ $task->status === 'selesai' ? 'bg-green-400' : 'bg-yellow-400' }}">
            </div>
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Today
            </span>
        </div>

        <div class="flex items-center gap-2">
            {{-- Pin Icon --}}
            <button class="p-1.5 text-gray-500 hover:text-yellow-400 hover:bg-slate-800/50 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
            </button>

            {{-- More Options --}}
            <button class="p-1.5 text-gray-500 hover:text-white hover:bg-slate-800/50 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Task Title --}}
    <h3 class="text-lg font-bold text-white mb-2 line-clamp-2 group-hover:text-blue-400 transition-colors">
        {{ $task->title }}
    </h3>

    {{-- Task Description --}}
    <p class="text-gray-400 text-sm mb-4 line-clamp-2">
        {{ $task->description ?? 'No description provided' }}
    </p>

    {{-- Meta Info --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-xs text-gray-400">
                {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('H:i A') : 'No time set' }}
            </span>
        </div>

        {{-- Category Badge --}}
        <span class="px-3 py-1 bg-slate-800/50 text-gray-300 text-xs font-medium rounded-lg">
            {{ $task->category }}
        </span>
    </div>

    {{-- Footer: Date & Status --}}
    <div class="flex items-center justify-between pt-4 border-t border-slate-800/50">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-sm font-medium text-blue-400">
                {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d M Y') : 'No deadline' }}
            </span>
        </div>

        {{-- Status Badge --}}
        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold
            {{ $task->status === 'selesai'
                ? 'bg-green-500/20 text-green-400'
                : 'bg-purple-500/20 text-purple-400' }}">
            {{ $task->status === 'selesai' ? 'COMPLETED' : 'IN PROGRESS' }}
        </span>
    </div>

    {{-- Participants (if you have collaboration feature) --}}
    @if(false) {{-- Enable this when you add collaboration --}}
    <div class="flex items-center gap-3 mt-4 pt-4 border-t border-slate-800/50">
        <span class="text-xs text-gray-500">Participants:</span>
        <div class="flex -space-x-2">
            <img src="https://ui-avatars.com/api/?name=User+1&background=3b82f6&color=fff"
                 class="w-7 h-7 rounded-full border-2 border-slate-900" alt="User 1">
            <img src="https://ui-avatars.com/api/?name=User+2&background=8b5cf6&color=fff"
                 class="w-7 h-7 rounded-full border-2 border-slate-900" alt="User 2">
            <div class="w-7 h-7 rounded-full border-2 border-slate-900 bg-slate-800 flex items-center justify-center text-xs text-gray-400 font-semibold">
                +4
            </div>
        </div>
    </div>
    @endif

    {{-- Quick Action: Edit --}}
    <a href="{{ route('tasks.edit', $task->id) }}"
       class="absolute inset-0 z-10"></a>
</div>
