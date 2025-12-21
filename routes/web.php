<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\FileController;

/* =========================================================
 | [ROOT] LANDING PAGE
 ========================================================= */
Route::get('/', function () {
    // Redirect based on authentication status
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('register');
})->name('home');

/* =========================================================
 | [A] AUTHENTICATED + VERIFIED ROUTES
 ========================================================= */
Route::middleware(['auth', 'verified'])->group(function () {

    /* -------------------------
     | [A1] DASHBOARD
     ------------------------- */
    Route::get('/dashboard', [TaskController::class, 'index'])
        ->name('dashboard');

    /* -------------------------
     | [A2] TASKS (CRUD)
     ------------------------- */
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/create', [TaskController::class, 'create'])->name('create');
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');

        // Additional task routes
        Route::patch('/{task}/toggle-status', [TaskController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{task}/duplicate', [TaskController::class, 'duplicate'])->name('duplicate');
        Route::get('/export', [TaskController::class, 'export'])->name('export');
    });

    /* -------------------------
     | [A3] CALENDAR
     ------------------------- */
    Route::prefix('calendar')->name('calendar.')->group(function () {
        Route::get('/', [CalendarController::class, 'index'])->name('index');
        Route::get('/events', [CalendarController::class, 'getEvents'])->name('events');
        Route::post('/events', [CalendarController::class, 'createEvent'])->name('events.create');
    });

    /* -------------------------
     | [A5] MESSAGES & ACTIVITY
     ------------------------- */
    Route::get('/messages', function () {
        return view('messages.index');
    })->name('messages.index');

    Route::get('/activity', function () {
        return view('activity.index');
    })->name('activity.index');

    Route::get('/settings', [ProfileController::class, 'edit'])->name('settings.index');

    /* -------------------------
     | [A4] NOTIFICATIONS
     ------------------------- */
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/count', [NotificationController::class, 'getUnreadCount'])->name('count');
        Route::get('/today', [NotificationController::class, 'getTodayTasks'])->name('today');
        Route::post('/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    Route::post('/files/upload', [FileController::class, 'upload'])->name('files.upload');
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');
});

/* =========================================================
 | [B] PROFILE MANAGEMENT (AUTH ONLY)
 ========================================================= */
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {

    // Profile views & basic updates
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('update');

    // Avatar management
    Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar.update');
    Route::delete('/avatar', [ProfileController::class, 'deleteAvatar'])->name('avatar.delete');

    // Password management
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');

    // Account deletion
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');

    // Additional profile features
    Route::get('/statistics', [ProfileController::class, 'statistics'])->name('statistics');
    Route::get('/export', [ProfileController::class, 'exportData'])->name('export');
});

/* =========================================================
 | [C] SUBSCRIPTION & PRICING (AUTH ONLY)
 ========================================================= */
Route::middleware('auth')->prefix('subscription')->name('subscription.')->group(function () {

    // View plans
    Route::get('/plans', [SubscriptionController::class, 'index'])->name('plans');

    // Update plan
    Route::post('/update', [SubscriptionController::class, 'updatePlan'])->name('update');

    // Checkout (Midtrans-ready)
    Route::post('/checkout', [SubscriptionController::class, 'checkout'])->name('checkout');

    // Payment page
    Route::get('/pay/{order}', [SubscriptionController::class, 'payment'])->name('payment');

    // Check feature access (AJAX)
    Route::get('/check-feature', [SubscriptionController::class, 'checkFeature'])->name('check');

    // Billing history (future)
    Route::get('/billing', [SubscriptionController::class, 'billing'])->name('billing');

    // Cancel subscription (future)
    Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
});

// Pricing alias route (for backward compatibility)
Route::middleware('auth')->get('/pricing', [SubscriptionController::class, 'index'])->name('pricing');

// Midtrans callback (no auth)
Route::post('/webhook/midtrans', [SubscriptionController::class, 'midtransCallback'])
    ->name('subscription.midtrans.callback');

