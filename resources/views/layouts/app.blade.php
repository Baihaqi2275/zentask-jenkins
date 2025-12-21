<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Zentask - Premium Task Management Dashboard">
    <meta name="theme-color" content="#0f172a">

    <title>{{ $title ?? config('app.name', 'Zentask') }}</title>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-image: url('{{ asset('images/dashboard-bg.png') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>

<body class="h-full font-sans antialiased bg-cover bg-center bg-no-repeat overflow-x-hidden"
      x-data="{ mobileMenuOpen: false, notificationOpen: false, userMenuOpen: false }">

    {{-- Background Overlay --}}
    <div class="fixed inset-0 bg-gradient-to-br from-slate-900/40 via-blue-900/30 to-slate-900/40 backdrop-blur-[2px] -z-10"
         aria-hidden="true">
    </div>

    {{-- Navigation Bar --}}
    <nav class="dashboard-nav" role="navigation" aria-label="Main navigation">
        <div class="nav-container">
            {{-- Logo --}}
            <div class="nav-logo">
                <a href="{{ route('dashboard') }}" class="focus:outline-none focus:ring-2 focus:ring-blue-400 rounded-lg">
                    <h1 class="text-2xl font-bold text-white tracking-tight">
                        Zentask<span class="text-blue-400">.</span>
                    </h1>
                </a>
            </div>

            {{-- Navigation Links --}}
            <div class="nav-links" :class="{ 'mobile-menu-open': mobileMenuOpen }">
                <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('tasks.index') }}"
                   class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Tasks</span>
                </a>

                <a href="{{ route('calendar.index') }}"
                   class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Calendar</span>
                </a>
            </div>

            {{-- User Menu --}}
            <div class="nav-user">
                {{-- Notification --}}
                <div class="notification-slot">
                    <div class="notification-placeholder">
                        <svg class="w-6 h-6 text-gray-400 hover:text-blue-400 transition-colors"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                </div>

                {{-- User Menu Component --}}
                <x-zentask-user-menu />

                {{-- Mobile Toggle --}}
                <button class="mobile-menu-toggle"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        type="button">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="dashboard-content" role="main">
        <div class="content-container">
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="dashboard-footer" role="contentinfo">
        <div class="content-container">
            <div class="footer-content">
                <p class="text-gray-400 text-sm">
                    © {{ date('Y') }} Zentask. All rights reserved.
                </p>
                <p class="text-gray-500 text-xs">
                    Premium Task Management System
                </p>
            </div>
        </div>
    </footer>

</body>
</html>

<style>
    /* Navigation Bar */
    .dashboard-nav {
        background: rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(24px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1rem 0;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
    }

    .nav-logo {
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }

    .nav-logo:hover {
        transform: scale(1.05);
    }

    .nav-links {
        display: flex;
        gap: 0.5rem;
        flex: 1;
        transition: all 0.3s ease;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
        font-size: 0.938rem;
        position: relative;
        outline: none;
    }

    .nav-link:hover {
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(96, 165, 250, 0.2);
    }

    .nav-link.active {
        background: linear-gradient(135deg, rgba(96, 165, 250, 0.25), rgba(59, 130, 246, 0.25));
        color: #60a5fa;
        border: 1px solid rgba(96, 165, 250, 0.4);
        box-shadow: 0 4px 12px rgba(96, 165, 250, 0.3);
    }

    .nav-user {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-shrink: 0;
    }

    .notification-slot {
        display: flex;
        align-items: center;
    }

    .notification-placeholder {
        padding: 0.5rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .notification-placeholder:hover {
        background: rgba(255, 255, 255, 0.05);
        transform: scale(1.1);
    }

    .mobile-menu-toggle {
        display: none;
        padding: 0.5rem;
        color: white;
        background: transparent;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .mobile-menu-toggle:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    /* Main Content */
    .dashboard-content {
        padding: 2.5rem 0;
        min-height: calc(100vh - 80px);
    }

    .content-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Footer */
    .dashboard-footer {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(16px);
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        padding: 2rem 0;
        margin-top: 4rem;
    }

    .footer-content {
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .nav-links {
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            background: rgba(15, 23, 42, 0.98);
            backdrop-filter: blur(24px);
            flex-direction: column;
            padding: 1rem;
            gap: 0.5rem;
            transform: translateY(-100%);
            opacity: 0;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-links.mobile-menu-open {
            transform: translateY(0);
            opacity: 1;
        }

        .mobile-menu-toggle {
            display: block;
        }

        .content-container {
            padding: 0 1rem;
        }

        .nav-container {
            padding: 0 1rem;
        }

        .dashboard-content {
            padding: 1.5rem 0;
        }
    }
</style>
