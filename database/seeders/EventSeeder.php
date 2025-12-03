<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run()
    {
        Event::truncate();

        Event::create([
            'title' => 'Meeting with client',
            'description' => 'Discuss project scope',
            'start_at' => Carbon::now()->addDays(1)->setTime(9,30),
            'end_at' => Carbon::now()->addDays(1)->setTime(10,30),
            'color' => '#10b981',
        ]);

        Event::create([
            'title' => 'Design review',
            'description' => 'Review landing page design',
            'start_at' => Carbon::now()->addDays(2)->setTime(14,0),
            'end_at' => Carbon::now()->addDays(2)->setTime(15,0),
            'color' => '#3b82f6',
        ]);

        Event::create([
            'title' => 'Reminder: Submit report',
            'description' => 'Deadline for weekly report',
            'start_at' => Carbon::now()->addDays(3)->setTime(17,0),
            'end_at' => Carbon::now()->addDays(3)->setTime(17,30),
            'color' => '#f97316',
        ]);
    }
}
