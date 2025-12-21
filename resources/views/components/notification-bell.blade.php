{{-- Notification Bell Component --}}
<div class="relative"
     x-data="notificationBell()"
     x-init="init()"
     @click.away="open = false">

    {{-- Bell Button --}}
    <button
        @click="toggleDropdown()"
        class="relative p-2 text-gray-300 hover:text-blue-400 hover:bg-slate-800/50 rounded-lg transition-all duration-200 group"
        type="button"
        aria-label="Notifications"
        :aria-expanded="open">

        {{-- Bell Icon --}}
        <svg class="w-6 h-6 transition-transform group-hover:scale-110"
             :class="{ 'animate-pulse text-blue-400': hasNotifications }"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        {{-- Badge Count --}}
        <span x-show="count > 0"
              x-transition
              class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center ring-2 ring-slate-900"
              x-text="count > 9 ? '9+' : count">
        </span>
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-3 w-80 sm:w-96 max-h-[520px] rounded-xl bg-slate-900/95 backdrop-blur-xl shadow-2xl border border-slate-700/50 overflow-hidden z-60"
         style="display: none;">

        {{-- Header --}}
        <div class="px-4 py-3 bg-gradient-to-r from-blue-600/10 to-purple-600/10 border-b border-slate-700/50 flex items-center justify-between">
            <h3 class="text-white font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Notifications
                <span x-show="count > 0"
                      class="px-2 py-0.5 bg-blue-600 text-white text-xs rounded-full"
                      x-text="count">
                </span>
            </h3>

            <button @click="refreshNotifications()"
                    class="p-1 hover:bg-slate-800 rounded transition-colors"
                    title="Refresh">
                <svg class="w-4 h-4 text-gray-400 hover:text-white"
                     :class="{ 'animate-spin': loading }"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </div>

        {{-- Loading State --}}
        <div x-show="loading && tasks.length === 0" class="p-8 text-center">
            <svg class="w-8 h-8 mx-auto text-blue-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-400 text-sm mt-2">Loading notifications...</p>
        </div>

        {{-- Empty State --}}
        <div x-show="!loading && tasks.length === 0" class="p-8 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-slate-800 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-white font-medium mb-1">All Caught Up!</p>
            <p class="text-gray-400 text-sm">No pending tasks</p>
        </div>

        {{-- Task List --}}
        <div x-show="!loading && tasks.length > 0"
             class="max-h-[360px] overflow-y-auto custom-scrollbar">

            <template x-for="task in tasks" :key="task.id">
                <a :href="`/tasks/${task.id}/edit`"
                   class="block mx-3 my-2 rounded-lg border border-slate-700/40 bg-slate-800/30 hover:bg-slate-800/60 hover:border-blue-500/30 transition-colors group">

                    <div class="flex items-start gap-3 px-3 py-3">
                        {{-- Priority Indicator --}}
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-2 h-2 rounded-full"
                                 :class="{
                                     'bg-red-500 animate-pulse': task.status_class === 'overdue',
                                     'bg-yellow-500': task.status_class === 'today',
                                     'bg-blue-500': task.status_class === 'upcoming' || task.status_class === 'pending',
                                     'bg-slate-500': task.status_class === 'no_deadline'
                                 }">
                            </div>
                        </div>

                        {{-- Task Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-medium text-sm truncate group-hover:text-blue-400 transition-colors"
                               x-text="task.title">
                            </p>

                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-gray-400 text-xs" x-text="task.category"></span>
                                <span class="text-gray-600">|</span>
                                <span class="text-gray-400 text-xs" x-text="task.deadline_formatted"></span>
                            </div>

                            {{-- Status Badge --}}
                            <div class="mt-2">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                                      :class="{
                                          'bg-red-600/20 text-red-400': task.status_class === 'overdue',
                                          'bg-yellow-600/20 text-yellow-400': task.status_class === 'today',
                                          'bg-blue-600/20 text-blue-400': task.status_class === 'upcoming' || task.status_class === 'pending',
                                          'bg-slate-600/20 text-slate-300': task.status_class === 'no_deadline'
                                      }">
                                    <template x-if="task.status_class === 'overdue'">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </template>
                                    <span x-text="task.status_label"></span>
                                    <template x-if="task.days_overdue">
                                        <span x-text="`(${task.days_overdue}d)`"></span>
                                    </template>
                                    <template x-if="task.days_until">
                                        <span x-text="`(${task.days_until}d)`"></span>
                                    </template>
                                </span>
                            </div>
                        </div>

                        {{-- Arrow Icon --}}
                        <svg class="w-4 h-4 text-gray-600 group-hover:text-blue-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            </template>
        </div>

        {{-- Footer --}}
        <div x-show="tasks.length > 0"
             class="px-4 py-3 bg-slate-800/30 border-t border-slate-700/50">
            <a href="/tasks"
               class="block text-center text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors">
                View all tasks
            </a>
        </div>
    </div>
</div>

{{-- Alpine.js Component Logic --}}
<script>
function notificationBell() {
    return {
        open: false,
        loading: false,
        count: 0,
        tasks: [],
        hasNotifications: false,

        init() {
            this.fetchNotifications();
            // Auto-refresh every 60 seconds
            setInterval(() => {
                this.fetchNotifications();
            }, 60000);
        },

        async toggleDropdown() {
            this.open = !this.open;
            if (this.open) {
                await this.fetchNotifications();
            }
        },

        async fetchNotifications() {
            this.loading = true;

            try {
                const response = await fetch('/notifications/today', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch notifications');

                const data = await response.json();

                this.tasks = data.tasks || [];
                this.count = data.total_count || 0;
                this.hasNotifications = data.has_tasks || false;

            } catch (error) {
                console.error('Error fetching notifications:', error);
                this.tasks = [];
                this.count = 0;
            } finally {
                this.loading = false;
            }
        },

        async refreshNotifications() {
            await this.fetchNotifications();
        }
    }
}
</script>

{{-- Custom Scrollbar Styles --}}
<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(15, 23, 42, 0.5);
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(96, 165, 250, 0.3);
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(96, 165, 250, 0.5);
}
</style>
