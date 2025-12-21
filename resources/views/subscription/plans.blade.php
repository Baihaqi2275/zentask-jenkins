@extends('layouts.dashboard-layout')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl lg:text-4xl font-bold text-white">Upgrade Plan</h1>
            <p class="text-blue-100/70 mt-1">
                Pilih paket yang paling sesuai dengan workflow Anda.
            </p>
            <p class="text-blue-100/60 text-sm mt-1">Anda dapat mengganti paket kapan saja.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="glass-card rounded-2xl p-4 border border-green-500/20 text-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="glass-card rounded-2xl p-4 border border-red-500/20 text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $key => $plan)
            @php
                $isCurrent = ($user->subscription_plan === $key);
                $price = $plan['price'] ?? 0;
                $cardBase = 'glass-card rounded-2xl p-6 border transition';
                $accent = match($key) {
                    'free' => 'border-white/10',
                    'pro' => 'border-purple-500/40 shadow-[0_0_30px_rgba(168,85,247,0.25)]',
                    'plus' => 'border-blue-500/30',
                    default => 'border-white/10',
                };
                $currentGlow = $isCurrent ? 'ring-2 ring-purple-500/40' : '';
                $badgeStyle = match($key) {
                    'free' => 'bg-white/10 text-white/80 border border-white/20',
                    'pro' => 'bg-purple-500/20 text-purple-100 border border-purple-400/30',
                    'plus' => 'bg-blue-500/20 text-blue-100 border border-blue-400/30',
                    default => 'bg-white/10 text-white/80 border border-white/20',
                };
                $ctaLabel = match($key) {
                    'pro' => 'Upgrade ke Pro',
                    'plus' => 'Upgrade ke Plus',
                    default => 'Pilih Free',
                };
                $priceClass = match($key) {
                    'pro' => 'text-purple-200',
                    'plus' => 'text-blue-200',
                    default => 'text-white',
                };
                $formAction = in_array($key, ['pro', 'plus'], true)
                    ? route('subscription.checkout')
                    : route('subscription.update');
            @endphp

            <div class="{{ $cardBase }} {{ $accent }} {{ $currentGlow }}">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $badgeStyle }}">
                            {{ strtoupper($plan['name']) }}
                        </span>
                        @if($isCurrent)
                            <span class="ml-2 text-xs font-semibold px-2.5 py-1 rounded-full bg-purple-500/20 text-purple-100 border border-purple-400/30">
                                Current Plan
                            </span>
                        @endif
                        <h2 class="text-xl font-bold text-white mt-3">{{ $plan['name'] }}</h2>
                        <div class="mt-3 text-2xl font-bold {{ $priceClass }}">
                            Rp {{ number_format($price, 0, ',', '.') }}
                            @if($price > 0)
                                <span class="text-sm text-blue-100/60 font-semibold">/ bulan</span>
                            @endif
                        </div>
                        <p class="text-sm mt-2 {{ $isCurrent ? 'text-purple-200' : 'text-blue-100/60' }}">
                            {{ $isCurrent ? 'Current plan' : 'Upgrade anytime' }}
                        </p>
                    </div>
                </div>

                <p class="text-sm text-blue-100/70 mt-4">
                    {{ $plan['description'] ?? 'Plan designed to boost your productivity.' }}
                </p>

                <div class="mt-5">
                    <ul class="space-y-2 text-sm text-blue-100/80">
                        @foreach($plan['features'] as $f)
                            <li class="flex items-start gap-2">
                                <span class="mt-1 text-blue-300">-</span>
                                <span>{{ $f }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-6">
                    <form method="POST" action="{{ $formAction }}">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $key }}">

                        <button type="submit"
                            class="w-full rounded-xl px-4 py-2.5 font-semibold transition
                                   {{ $isCurrent
                                        ? 'bg-white/10 text-white/50 cursor-not-allowed'
                                        : 'bg-blue-600 hover:bg-blue-700 text-white' }}"
                            {{ $isCurrent ? 'disabled' : '' }}>
                            {{ $isCurrent ? 'Current Plan' : $ctaLabel }}
                        </button>
                    </form>

                    <p class="text-xs text-blue-100/60 mt-3">
                        Anda dapat mengganti paket kapan saja.
                    </p>

                    @if($isCurrent && $user->plan_started_at)
                        <p class="text-xs text-blue-100/40 mt-2">
                            Active since: {{ \Carbon\Carbon::parse($user->plan_started_at)->format('M d, Y') }}
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-sm text-blue-100/60">
        <p>
            Voice recorder tersedia untuk <span class="text-white font-semibold">Pro</span> dan
            <span class="text-white font-semibold">Plus</span>.
        </p>
        <p class="mt-1">
            Batas kolaborasi mengikuti paket yang Anda pilih.
        </p>
        <p class="mt-2 text-blue-100/50">
            Pembayaran aman melalui Midtrans.
        </p>
    </div>
</div>
@endsection
