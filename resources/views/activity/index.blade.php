@extends('layouts.dashboard-layout')

@section('content')
@php
    $user = Auth::user();
    $rangeStart = now()->subMonths(5)->startOfMonth();

    $months = collect(range(0, 5))->map(function ($i) {
        return now()->subMonths(5 - $i)->startOfMonth();
    });

    $monthlyData = $months->map(function ($month) use ($user) {
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();
        $completedTasks = $user->tasks()
            ->where('status', 'selesai')
            ->whereBetween('updated_at', [$monthStart, $monthEnd])
            ->get();

        return [
            'label' => $monthStart->format('M'),
            'value' => $completedTasks->count(),
            'tasks' => $completedTasks->pluck('title')->filter()->values()->all(),
        ];
    });

    $completedCount = $user->tasks()
        ->where('status', 'selesai')
        ->where('updated_at', '>=', $rangeStart)
        ->count();

    $overdueCount = $user->tasks()
        ->where('status', 'berjalan')
        ->whereNotNull('due_date')
        ->where('due_date', '<', now())
        ->where('created_at', '>=', $rangeStart)
        ->count();

    $inProgressCount = $user->tasks()
        ->where('status', 'berjalan')
        ->where('created_at', '>=', $rangeStart)
        ->where(function ($query) {
            $query->whereNull('due_date')
                ->orWhere('due_date', '>=', now());
        })
        ->count();

    $totalCount = $completedCount + $inProgressCount + $overdueCount;
    $donutData = [
        'completed' => $completedCount,
        'in_progress' => $inProgressCount,
        'overdue' => $overdueCount,
        'total' => $totalCount,
    ];
@endphp

