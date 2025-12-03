<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DashboardController; // pastikan ini ada di atas file

// ... route existing yang kamu punya

// Tambah ini:
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/', function () {
    return redirect('/calendar');
});

// Calendar page
Route::get('/calendar', [EventController::class, 'index'])->name('calendar.index');

// Events CRUD
Route::get('/events', [EventController::class, 'events'])->name('events.list');
Route::post('/events', [EventController::class, 'store'])->name('events.store');
Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

// Import/Export & Transcribe endpoints
Route::post('/events/import', [EventController::class, 'import'])->name('events.import');
Route::get('/events/export.ics', [EventController::class, 'exportIcs'])->name('events.export');

Route::post('/transcribe', [EventController::class, 'transcribe'])->name('events.transcribe');
Route::get('/events/{event}/transcript', [EventController::class, 'transcriptStatus'])->name('events.transcript.status');
Route::post('/realtime-transcript', [EventController::class, 'realtimeTranscript'])->name('events.realtime.transcript');
