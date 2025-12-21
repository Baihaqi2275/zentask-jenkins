<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 overflow-x-hidden">

<head>
    {{-- ========================================
         META & CORE CONFIGURATION
         ========================================= --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ✅ IMPROVEMENT: Enhanced meta tags for better SEO & PWA support --}}
    <meta name="description" content="Zentask - Premium Task Management Dashboard">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>{{ config('app.name', 'Zentask') }} - Dashboard</title>

    {{-- ✅ IMPROVEMENT: Added Alpine.js for reactive components (notification bell, etc) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- ✅ IMPROVEMENT: Preload critical assets for better performance --}}
    <link rel="preload" href="{{ asset('images/dashboard-bg.png') }}" as="image">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ✅ IMPROVEMENT: Added inline critical CSS for faster initial render --}}
    <style>
        /* Critical CSS for instant render */
        .dashboard-nav { will-change: transform; }
        .nav-link { will-change: transform, background-color; }
    </style>
</head>

<body
    class="h-full font-sans antialiased bg-cover bg-center bg-no-repeat overflow-x-hidden"
    style="background-image: url('{{ asset('images/dashboard-bg.png') }}');"
    {{-- ✅ IMPROVEMENT: Added Alpine.js root data for global state --}}
    x-data="{
        mobileMenuOpen: false,
        notificationOpen: false,
        userMenuOpen: false
    }"
    {{-- ✅ IMPROVEMENT: Accessibility - skip to main content --}}
    role="document">

    {{-- ✅ IMPROVEMENT: Added skip to main content link for accessibility --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50
              focus:px-4 focus:py-2 focus:bg-blue-600 focus:text-white focus:rounded-lg">
        Skip to main content
    </a>

    <!-- Overlay gelap untuk readability yang lebih baik -->
    <div
        class="fixed inset-0 bg-gradient-to-br from-slate-900/40 via-blue-900/30 to-slate-900/40 backdrop-blur-[2px] -z-10"
        {{-- ✅ IMPROVEMENT: Added aria-hidden for accessibility --}}
        aria-hidden="true">
    </div>

    {{-- ========================================
         NAVIGATION BAR - ENHANCED
         ========================================= --}}
    <nav class="dashboard-nav"
         role="navigation"
         aria-label="Main navigation"
         {{-- ✅ IMPROVEMENT: Added keyboard navigation support --}}
         @keydown.escape="mobileMenuOpen = false; userMenuOpen = false">
        <div class="nav-container">
            {{-- ========================================
                 LOGO SECTION
                 ========================================= --}}
            <div class="nav-logo">
                <a href="{{ route('dashboard') }}"
                   aria-label="Zentask Dashboard Home"
                   class="focus:outline-none focus:ring-2 focus:ring-blue-400 rounded-lg">
                    <h1 class="text-2xl font-bold text-white tracking-tight">
                        Zentask<span class="text-blue-400">.</span>
                    </h1>
                </a>
            </div>

            {{-- ========================================
                 NAVIGATION LINKS - ENHANCED
                 ========================================= --}}
            <div class="nav-links"
                 role="menubar"
                 {{-- ✅ IMPROVEMENT: Mobile menu toggle support --}}
                 :class="{ 'mobile-menu-open': mobileMenuOpen }">

                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    role="menuitem"
                    {{-- ✅ IMPROVEMENT: Enhanced accessibility --}}
                    aria-label="Navigate to Dashboard"
                    aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}"
                    {{-- ✅ IMPROVEMENT: Better keyboard navigation --}}
                    tabindex="0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('tasks.index') }}"
                    class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}"
                    role="menuitem"
                    aria-label="Navigate to Tasks"
                    aria-current="{{ request()->routeIs('tasks.*') ? 'page' : 'false' }}"
                    tabindex="0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Tasks</span>
                </a>

                <a href="{{ route('calendar.index') }}"
                    class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}"
                    role="menuitem"
                    aria-label="Navigate to Calendar"
                    aria-current="{{ request()->routeIs('calendar.*') ? 'page' : 'false' }}"
                    tabindex="0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Calendar</span>
                </a>
            </div>

            {{-- ========================================
                 USER MENU & NOTIFICATION SECTION - ENHANCED
                 ========================================= --}}
            <div class="nav-user">

    {{-- Notification Bell --}}

    <div class="notification-slot">

        @if(View::exists('components.notification-bell'))

            <x-notification-bell />

        @else

            {{-- Temporary notification placeholder --}}

            <div class="notification-placeholder"

                 title="Notifications (Coming Soon)"

                 role="button"

                 aria-label="Notifications - Feature coming soon"

                 tabindex="0">

                <svg class="w-6 h-6 text-gray-400 hover:text-blue-400 transition-colors"

                     fill="none"

                     stroke="currentColor"

                     viewBox="0 0 24 24"

                     aria-hidden="true">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"

                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>

                </svg>

            </div>

        @endif

    </div>



            {{-- User Menu Component (BARU!) --}}
            <x-zentask-user-menu />

                {{-- ✅ IMPROVEMENT: Mobile menu toggle button --}}
                <button class="mobile-menu-toggle"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        aria-label="Toggle mobile menu"
                        aria-expanded="false"
                        type="button">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    {{-- ========================================
         MAIN CONTENT AREA - ENHANCED
         ========================================= --}}
    <main class="dashboard-content"
          id="main-content"
          role="main"
          aria-label="Main content area"
          {{-- ✅ IMPROVEMENT: Added focus management --}}
          tabindex="-1">
        <div class="content-container">
            {{-- ✅ IMPROVEMENT: Added loading state indicator --}}
            <div x-data="{ pageLoading: false }"
                 @page-loading.window="pageLoading = true"
                 @page-loaded.window="pageLoading = false">

                {{-- Loading overlay --}}
                <div x-show="pageLoading"
                     x-transition
                     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
                     role="status"
                     aria-live="polite"
                     aria-label="Loading content">
                    <div class="loading-spinner">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-400"></div>
                        <p class="text-white mt-4">Loading...</p>
                    </div>
                </div>

                {{-- Main content yield --}}
                @yield('content')
            </div>
        </div>
    </main>

    {{-- ✅ IMPROVEMENT: Added footer section for better structure --}}
    <footer class="dashboard-footer" role="contentinfo" aria-label="Footer">
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
    {{-- ========================================
         ORIGINAL STYLES (PRESERVED & ENHANCED)
         ========================================= --}}

    /* Navigation Bar - Style Premium */
    .dashboard-nav {
        background: rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1rem 0;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        {{-- ✅ IMPROVEMENT: Added smooth transition on scroll --}}
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
        {{-- ✅ IMPROVEMENT: Added hover effect --}}
        transition: transform 0.3s ease;
    }

    {{-- ✅ IMPROVEMENT: Added logo hover animation --}}
    .nav-logo:hover {
        transform: scale(1.05);
    }

    .nav-links {
        display: flex;
        gap: 0.5rem;
        flex: 1;
        {{-- ✅ IMPROVEMENT: Better responsive behavior --}}
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
        {{-- ✅ IMPROVEMENT: Better focus states --}}
        outline: none;
    }

    {{-- ✅ IMPROVEMENT: Enhanced focus visible state --}}
    .nav-link:focus-visible {
        outline: 2px solid #60a5fa;
        outline-offset: 2px;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(96, 165, 250, 0.1), rgba(59, 130, 246, 0.1));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .nav-link:hover {
        color: white;
        transform: translateY(-2px);
        {{-- ✅ IMPROVEMENT: Added subtle shadow on hover --}}
        box-shadow: 0 4px 12px rgba(96, 165, 250, 0.2);
    }

    .nav-link:hover::before {
        opacity: 1;
    }

    .nav-link.active {
        background: linear-gradient(135deg, rgba(96, 165, 250, 0.25), rgba(59, 130, 246, 0.25));
        color: #60a5fa;
        border: 1px solid rgba(96, 165, 250, 0.4);
        box-shadow: 0 4px 12px rgba(96, 165, 250, 0.3);
    }

    {{-- ✅ IMPROVEMENT: Active link pulse animation --}}
    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 50%;
        transform: translateX(-50%);
        width: 40%;
        height: 2px;
        background: linear-gradient(90deg, transparent, #60a5fa, transparent);
        animation: pulse-line 2s ease-in-out infinite;
    }

    @keyframes pulse-line {
        0%, 100% { opacity: 0.5; width: 40%; }
        50% { opacity: 1; width: 60%; }
    }

    /* User Menu */
    .nav-user {
        display: flex;
        align-items: center;
        gap: 1rem;
        {{-- ✅ IMPROVEMENT: Better flex behavior --}}
        flex-shrink: 0;
    }

    {{-- ✅ IMPROVEMENT: Added notification slot styles --}}
    .notification-slot {
        display: flex;
        align-items: center;
        {{-- Reserved space for notification bell component --}}
    }

    .notification-placeholder {
        padding: 0.5rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        {{-- ✅ IMPROVEMENT: Interactive placeholder --}}
    }

    .notification-placeholder:hover {
        background: rgba(255, 255, 255, 0.05);
        transform: scale(1.1);
    }

    .notification-placeholder:focus {
        outline: 2px solid #60a5fa;
        outline-offset: 2px;
    }

    .user-info {
        display: none;
        flex-direction: column;
        align-items: flex-end;
        {{-- ✅ IMPROVEMENT: Smooth appearance --}}
        animation: fadeIn 0.3s ease;
    }

    @media (min-width: 768px) {
        .user-info {
            display: flex;
        }
    }

    .user-name {
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        {{-- ✅ IMPROVEMENT: Better text rendering --}}
        text-rendering: optimizeLegibility;
    }

    .user-email {
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.75rem;
        {{-- ✅ IMPROVEMENT: Prevent email overflow --}}
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #60a5fa, #3b82f6);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.125rem;
        box-shadow: 0 4px 16px rgba(96, 165, 250, 0.5);
        border: 2px solid rgba(255, 255, 255, 0.2);
        {{-- ✅ IMPROVEMENT: Made avatar interactive --}}
        cursor: pointer;
        transition: all 0.3s ease;
        outline: none;
    }

    {{-- ✅ IMPROVEMENT: Avatar hover & focus states --}}
    .user-avatar:hover {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 6px 20px rgba(96, 165, 250, 0.7);
    }

    .user-avatar:focus-visible {
        outline: 2px solid #60a5fa;
        outline-offset: 3px;
    }

    .user-avatar:active {
        transform: scale(0.95);
    }

    .logout-btn {
        padding: 0.625rem;
        color: rgba(255, 255, 255, 0.7);
        background: transparent;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        {{-- ✅ IMPROVEMENT: Better interaction feedback --}}
        outline: none;
    }

    .logout-btn:hover {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        transform: scale(1.05);
        {{-- ✅ IMPROVEMENT: Added rotate animation on hover --}}
        animation: shake 0.5s ease;
    }

    {{-- ✅ IMPROVEMENT: Focus state for logout button --}}
    .logout-btn:focus-visible {
        outline: 2px solid #ef4444;
        outline-offset: 2px;
    }

    {{-- ✅ IMPROVEMENT: Shake animation for logout button --}}
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    {{-- ✅ IMPROVEMENT: Mobile menu toggle button styles --}}
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
        {{-- ✅ IMPROVEMENT: Smooth scroll behavior --}}
        scroll-behavior: smooth;
    }

    .content-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
        {{-- ✅ IMPROVEMENT: Content fade-in animation --}}
        animation: fadeIn 0.5s ease;
    }

    {{-- ✅ IMPROVEMENT: Fade-in animation --}}
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

    {{-- ✅ IMPROVEMENT: Added footer styles --}}
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

    {{-- ✅ IMPROVEMENT: Loading spinner styles --}}
    .loading-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    {{-- ✅ IMPROVEMENT: Screen reader only utility --}}
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;
    }

    .sr-only:focus {
        position: static;
        width: auto;
        height: auto;
        padding: inherit;
        margin: inherit;
        overflow: visible;
        clip: auto;
        white-space: normal;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .nav-links {
            {{-- ✅ IMPROVEMENT: Mobile menu behavior --}}
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

        {{-- ✅ IMPROVEMENT: Mobile menu open state --}}
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

        {{-- ✅ IMPROVEMENT: Better mobile spacing --}}
        .dashboard-content {
            padding: 1.5rem 0;
        }

        .nav-user {
            gap: 0.5rem;
        }

        {{-- ✅ IMPROVEMENT: Hide email on very small screens --}}
        .user-email {
            display: none;
        }
    }

    {{-- ✅ IMPROVEMENT: High contrast mode support --}}
    @media (prefers-contrast: high) {
        .nav-link {
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .user-avatar {
            border: 3px solid rgba(255, 255, 255, 0.5);
        }
    }

    {{-- ✅ IMPROVEMENT: Reduced motion support for accessibility --}}
    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
    }

    {{-- ✅ IMPROVEMENT: Print styles --}}
    @media print {
        .dashboard-nav, 
        .dashboard-footer,
        .notification-slot,
        .logout-btn,
        .mobile-menu-toggle {
            display: none !important;
        }

        .dashboard-content {
            padding: 0;
            min-height: auto;
        }

        body {
            background: white !important;
        }
    }
</style>
