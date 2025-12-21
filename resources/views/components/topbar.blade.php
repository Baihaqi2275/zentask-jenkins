{{-- Top Navigation Bar --}}
<header class="relative z-50 h-20 bg-slate-900/40 backdrop-blur-xl border-b border-slate-800/50 flex items-center px-6 lg:px-8">
    <div class="flex items-center justify-between w-full gap-4">

        {{-- Left: Breadcrumb / Date Selector --}}
        <div class="flex items-center gap-4">
            {{-- Mobile Menu Toggle --}}
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden p-2 text-gray-400 hover:text-white hover:bg-slate-800/50 rounded-lg transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Breadcrumb Navigation --}}
            <div class="hidden md:flex items-center gap-2 text-sm">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Home</span>
                </a>
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-white font-medium">{{ $breadcrumb ?? 'Dashboard' }}</span>
            </div>

            {{-- Date Selector --}}
            <div class="hidden lg:flex items-center gap-2 px-4 py-2 bg-slate-800/50 rounded-xl border border-slate-700/50 cursor-pointer hover:border-blue-500/50 transition-all group">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-white font-medium text-sm">{{ now()->format('d M Y') }}</span>
                <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        {{-- Right: Search, Notifications, User --}}
        <div class="flex items-center gap-3">

            {{-- Search Bar --}}
            <div class="search-wrapper" x-data="searchComponent()">
                {{-- Collapsed Search --}}
                <button @click="openSearch()"
                        x-show="!searchOpen"
                        class="hidden md:flex items-center gap-2 px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl hover:border-blue-500/50 transition-all group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span class="text-gray-400 text-sm group-hover:text-white transition-colors">Search tasks...</span>
                    <kbd class="hidden lg:inline-block px-2 py-0.5 text-xs font-semibold text-gray-500 bg-slate-700/50 rounded">Ctrl+K</kbd>
                </button>

                {{-- Expanded Search --}}
                <div x-show="searchOpen"
                     x-transition
                     class="search-active"
                     @click.away="closeSearch()">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           x-model="searchQuery"
                           @input.debounce.300ms="performSearch()"
                           @keydown.escape="closeSearch()"
                           placeholder="Search tasks, categories, descriptions..."
                           class="search-input"
                           x-ref="searchInput"
                           autocomplete="off">
                    <button @click="closeSearch()" class="search-close-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    {{-- Search Dropdown Results --}}
                    <div x-show="searchQuery.length > 0"
                         class="search-dropdown"
                         x-transition>

                        {{-- Loading State --}}
                        <div x-show="searchLoading" class="search-loading">
                            <svg class="animate-spin h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-gray-400 text-sm mt-2">Searching...</span>
                        </div>

                        {{-- No Results --}}
                        <div x-show="!searchLoading && searchResults.length === 0 && searchQuery.length > 0" class="search-empty">
                            <svg class="w-16 h-16 text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-400 font-medium">No results found</p>
                            <p class="text-gray-600 text-sm mt-1">Try different keywords</p>
                        </div>

                        {{-- Results List --}}
                        <div x-show="!searchLoading && searchResults.length > 0">
                            <div class="px-4 py-2 border-b border-slate-800/50">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Found <span x-text="searchResults.length"></span> result(s)
                                </p>
                            </div>
                            <template x-for="result in searchResults" :key="result.id">
                                <a :href="`/tasks/${result.id}/edit`"
                                   class="search-result-item"
                                   @click="closeSearch()">
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        <div class="search-result-icon"
                                             :class="{
                                                 'bg-green-500/20 text-green-400': result.status === 'selesai',
                                                 'bg-yellow-500/20 text-yellow-400': result.status === 'berjalan'
                                             }">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-white font-medium text-sm truncate" x-text="result.title"></p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-gray-400 text-xs" x-text="result.category"></span>
                                                <span class="text-gray-600">|</span>
                                                <span class="text-gray-500 text-xs" x-text="result.deadline_formatted || 'No deadline'"></span>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="px-2 py-1 rounded-lg text-xs font-medium"
                                                  :class="{
                                                      'bg-green-500/20 text-green-400': result.status === 'selesai',
                                                      'bg-yellow-500/20 text-yellow-400': result.status === 'berjalan'
                                                  }"
                                                  x-text="result.status === 'selesai' ? 'Completed' : 'In Progress'">
                                            </span>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile Search Icon --}}
            <button class="md:hidden p-2 text-gray-400 hover:text-white hover:bg-slate-800/50 rounded-lg transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>

            {{-- Add Member Button --}}
            <button class="hidden xl:flex items-center gap-2 px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl hover:border-blue-500/50 transition-all group">
                <div class="flex -space-x-2">
                    <img src="https://ui-avatars.com/api/?name=User+1&background=3b82f6&color=fff"
                         class="w-7 h-7 rounded-full border-2 border-slate-900" alt="User 1">
                    <img src="https://ui-avatars.com/api/?name=User+2&background=8b5cf6&color=fff"
                         class="w-7 h-7 rounded-full border-2 border-slate-900" alt="User 2">
                    <img src="https://ui-avatars.com/api/?name=User+3&background=ec4899&color=fff"
                         class="w-7 h-7 rounded-full border-2 border-slate-900" alt="User 3">
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-sm text-gray-400 group-hover:text-white transition-colors">Add member</span>
            </button>

            {{-- Notification Bell --}}
            <x-notification-bell />

            {{-- Profile Avatar (visual-only) --}}
            <div class="flex items-center gap-3 px-2 py-1.5 rounded-lg">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-medium text-white">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ Auth::user()->email }}
                    </p>
                </div>
                <div class="relative">
                    @if(Auth::user()->avatar)
                        <img
                            src="{{ asset('storage/' . Auth::user()->avatar) }}"
                            alt="{{ Auth::user()->name }}"
                            class="w-10 h-10 rounded-full object-cover border-2 border-blue-400/30"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >
                    @endif
                    <div
                        class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-lg border-2 border-blue-400/30
                               {{ Auth::user()->avatar ? 'hidden' : '' }}">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-slate-950"></div>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Search Component Script --}}
