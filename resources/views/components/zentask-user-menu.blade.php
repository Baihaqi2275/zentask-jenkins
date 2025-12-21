{{-- ZenTask User Menu Dropdown Component --}}

<div class="relative" x-data="{ open: false }" @click.away="open = false">
    {{-- User Profile Button --}}
    <button
        @click="open = !open"
        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-blue-600/20 transition-all duration-200"
        type="button"
        aria-expanded="false"
        aria-haspopup="true"
    >
        {{-- User Info (Name & Email) --}}
        <div class="text-right hidden sm:block">
            <p class="text-sm font-medium text-white group-hover:text-blue-400 transition-colors">
                {{ Auth::user()->name }}
            </p>
            <p class="text-xs text-gray-400">
                {{ Auth::user()->email }}
            </p>
        </div>

        {{-- User Avatar --}}
        <div class="relative">
    @if(Auth::user()->avatar)
        <img
            src="{{ asset('storage/' . Auth::user()->avatar) }}"
            alt="{{ Auth::user()->name }}"
            class="w-10 h-10 rounded-full object-cover border-2 border-blue-400/30 hover:border-blue-400 transition-all"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
        >
    @endif

    {{-- Fallback Initial --}}
    <div
        class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-lg border-2 border-blue-400/30 hover:border-blue-400 transition-all
               {{ Auth::user()->avatar ? 'hidden' : '' }}">
        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
    </div>

    {{-- Online indicator --}}
    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-slate-950"></div>
    </div>
    </button>

    {{-- Dropdown Menu --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-3 w-64 rounded-xl bg-slate-900/95 backdrop-blur-xl shadow-2xl border border-slate-700/50 overflow-hidden z-60"
        style="display: none;"
    >
        {{-- User Info Header --}}
        <div class="px-4 py-4 bg-gradient-to-br from-blue-600/10 to-purple-600/10 border-b border-slate-700/50">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-xl border-2 border-blue-400/30">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>

        {{-- Menu Items --}}
        <div class="py-2">
            {{-- Edit Profile --}}
            <a
                href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-blue-600/10 hover:text-blue-400 transition-all duration-150 group"
            >
                <div class="w-9 h-9 rounded-lg bg-blue-600/10 flex items-center justify-center group-hover:bg-blue-600/20 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-medium">Edit Profile</p>
                    <p class="text-xs text-gray-500">Update your information</p>
                </div>
                <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            {{-- Upgrade Plan --}}
            <a
                href="{{ route('subscription.plans') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-purple-600/10 hover:text-purple-400 transition-all duration-150 group"
            >
                <div class="w-9 h-9 rounded-lg bg-purple-600/10 flex items-center justify-center group-hover:bg-purple-600/20 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-medium">Upgrade Plan</p>
                    <p class="text-xs text-gray-500">Unlock premium features</p>
                </div>
                <span class="px-2 py-0.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-xs font-semibold rounded-full">
                    PRO
                </span>
            </a>
        </div>

        {{-- Divider --}}
        <div class="border-t border-slate-700/50 my-1"></div>

        {{-- Logout --}}
        <div class="py-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    onclick="return confirm('Are you sure you want to logout?')"
                    class="flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-600/10 hover:text-red-300 transition-all duration-150 w-full group"
                >
                    <div class="w-9 h-9 rounded-lg bg-red-600/10 flex items-center justify-center group-hover:bg-red-600/20 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <div class="flex-1 text-left">
                        <p class="font-medium">Log Out</p>
                        <p class="text-xs text-gray-500">Sign out of your account</p>
                    </div>
                    <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
