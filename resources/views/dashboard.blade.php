@extends('layouts.app')

@section('title', 'Dashboard - ZENTASK')

@section('content')
<style>
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fadeInDown {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fadeInLeft {
    from {
      opacity: 0;
      transform: translateX(-20px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  .animate-fade-in-down {
    animation: fadeInDown 0.5s ease-out;
  }

  .animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out;
  }

  .animate-fade-in-left {
    animation: fadeInLeft 0.5s ease-out;
  }

  .delay-100 { animation-delay: 0.1s; animation-fill-mode: both; }
  .delay-200 { animation-delay: 0.2s; animation-fill-mode: both; }
  .delay-300 { animation-delay: 0.3s; animation-fill-mode: both; }
  .delay-400 { animation-delay: 0.4s; animation-fill-mode: both; }
  .delay-500 { animation-delay: 0.5s; animation-fill-mode: both; }

  .gradient-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
  }

  .shadow-elegant {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  }

  .shadow-glow {
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
  }

  .transition-smooth {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  }

  .hover-scale:hover {
    transform: scale(1.02);
  }

  .card-stat {
    background: white;
    border-radius: 12px;
    padding: 24px;
    border: 1px solid rgba(59, 130, 246, 0.2);
  }

  .card-accent {
    border-color: rgba(251, 146, 60, 0.2);
  }

  .card-success {
    border-color: rgba(34, 197, 94, 0.2);
  }

  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .backdrop-blur {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
  }

  .sticky-header {
    position: sticky;
    top: 0;
    z-index: 40;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(8px);
    border-bottom: 1px solid #e5e7eb;
  }
</style>

  <!-- Main Content -->
  <main style="max-width:1280px;margin:0 auto;padding:32px 24px">

    <!-- Stats Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:24px;margin-bottom:32px">

      <!-- Total Events Card -->
      <div class="card-stat shadow-elegant hover-lift transition-smooth animate-fade-in-up delay-100">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
          <h3 style="font-size:14px;font-weight:500;color:#6b7280;margin:0">Total Events</h3>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
        </div>
        <div style="font-size:36px;font-weight:700;color:#111827;margin:12px 0">
          {{ $totalEvents }}
        </div>
        <p style="font-size:12px;color:#6b7280;margin:8px 0 0">
          This month: <strong style="color:#111827">{{ $eventsThisMonth }}</strong>
        </p>
      </div>

      <!-- Upcoming Events Card -->
      <div class="card-stat card-accent shadow-elegant hover-lift transition-smooth animate-fade-in-up delay-200">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
          <h3 style="font-size:14px;font-weight:500;color:#6b7280;margin:0">Upcoming (7 days)</h3>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fb923c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <div style="font-size:36px;font-weight:700;color:#111827;margin:12px 0">
          {{ $upcoming->count() }}
        </div>
        <p style="font-size:12px;color:#6b7280;margin:8px 0 0">
          Events in the next week
        </p>
      </div>

      <!-- Quick Actions Card -->
      <div class="card-stat card-success shadow-elegant hover-lift transition-smooth animate-fade-in-up delay-300">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
          <h3 style="font-size:14px;font-weight:500;color:#6b7280;margin:0">Quick Actions</h3>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
            <polyline points="17 6 23 6 23 12"></polyline>
          </svg>
        </div>
        <div style="display:flex;gap:8px;margin-top:16px">
          <a href="{{ route('calendar.index') }}"
             class="transition-smooth"
             style="flex:1;padding:8px 16px;border:1px solid #e5e7eb;border-radius:8px;text-align:center;text-decoration:none;color:#374151;font-size:14px;font-weight:500;background:white">
            Calendar
          </a>
          <a href="#"
             onclick="location.reload();return false;"
             class="transition-smooth"
             style="flex:1;padding:8px 16px;border:1px solid #e5e7eb;border-radius:8px;text-align:center;text-decoration:none;color:#374151;font-size:14px;font-weight:500;background:white">
            Refresh
          </a>
        </div>
      </div>

    </div>

    <!-- Upcoming Events Section -->
    <div class="animate-fade-in-up delay-400" style="margin-bottom:32px">
      <div style="background:white;border-radius:12px;padding:24px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
          <div>
            <h2 style="font-size:20px;font-weight:700;margin:0 0 4px;color:#111827">Upcoming Events</h2>
            <p style="font-size:14px;color:#6b7280;margin:0">Next 7 days schedule</p>
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>

        @if($upcoming->isEmpty())
          <div style="text-align:center;padding:32px 0;color:#6b7280">
            No upcoming events in the next 7 days
          </div>
        @else
          <div style="display:flex;flex-direction:column;gap:16px">
            @foreach($upcoming->take(5) as $index => $ev)
              <div class="animate-fade-in-left transition-smooth hover-lift"
                   style="display:flex;align-items:start;gap:16px;padding:16px;border-radius:12px;border:1px solid #e5e7eb;cursor:pointer;animation-delay:{{ 0.5 + ($index * 0.05) }}s"
                   onclick="window.location='{{ route('calendar.index') }}'">

                <div style="width:4px;height:64px;border-radius:9999px;flex-shrink:0;background:{{ $ev->color ?? '#14b8a6' }}"></div>

                <div style="flex:1;min-width:0">
                  <h4 style="font-weight:600;color:#111827;margin:0 0 4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    {{ $ev->title }}
                  </h4>
                  <p style="font-size:14px;color:#6b7280;margin:4px 0">
                    {{ $ev->start_at->format('D, d M Y H:i') }}
                    @if($ev->end_at) - {{ $ev->end_at->format('H:i') }}@endif
                  </p>
                  @if($ev->description)
                    <p class="line-clamp-2" style="font-size:14px;color:#6b7280;margin:8px 0 0">
                      {{ $ev->description }}
                    </p>
                  @endif
                </div>

                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:4px">
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                  <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="animate-fade-in-up delay-500">
      <div style="background:white;border-radius:12px;padding:24px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
          <div>
            <h2 style="font-size:20px;font-weight:700;margin:0 0 4px;color:#111827">Activity</h2>
            <p style="font-size:14px;color:#6b7280;margin:0">Recent events and actions</p>
          </div>
          <p style="font-size:12px;color:#9ca3af">
            Updated: {{ now()->format('d M Y H:i') }}
          </p>
        </div>

        @if($upcoming->isEmpty())
          <div style="text-align:center;padding:32px 0;color:#6b7280">
            No recent activity
          </div>
        @else
          <div style="display:flex;flex-direction:column;gap:12px">
            @foreach($upcoming->take(6) as $index => $ev)
              <div class="animate-fade-in-left transition-smooth"
                   style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-radius:12px;background:rgba(243,244,246,0.5);cursor:pointer;animation-delay:{{ 0.6 + ($index * 0.05) }}s"
                   onmouseover="this.style.background='rgba(243,244,246,0.8)'"
                   onmouseout="this.style.background='rgba(243,244,246,0.5)'"
                   onclick="window.location='{{ route('calendar.index') }}'">

                <div style="flex:1;min-width:0">
                  <h4 style="font-weight:500;color:#111827;margin:0 0 4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    {{ $ev->title }}
                  </h4>
                  <p style="font-size:12px;color:#6b7280;margin:0">
                    {{ $ev->start_at->format('D, d M Y H:i') }}
                  </p>
                </div>

                <button style="padding:6px 12px;background:transparent;border:none;color:#3b82f6;font-size:12px;font-weight:500;cursor:pointer">
                  View
                </button>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

  </main>
</div>
@endsection