<div class="space-y-6">
    <div>
        <h1 class="text-3xl lg:text-4xl font-bold text-white">Activity</h1>
        <p class="text-blue-100/70 mt-1">
            Visualisasi aktivitas penyelesaian task Anda selama 6 bulan terakhir.
        </p>
    </div>

    @if($totalCount === 0)
        <div class="glass-card rounded-2xl p-6 text-center">
            <p class="text-blue-100/70">Belum ada aktivitas yang tercatat dalam 6 bulan terakhir.</p>
        </div>
    @endif

    <div class="space-y-6">
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">Monthly Completed Tasks</h2>
                    <p class="text-sm text-blue-100/60">Aktivitas selesai per bulan.</p>
                </div>
                <span class="text-xs px-3 py-1 rounded-full bg-blue-500/15 border border-blue-400/20 text-blue-100">
                    Last 6 months
                </span>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_260px] items-start">
                <div class="relative chart-shell">
                    <div class="chart-grid"></div>
                    <svg id="activityBarChart" viewBox="0 0 600 240" class="w-full h-60"></svg>
                </div>

                <div id="barDetails" class="details-panel">
                    <div class="details-header">
                        <div>
                            <div class="details-label">Detail Tasks</div>
                            <div class="details-subtitle">Aktivitas selesai per bulan</div>
                        </div>
                        <span class="details-pill">6 months</span>
                    </div>
                    <div class="details-body">
                        <div class="details-title">Pilih bulan</div>
                        <div class="details-count text-blue-100/70">Arahkan kursor ke grafik.</div>
                        <div class="details-list"></div>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex justify-between text-xs text-blue-100/60">
                @foreach($monthlyData as $item)
                    <span>{{ $item['label'] }}</span>
                @endforeach
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 relative">
            <div>
                <h2 class="text-lg font-semibold text-white">Task Distribution</h2>
                <p class="text-sm text-blue-100/60">Ringkasan status 6 bulan.</p>
            </div>

            <div class="relative mt-6 flex items-center justify-center">
                <svg id="donutChart" viewBox="0 0 220 220" class="w-52 h-52"></svg>
                <div class="absolute text-center">
                    <div class="text-2xl font-bold text-white" id="donutTotal">{{ $totalCount }}</div>
                    <div class="text-xs text-blue-100/60">Total task</div>
                </div>
                <div id="donutTooltip" class="chart-tooltip" style="display: none;"></div>
            </div>

            <div class="mt-6 space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
                        <span class="text-blue-100/70">Completed</span>
                    </div>
                    <span class="text-white">{{ $completedCount }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span>
                        <span class="text-blue-100/70">In Progress</span>
                    </div>
                    <span class="text-white">{{ $inProgressCount }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                        <span class="text-blue-100/70">Overdue</span>
                    </div>
                    <span class="text-white">{{ $overdueCount }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const monthlyData = @json($monthlyData);
const donutData = @json($donutData);

(function renderBarChart() {
    const svg = document.getElementById('activityBarChart');
    const details = document.getElementById('barDetails');
    if (!svg || !details) return;

    const width = 600;
    const height = 240;
    const padding = { top: 20, right: 20, bottom: 30, left: 20 };
    const values = monthlyData.map(item => item.value);
    const maxValue = Math.max(...values, 1);
    const chartWidth = width - padding.left - padding.right;
    const chartHeight = height - padding.top - padding.bottom;
    const stepX = chartWidth / (monthlyData.length || 1);
    const barWidth = Math.min(48, stepX * 0.55);

    const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
    defs.innerHTML = `
        <linearGradient id="barGradient" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#38bdf8" />
            <stop offset="100%" stop-color="#22d3ee" />
        </linearGradient>
    `;

    svg.innerHTML = '';
    svg.appendChild(defs);

    monthlyData.forEach((item, index) => {
        const xCenter = padding.left + stepX * index + stepX / 2;
        const barHeight = (item.value / maxValue) * chartHeight;
        const x = xCenter - barWidth / 2;
        const y = padding.top + (chartHeight - barHeight);

        const bar = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        bar.setAttribute('x', x);
        bar.setAttribute('y', y);
        bar.setAttribute('width', barWidth);
        bar.setAttribute('height', Math.max(barHeight, 4));
        bar.setAttribute('rx', 8);
        bar.setAttribute('class', 'bar-rect');
        bar.setAttribute('fill', 'url(#barGradient)');
        svg.appendChild(bar);

        const hit = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        hit.setAttribute('x', x);
        hit.setAttribute('y', padding.top);
        hit.setAttribute('width', barWidth);
        hit.setAttribute('height', chartHeight);
        hit.setAttribute('fill', 'transparent');
        hit.dataset.index = index;
        svg.appendChild(hit);

        const renderDetails = (data) => {
            const tasks = data.tasks || [];
            const taskItems = (tasks.length ? tasks : ['No completed tasks']).map((task, idx) => {
                const delay = idx * 60;
                return `
                    <div class="details-item" style="animation-delay:${delay}ms;">
                        <span class="tooltip-check"></span>
                        <span>${task}</span>
                    </div>
                `;
            }).join('');

            details.innerHTML = `
                <div class="details-header">
                    <div>
                        <div class="details-label">Detail Tasks</div>
                        <div class="details-subtitle">${data.label} ${new Date().getFullYear()}</div>
                    </div>
                    <span class="details-pill">${data.value} selesai</span>
                </div>
                <div class="details-body">
                    <div class="details-title">Task selesai</div>
                    <div class="details-count">${data.value} task</div>
                    <div class="details-list">${taskItems}</div>
                </div>
            `;
        };

        const showDetails = () => {
            renderDetails(item);
            bar.classList.add('bar-active');
        };

        const hideDetails = () => {
            bar.classList.remove('bar-active');
        };

        hit.addEventListener('mouseenter', showDetails);
        hit.addEventListener('mousemove', showDetails);
        hit.addEventListener('mouseleave', hideDetails);
    });

    const fallback = monthlyData[monthlyData.length - 1];
    if (fallback) {
        const tasks = fallback.tasks || [];
        const taskItems = (tasks.length ? tasks : ['No completed tasks']).map((task, idx) => {
            const delay = idx * 60;
            return `
                <div class="details-item" style="animation-delay:${delay}ms;">
                    <span class="tooltip-check"></span>
                    <span>${task}</span>
                </div>
            `;
        }).join('');

        details.innerHTML = `
            <div class="details-header">
                <div>
                    <div class="details-label">Detail Tasks</div>
                    <div class="details-subtitle">${fallback.label} ${new Date().getFullYear()}</div>
                </div>
                <span class="details-pill">${fallback.value} selesai</span>
            </div>
            <div class="details-body">
                <div class="details-title">Task selesai</div>
                <div class="details-count">${fallback.value} task</div>
                <div class="details-list">${taskItems}</div>
            </div>
        `;
    }
})();

(function renderDonutChart() {
    const svg = document.getElementById('donutChart');
    const tooltip = document.getElementById('donutTooltip');
    if (!svg || !tooltip) return;

    const size = 220;
    const center = size / 2;
    const radius = 70;
    const circumference = 2 * Math.PI * radius;
    const total = Math.max(donutData.total, 1);

    const segments = [
        { key: 'completed', label: 'Completed', value: donutData.completed, color: '#4ade80' },
        { key: 'in_progress', label: 'In Progress', value: donutData.in_progress, color: '#a855f7' },
        { key: 'overdue', label: 'Overdue', value: donutData.overdue, color: '#f87171' },
    ];

    svg.innerHTML = '';

    let offset = 0;
    const minPercent = 0.06;
    const totals = segments.map(segment => segment.value);
    const hasData = totals.some(value => value > 0);
    const normalizedSegments = segments.map(segment => {
        if (!hasData) return { ...segment, renderValue: 1 };
        if (segment.value === 0) return { ...segment, renderValue: 0 };
        const rawPercent = segment.value / total;
        return {
            ...segment,
            renderValue: rawPercent < minPercent ? total * minPercent : segment.value,
        };
    });

    normalizedSegments.forEach((segment) => {
        const value = segment.value;
        const renderValue = segment.renderValue;
        const renderTotal = Math.max(
            normalizedSegments.reduce((sum, item) => sum + item.renderValue, 0),
            1
        );
        const length = (renderValue / renderTotal) * circumference;
        const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circle.setAttribute('cx', center);
        circle.setAttribute('cy', center);
        circle.setAttribute('r', radius);
        circle.setAttribute('fill', 'transparent');
        circle.setAttribute('stroke', segment.color);
        circle.setAttribute('stroke-width', 18);
        circle.setAttribute('stroke-dasharray', `${length} ${circumference - length}`);
        circle.setAttribute('stroke-dashoffset', offset);
        circle.setAttribute('stroke-linecap', 'round');
        circle.setAttribute('class', 'donut-segment');
        circle.dataset.label = segment.label;
        circle.dataset.value = value;
        circle.dataset.percent = total === 0 ? 0 : Math.round((value / total) * 100);
        circle.style.transform = 'rotate(-90deg)';
        circle.style.transformOrigin = `${center}px ${center}px`;
        svg.appendChild(circle);

        offset -= length;

        circle.addEventListener('mouseenter', (event) => {
            const rect = svg.getBoundingClientRect();
            const x = event.clientX - rect.left + 12;
            const y = event.clientY - rect.top - 12;
            tooltip.innerHTML = `
                <div class="tooltip-title">${segment.label}</div>
                <div class="tooltip-count">${value} task (${circle.dataset.percent}%)</div>
            `;
            tooltip.style.display = 'block';
            tooltip.style.transform = `translate(${x}px, ${y}px)`;
        });
        circle.addEventListener('mouseleave', () => {
            tooltip.style.display = 'none';
        });
    });
})();
</script>

<style>
.chart-grid {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(to right, rgba(148, 163, 184, 0.06) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(148, 163, 184, 0.06) 1px, transparent 1px);
    background-size: 36px 36px;
    border-radius: 16px;
    pointer-events: none;
}

.chart-shell::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 16px;
    background: radial-gradient(circle at 20% 20%, rgba(56, 189, 248, 0.12), transparent 55%),
        radial-gradient(circle at 80% 10%, rgba(125, 211, 252, 0.08), transparent 50%);
    pointer-events: none;
}

.bar-rect {
    filter: drop-shadow(0 0 10px rgba(56, 189, 248, 0.35));
    transition: transform 0.2s ease, filter 0.2s ease;
}

.bar-rect.bar-active {
    transform: translateY(-8px);
    filter: drop-shadow(0 0 16px rgba(56, 189, 248, 0.6));
}

.chart-tooltip {
    position: absolute;
    padding: 12px 14px;
    background: rgba(15, 23, 42, 0.9);
    border: 1px solid rgba(148, 163, 184, 0.3);
    border-radius: 14px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.45);
    color: #f8fafc;
    font-size: 12px;
    z-index: 10;
    pointer-events: none;
    transform: translate(0, 0);
    backdrop-filter: blur(12px);
}

.tooltip-title,
.details-title {
    font-weight: 600;
    margin-bottom: 4px;
}

.tooltip-count,
.details-count {
    color: rgba(226, 232, 240, 0.8);
    margin-bottom: 6px;
}

.tooltip-list,
.details-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.details-list {
    max-height: 96px;
    overflow-y: auto;
    padding-right: 6px;
}

.details-item {
    display: flex;
    align-items: center;
    gap: 6px;
    opacity: 0;
    transform: translateY(6px);
    animation: tooltipIn 0.25s ease forwards;
}

.tooltip-check {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #4ade80;
    box-shadow: 0 0 8px rgba(74, 222, 128, 0.5);
    flex-shrink: 0;
}

.tooltip-more {
    margin-top: 4px;
    color: rgba(226, 232, 240, 0.7);
}

.details-panel {
    border-radius: 16px;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.95), rgba(15, 23, 42, 0.7));
    border: 1px solid rgba(148, 163, 184, 0.3);
    padding: 16px;
    min-height: 200px;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.03), 0 18px 40px rgba(8, 15, 30, 0.5);
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.details-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.details-label {
    font-size: 12px;
    color: rgba(226, 232, 240, 0.7);
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.details-subtitle {
    margin-top: 4px;
    font-size: 13px;
    color: #f8fafc;
    font-weight: 600;
}

.details-pill {
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 999px;
    background: rgba(56, 189, 248, 0.15);
    color: #bae6fd;
    border: 1px solid rgba(56, 189, 248, 0.3);
    white-space: nowrap;
}

.details-body {
    border-top: 1px solid rgba(148, 163, 184, 0.2);
    padding-top: 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    color: #f8fafc;
}

.details-list::-webkit-scrollbar {
    width: 6px;
}

.details-list::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.35);
    border-radius: 999px;
}

.donut-segment {
    transition: transform 0.2s ease, stroke-width 0.2s ease;
}

.donut-segment:hover {
    stroke-width: 22;
}

@keyframes tooltipIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection
