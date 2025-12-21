@extends('layouts.dashboard-layout')

@section('content')
@php
    use Carbon\Carbon;

    $tz = config('app.timezone');

    $monthLabel = $monthStart->timezone($tz)->format('F Y');
    $startOfGrid = $monthStart->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
    $endOfGrid = $monthStart->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

    $days = [];
    $cursor = $startOfGrid->copy();
    while ($cursor->lte($endOfGrid)) {
        $days[] = $cursor->copy();
        $cursor->addDay();
    }

    $selected = Carbon::parse($selectedDate, $tz)->toDateString();
    $dayHeaders = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
@endphp

<div class="space-y-6">
    <div>
        <h1 class="text-3xl lg:text-4xl font-bold text-white">Calendar</h1>
        <p class="text-blue-100/70 mt-1">Kelola dan rencanakan task Anda berdasarkan tanggal.</p>
    </div>

    <div class="calendar-card max-w-5xl mx-auto">
        @if(empty($days))
            <div class="calendar-fallback">
                Calendar tidak dapat dimuat. Silakan refresh halaman.
            </div>
        @else
            <div class="calendar-header">
                <a href="{{ url('/calendar') }}?month={{ $prevMonth }}&date={{ $selectedDate }}"
                    class="calendar-nav">
                    Prev
                </a>

                <div class="calendar-title">
                    <div class="calendar-month">{{ $monthLabel }}</div>
                    <div class="calendar-subtitle">
                        Today: {{ now($tz)->format('d M Y') }}
                    </div>
                </div>

                <a href="{{ url('/calendar') }}?month={{ $nextMonth }}&date={{ $selectedDate }}"
                    class="calendar-nav">
                    Next
                </a>
            </div>

            <div class="calendar-grid-head">
                @foreach($dayHeaders as $h)
                    <div class="calendar-day-name">{{ $h }}</div>
                @endforeach
            </div>

            <div class="calendar-grid">
                @foreach($days as $d)
                    @php
                        $dateStr = $d->toDateString();
                        $inMonth = $d->month === $monthStart->month;
                        $isToday = $dateStr === now($tz)->toDateString();
                        $isSelected = $dateStr === $selected;
                        $count = $taskCountByDate[$dateStr] ?? 0;

                        $dayClass = 'calendar-day';
                        if (!$inMonth) {
                            $dayClass .= ' calendar-day--muted';
                        }
                        if ($isToday) {
                            $dayClass .= ' calendar-day--today';
                        }
                        if ($isSelected) {
                            $dayClass .= ' calendar-day--selected';
                        }
                    @endphp

                    <a href="{{ route('tasks.create', ['date' => $dateStr]) }}"
                        class="{{ $dayClass }}"
                        data-tooltip="Buat task di tanggal ini">
                        <span class="calendar-date">{{ $d->format('j') }}</span>
                        @if($count > 0)
                            <span class="calendar-badge">{{ $count }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

<style>
.calendar-card {
    background: rgba(15, 23, 42, 0.75);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 20px;
    backdrop-filter: blur(24px);
    padding: 24px;
    box-shadow: 0 18px 40px rgba(8, 15, 30, 0.55);
}

.calendar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
}

.calendar-title {
    text-align: center;
}

.calendar-month {
    font-size: 24px;
    font-weight: 700;
    color: #f8fafc;
}

.calendar-subtitle {
    font-size: 12px;
    color: rgba(226, 232, 240, 0.7);
    margin-top: 4px;
}

.calendar-nav {
    padding: 8px 16px;
    border-radius: 12px;
    border: 1px solid rgba(148, 163, 184, 0.2);
    background: rgba(15, 23, 42, 0.5);
    color: #cbd5f5;
    font-weight: 600;
    transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
}

.calendar-nav:hover {
    background: rgba(59, 130, 246, 0.15);
    border-color: rgba(59, 130, 246, 0.3);
    color: #e2e8f0;
}

.calendar-grid-head {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 8px;
    margin-bottom: 10px;
}

.calendar-day-name {
    text-align: center;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: rgba(226, 232, 240, 0.6);
    font-weight: 600;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 10px;
}

.calendar-day {
    position: relative;
    min-height: 88px;
    border-radius: 16px;
    border: 1px solid rgba(148, 163, 184, 0.18);
    background: rgba(15, 23, 42, 0.55);
    color: #e2e8f0;
    padding: 10px 12px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
}

.calendar-day:hover {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.6), rgba(59, 130, 246, 0.18));
    border-color: rgba(59, 130, 246, 0.35);
    box-shadow: 0 12px 28px rgba(30, 64, 175, 0.2);
}

.calendar-day:active {
    transform: scale(0.98);
}

.calendar-day--muted {
    opacity: 0.45;
}

.calendar-day--today {
    border-color: rgba(59, 130, 246, 0.5);
    box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.4);
}

.calendar-day--selected {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.25), rgba(14, 165, 233, 0.2));
    border-color: rgba(59, 130, 246, 0.55);
}

.calendar-date {
    font-weight: 700;
    font-size: 14px;
}

.calendar-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 999px;
    background: rgba(59, 130, 246, 0.2);
    border: 1px solid rgba(59, 130, 246, 0.35);
    color: #bfdbfe;
}

.calendar-day[data-tooltip]::after {
    content: attr(data-tooltip);
    position: absolute;
    left: 12px;
    bottom: 10px;
    font-size: 11px;
    color: rgba(226, 232, 240, 0.75);
    opacity: 0;
    transform: translateY(4px);
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.calendar-day:hover::after {
    opacity: 1;
    transform: translateY(0);
}

.calendar-fallback {
    text-align: center;
    color: rgba(226, 232, 240, 0.75);
    padding: 40px 16px;
}
</style>
