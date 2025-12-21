@extends('layouts.dashboard-layout')

@section('content')
@php
    $price = $intent->price ?? 0;
@endphp

<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h1 class="text-3xl lg:text-4xl font-bold text-white">Checkout</h1>
        <p class="text-blue-100/70 mt-1">Selesaikan pembayaran untuk mengaktifkan plan Anda.</p>
    </div>

    <div class="glass-card rounded-2xl p-6 border border-white/10">
        @if($intent->status === 'success')
            <div class="mb-4 rounded-xl border border-green-400/30 bg-green-400/10 px-4 py-3 text-sm text-green-100">
                Plan berhasil di-upgrade.
            </div>
        @elseif($intent->status === 'failed')
            <div class="mb-4 rounded-xl border border-red-400/30 bg-red-400/10 px-4 py-3 text-sm text-red-100">
                Pembayaran gagal. Silakan coba lagi.
            </div>
        @endif
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-blue-100/60">Order ID</div>
                <div class="text-white font-semibold">{{ $intent->order_id }}</div>
            </div>
            <span class="text-xs px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-100">
                {{ strtoupper($intent->plan) }}
            </span>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                <div class="text-sm text-blue-100/60">Total</div>
                <div class="text-2xl font-bold text-white">
                    Rp {{ number_format($price, 0, ',', '.') }}
                    <span class="text-sm text-blue-100/60 font-semibold">/ bulan</span>
                </div>
            </div>
            <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                <div class="text-sm text-blue-100/60">Status</div>
                <div class="text-white font-semibold capitalize">{{ $intent->status }}</div>
            </div>
        </div>

        <div class="mt-6">
            <button id="payButton"
                class="w-full rounded-xl px-4 py-3 font-semibold transition bg-blue-600 hover:bg-blue-700 text-white">
                Bayar dengan Midtrans
            </button>
            <p class="text-xs text-blue-100/60 mt-3">
                Pembayaran aman melalui Midtrans.
            </p>
        </div>
    </div>
</div>

    @if(config('services.midtrans.client_key'))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @endif

    <script>
        (function () {
            const payButton = document.getElementById('payButton');
            const snapToken = @json($intent->payment_token);
            if (!payButton) return;

            if (!snapToken || typeof window.snap === 'undefined') {
                payButton.addEventListener('click', () => {
                    alert('Integrasi Midtrans siap dihubungkan. Payment token belum tersedia.');
                });
                return;
            }

        payButton.addEventListener('click', () => {
            window.snap.pay(snapToken, {
                onSuccess: function () {
                    window.location.reload();
                },
                onPending: function () {
                    window.location.reload();
                },
                onError: function () {
                    window.location.reload();
                }
            });
        });
    })();
</script>
@endsection
