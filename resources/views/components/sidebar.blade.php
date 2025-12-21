{{-- Left Sidebar Navigation --}}
<aside class="w-72 bg-slate-900/40 backdrop-blur-xl border-r border-slate-800/50 flex flex-col transition-all duration-300"
       :class="{ '-translate-x-full lg:translate-x-0': !sidebarOpen }"
       x-transition>

    {{-- Logo & Brand --}}
    <div class="h-20 flex items-center px-6 border-b border-slate-800/50">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="relative">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 via-cyan-500 to-blue-600 flex items-center justify-center transform group-hover:scale-110 transition-all duration-300 glow-blue">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-slate-900 animate-pulse"></div>
            </div>
            <div>
                <span class="text-xl font-bold text-white tracking-tight">Zentask</span>
                <p class="text-xs text-gray-400">Productivity Hub</p>
            </div>
        </a>
    </div>

    {{-- Navigation Menu --}}
    <nav class="flex-1 px-3 py-5 space-y-6 overflow-y-auto custom-scrollbar">

        {{-- Main Section --}}
        <div class="space-y-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Main</p>
            <div class="space-y-1">

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
               class="sidebar-item {{ request()->routeIs('dashboard') ? 'sidebar-item--active' : '' }}">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="flex-1">Dashboard</span>
                @if(request()->routeIs('dashboard'))
                    <div class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></div>
                @endif
            </a>

            {{-- Tasks --}}
            <a href="{{ route('tasks.create') }}"
               class="sidebar-item {{ request()->routeIs('tasks.*') ? 'sidebar-item--active' : '' }}">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <span class="flex-1">Tasks</span>
                <span class="px-2 py-0.5 bg-blue-500/20 text-blue-400 text-xs font-semibold rounded-full">
                    {{ Auth::user()->tasks()->where('status', 'berjalan')->count() }}
                </span>
            </a>

            {{-- Messages --}}
            <a href="{{ route('messages.index') }}"
               class="sidebar-item {{ request()->routeIs('messages.*') ? 'sidebar-item--active' : '' }}">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <span class="flex-1">Messages</span>
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                </span>
            </a>

            {{-- Activity --}}
            <a href="{{ route('activity.index') }}"
               class="sidebar-item {{ request()->routeIs('activity.*') ? 'sidebar-item--active' : '' }}">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="flex-1">Activity</span>
            </a>

            {{-- Calendar --}}
            <a href="{{ route('calendar.index') }}"
               class="sidebar-item {{ request()->routeIs('calendar.*') ? 'sidebar-item--active' : '' }}">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="flex-1">Calendar</span>
            </a>
            </div>
        </div>

        {{-- Settings Section --}}
        <div class="pt-4 border-t border-slate-800/50 space-y-2">
            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Settings</p>
            <div class="space-y-1">

            <a href="{{ route('profile.edit') }}"
               class="sidebar-item {{ request()->routeIs('profile.*') ? 'sidebar-item--active' : '' }}">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="flex-1">Edit Profile</span>
            </a>

            <a href="{{ route('subscription.plans') }}"
               class="sidebar-item {{ request()->routeIs('subscription.*') ? 'sidebar-item--active' : '' }}">
                <div class="sidebar-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <span class="flex-1">Upgrade Plan</span>
                @if(in_array(Auth::user()->subscription_plan, ['pro', 'plus']))
                    <span class="px-2 py-0.5 bg-purple-500/20 text-purple-100 text-xs font-semibold rounded-full border border-purple-400/30">
                        {{ strtoupper(Auth::user()->subscription_plan) }}
                    </span>
                @endif
            </a>

            </div>
        </div>
    </nav>

    {{-- Bottom User Section --}}
    <div class="p-4 border-t border-slate-800/50 space-y-2">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-item sidebar-item--danger w-full text-left">
                <div class="sidebar-icon sidebar-icon--danger">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <span class="flex-1">Log out</span>
            </button>
        </form>
    </div>
</aside>

<style>
.sidebar-item {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.75rem;
    color: #94a3b8;
    font-weight: 500;
    font-size: 0.875rem;
    cursor: pointer;
    background: transparent;
    transition: color 0.25s ease, background-color 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease;
}

.sidebar-item::before {
    content: "";
    position: absolute;
    left: 0.35rem;
    top: 50%;
    width: 3px;
    height: 60%;
    transform: translateY(-50%);
    border-radius: 999px;
    background: linear-gradient(180deg, rgba(56, 189, 248, 0.9), rgba(59, 130, 246, 0.35));
    opacity: 0;
    transition: opacity 0.25s ease;
}

.sidebar-item--active {
    color: #ffffff;
    border: 1px solid rgba(59, 130, 246, 0.3);
    background: linear-gradient(90deg, rgba(59, 130, 246, 0.2), rgba(34, 211, 238, 0.2), rgba(59, 130, 246, 0.1));
    box-shadow: 0 0 20px rgba(96, 165, 250, 0.15), inset 0 0 20px rgba(96, 165, 250, 0.05);
}

.sidebar-item--active::before {
    opacity: 1;
}

.sidebar-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.5rem;
    background: rgba(30, 41, 59, 0.5);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #94a3b8;
    transition: background-color 0.2s ease, transform 0.2s ease, color 0.2s ease;
}

.sidebar-item--active .sidebar-icon {
    background: rgba(59, 130, 246, 0.2);
    color: #60a5fa;
}

.sidebar-item svg {
    width: 1.25rem;
    height: 1.25rem;
    color: inherit;
}

.sidebar-item:hover,
.sidebar-item--hover {
    color: #e2e8f0;
    background: linear-gradient(90deg, rgba(15, 23, 42, 0.2), rgba(59, 130, 246, 0.18), rgba(15, 23, 42, 0.1));
    box-shadow: 0 0 16px rgba(56, 189, 248, 0.12);
}

.sidebar-item:hover::before,
.sidebar-item--hover::before {
    opacity: 0.6;
}

.sidebar-item:hover .sidebar-icon,
.sidebar-item--hover .sidebar-icon {
    background: rgba(59, 130, 246, 0.16);
    color: #e2e8f0;
    transform: scale(1.03);
}

.sidebar-item:focus-visible {
    outline: 2px solid rgba(56, 189, 248, 0.4);
    outline-offset: 2px;
}

.sidebar-item--danger {
    color: #f87171;
    border: 1px solid transparent;
}

.sidebar-item--danger::before {
    background: linear-gradient(180deg, rgba(248, 113, 113, 0.9), rgba(248, 113, 113, 0.3));
}

.sidebar-item--danger:hover {
    background: rgba(248, 113, 113, 0.08);
    color: #fecaca;
}

.sidebar-icon--danger {
    background: rgba(248, 113, 113, 0.12);
    color: #fecaca;
}
</style>
