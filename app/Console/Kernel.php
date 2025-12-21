<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // ✅ Run every day at 07:00 WIB
        $schedule->command('notifications:send-daily')
                 ->dailyAt('07:00')
                 ->timezone('Asia/Jakarta')
                 ->onSuccess(function () {
                     \Log::info('Daily notifications sent successfully');
                 })
                 ->onFailure(function () {
                     \Log::error('Daily notifications failed');
                 });
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