/* =========================================================
 | [D] API ROUTES (AJAX ENDPOINTS)
 ========================================================= */
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {

    // Notifications API
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/count', [NotificationController::class, 'getUnreadCount'])->name('count');
        Route::get('/today', [NotificationController::class, 'getTodayTasks'])->name('today');
        Route::post('/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    });

    // Tasks API (for AJAX operations)
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/search', [TaskController::class, 'search'])->name('search');
        Route::get('/filter', [TaskController::class, 'filter'])->name('filter');
        Route::post('/{task}/quick-update', [TaskController::class, 'quickUpdate'])->name('quick-update');
    });

    // Dashboard API
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/stats', [TaskController::class, 'getDashboardStats'])->name('stats');
        Route::get('/recent-tasks', [TaskController::class, 'getRecentTasks'])->name('recent-tasks');
        Route::get('/productivity', [TaskController::class, 'getProductivityData'])->name('productivity');
    });
});

/* =========================================================
 | [E] AUTHENTICATION ROUTES (Laravel Breeze)
 ========================================================= */
require __DIR__ . '/auth.php';

/* =========================================================
 | [F] ADMIN ROUTES (FUTURE - OPTIONAL)
 ========================================================= */
// Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
//     Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
//     Route::resource('users', AdminUserController::class);
//     Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
// });

/* =========================================================
 | [G] FALLBACK & ERROR ROUTES
 ========================================================= */

// 404 - Custom not found page
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

/* =========================================================
 | [H] FEATURE-PROTECTED ROUTES (SUBSCRIPTION-BASED)
 ========================================================= */

// Voice Recorder (Pro+ only) - Example for future
// Route::middleware(['auth', 'subscription.feature:voice_recorder'])->group(function () {
//     Route::get('/voice-recorder', [VoiceRecorderController::class, 'index'])->name('voice.recorder');
//     Route::post('/voice-recorder/upload', [VoiceRecorderController::class, 'upload'])->name('voice.upload');
// });

// Team Collaboration (Pro+ only) - Example for future
// Route::middleware(['auth', 'subscription.feature:collaboration'])->prefix('team')->group(function () {
//     Route::get('/', [TeamController::class, 'index'])->name('team.index');
//     Route::post('/invite', [TeamController::class, 'invite'])->name('team.invite');
// });

/* =========================================================
 | [I] WEBHOOK ROUTES (PAYMENT GATEWAY - FUTURE)
 ========================================================= */

// Payment webhooks (no auth required)
// Route::post('/webhook/midtrans', [WebhookController::class, 'midtrans'])->name('webhook.midtrans');
// Route::post('/webhook/stripe', [WebhookController::class, 'stripe'])->name('webhook.stripe');

/* =========================================================
 | [J] MAINTENANCE & HEALTH CHECK
 ========================================================= */

// Health check endpoint (for monitoring)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toDateTimeString(),
        'app' => config('app.name'),
        'version' => '2.0.0', // Updated version
    ]);
})->name('health');

// Cache clear route (development only - remove in production)
if (app()->environment('local')) {
    Route::get('/clear-cache', function () {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');
        \Artisan::call('view:clear');

        return response()->json([
            'message' => 'Cache cleared successfully!',
            'cleared' => [
                'cache',
                'config',
                'routes',
                'views',
            ]
        ]);
    })->name('dev.clear-cache');
}

/* =========================================================
 | [K] DOCUMENTATION & HELP
 ========================================================= */

// Route::get('/help', [HelpController::class, 'index'])->name('help');
// Route::get('/faq', [HelpController::class, 'faq'])->name('faq');
// Route::get('/contact', [HelpController::class, 'contact'])->name('contact');
// Route::post('/contact', [HelpController::class, 'sendMessage'])->name('contact.send');

/* =========================================================
 | ROUTE SUMMARY
 |
 | Auth Routes:        auth.php
 | Dashboard:          GET  /dashboard
 | Tasks:              CRUD /tasks (+ toggle, duplicate, export)
 | Calendar:           GET  /calendar (+ events API)
 | Profile:            GET  /profile (+ avatar, password, stats, export)
 | Subscription:       GET  /subscription/plans
 | Notifications:      GET  /notifications (+ count, today, mark-read)
 | API Endpoints:      /api/* (search, filter, stats, productivity)
 |
 | Total Routes: ~45+ routes
 | New Dashboard API: /api/dashboard/stats, /api/dashboard/productivity
 | New Tasks API: /api/tasks/search (for search functionality)
 ========================================================= */
