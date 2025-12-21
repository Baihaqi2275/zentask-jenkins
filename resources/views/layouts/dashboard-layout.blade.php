<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - Zentask</title>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            background: radial-gradient(ellipse at top, #1e293b 0%, #0f172a 50%, #020617 100%);
            background-attachment: fixed;
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
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

        /* Glassmorphism */
        .glass {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(96, 165, 250, 0.1);
        }

        .glass-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.7) 0%, rgba(15, 23, 42, 0.8) 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(96, 165, 250, 0.15);
        }

        /* Glow Effects */
        .glow-blue {
            box-shadow: 0 0 30px rgba(96, 165, 250, 0.4), 0 0 60px rgba(96, 165, 250, 0.2);
        }

        .glow-text-blue {
            text-shadow: 0 0 20px rgba(96, 165, 250, 0.6);
        }

        /* Smooth Animations */
        .transition-smooth {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>

<body class="h-full overflow-hidden antialiased"
      x-data="{
          sidebarOpen: true,
          searchOpen: false,
          taskModal: false,
          notificationOpen: false
      }">

    {{-- Stars Background Animation --}}
    <div class="fixed inset-0 pointer-events-none opacity-30">
        <div class="absolute inset-0" style="background-image: radial-gradient(2px 2px at 20% 30%, white, transparent), radial-gradient(2px 2px at 60% 70%, white, transparent), radial-gradient(1px 1px at 50% 50%, white, transparent); background-size: 200px 200px, 300px 300px, 150px 150px; background-position: 0 0, 40px 60px, 130px 270px;"></div>
    </div>

    {{-- Main Container: 3-Column Layout --}}
    <div class="flex h-screen bg-transparent relative z-10">

        {{-- ========================================
             LEFT SIDEBAR - Fixed Navigation
             ========================================= --}}
        @include('components.sidebar')

        {{-- ========================================
             CENTER: Main Content Area
             ========================================= --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-visible">

            {{-- Top Navigation Bar --}}
            @include('components.topbar')

            {{-- Main Content --}}
            <main class="flex-1 overflow-y-auto custom-scrollbar bg-gradient-to-b from-transparent to-slate-950/30">
                <div class="p-6 lg:p-8">
                    @yield('content')
                </div>
            </main>
        </div>

        {{-- ========================================
             RIGHT SIDEBAR - Activity & Notes
             ========================================= --}}
        @include('components.right-sidebar')
    </div>

    {{-- Task Creation Modal (Quick Add) --}}
    <div x-show="taskModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         style="display: none;"
         @click.self="taskModal = false">

        <div class="glass-card rounded-2xl p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto custom-scrollbar"
             @click.stop
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white">Create New Task</h2>
                <button @click="taskModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Task Title</label>
                    <input type="text" name="title" required
                           class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                    <input type="text" name="category" required
                           class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Deadline</label>
                    <input type="date" name="deadline" required
                           class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="taskModal = false"
                            class="flex-1 px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white rounded-xl font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white rounded-xl font-medium transition-all transform hover:scale-105">
                        Create Task
                    </button>
                </div>
            </form>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