<script>
function searchComponent() {
    return {
        searchQuery: '',
        searchResults: [],
        searchLoading: false,
        searchOpen: false,

        openSearch() {
            this.searchOpen = true;
            this.$nextTick(() => {
                this.$refs.searchInput.focus();
            });
        },

        closeSearch() {
            this.searchOpen = false;
            this.searchQuery = '';
            this.searchResults = [];
        },

        async performSearch() {
            if (this.searchQuery.length < 2) {
                this.searchResults = [];
                return;
            }

            this.searchLoading = true;

            try {
                const response = await fetch(`/api/tasks/search?q=${encodeURIComponent(this.searchQuery)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) throw new Error('Search failed');

                const data = await response.json();
                this.searchResults = data.results || [];

            } catch (error) {
                console.error('Search error:', error);
                this.searchResults = [];
            } finally {
                this.searchLoading = false;
            }
        }
    }
}

// Keyboard shortcut: CMD/CTRL + K
document.addEventListener('keydown', function(e) {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        // Trigger Alpine component
        window.dispatchEvent(new CustomEvent('open-search'));
    }
});
</script>

<style>
.search-wrapper {
    position: relative;
}

.search-active {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.625rem 1rem;
    background: rgba(30, 41, 59, 0.8);
    border: 2px solid rgba(96, 165, 250, 0.5);
    border-radius: 12px;
    min-width: 400px;
    box-shadow: 0 4px 30px rgba(96, 165, 250, 0.3);
}

.search-input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    color: white;
    font-size: 0.875rem;
}

.search-input::placeholder {
    color: rgba(255, 255, 255, 0.4);
}

.search-close-btn {
    padding: 0.25rem;
    color: rgba(255, 255, 255, 0.5);
    hover:color: white;
    transition: color 0.2s;
    border-radius: 0.375rem;
}

.search-close-btn:hover {
    background: rgba(255, 255, 255, 0.1);
}

.search-dropdown {
    position: absolute;
    top: calc(100% + 0.75rem);
    left: 0;
    right: 0;
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.98) 0%, rgba(30, 41, 59, 0.98) 100%);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(96, 165, 250, 0.2);
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), 0 0 40px rgba(96, 165, 250, 0.1);
    max-height: 500px;
    overflow-y: auto;
    z-index: 100;
}

.search-loading,
.search-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 2rem;
}

.search-result-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.2s ease;
    text-decoration: none;
    cursor: pointer;
}

.search-result-item:hover {
    background: rgba(96, 165, 250, 0.1);
    border-left: 3px solid rgba(96, 165, 250, 0.5);
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .search-active {
        min-width: 280px;
    }
}
</style>
