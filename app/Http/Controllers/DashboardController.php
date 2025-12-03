<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEvents = Event::count();

        $now = Carbon::now();
        $weekLater = $now->copy()->addDays(7);
        $upcoming = Event::whereBetween('start_at', [$now, $weekLater])
            ->orderBy('start_at')
            ->take(6)
            ->get();

        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();
        $eventsThisMonth = Event::whereBetween('start_at', [$monthStart, $monthEnd])->count();

        $totalUsers = class_exists(\App\Models\User::class) ? User::count() : 0;

        return view('dashboard', compact(
            'totalEvents',
            'upcoming',
            'eventsThisMonth',
            'totalUsers'
        ));
    }
}
